<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_sqlquerybuilder\query;

use core\clock;
use core\di;
use local_sqlquerybuilder\contracts\i_query;
use local_sqlquerybuilder\contracts\i_expression;
use local_sqlquerybuilder\query\where\like_options;
use local_sqlquerybuilder\query\where\where_column_comparison;
use local_sqlquerybuilder\query\where\where_expression;
use local_sqlquerybuilder\query\where\where_comparison;
use local_sqlquerybuilder\query\where\or_where_group;
use local_sqlquerybuilder\query\where\where_fulltext;
use local_sqlquerybuilder\query\where\where_is_null;
use local_sqlquerybuilder\query\where\where_in;
use local_sqlquerybuilder\query\where\where_like;

/**
 * Builds an where expression without WHERE
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025 Konrad Ebel <despair2400@proton.me>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition implements i_expression {
    protected array $conditionparts = [];

    public function where(string $column, string $operator, mixed $value, bool $negate = false): void {
        if ($operator == 'like') {
            $this->conditionparts[] = new where_like($column, $value, $negate);
        } else {
            $this->conditionparts[] = new where_comparison($column, $operator, $value, $negate);
        }
    }

    public function where_column(string $column, string $operator, string $othercolumn, bool $negate = false): void {
        $this->conditionparts[] = new where_column_comparison($column, $operator, $othercolumn, $negate);
    }

    public function or_where(string $column, string $operator, mixed $value, bool $negate = false): void {
        $this->where($column, $operator, $value, $negate);
        $this->combine_last_two_by_or();
    }

    public function where_not(string $column, string $operator, mixed $value): void {
        $this->where($column, $operator, $value, true);
    }

    public function or_where_not(string $column, string $operator, mixed $value): void {
        $this->or_where($column, $operator, $value, true);
    }

    public function where_fulltext(string $column, string $value, bool $negate = false): void {
        $this->conditionparts[] = new where_fulltext($column, $value, $negate);
    }

    public function where_fulltext_not(string $column, string $value): void {
        $this->conditionparts[] = new where_fulltext($column, $value, true);
    }

    public function where_like(string $column, string $value, ?like_options $options = null, bool $negate = false): void {
        $this->conditionparts[] = new where_like($column, $value, $negate, $options);
    }

    public function where_not_like(string $column, string $value, ?like_options $options = null): void {
        $this->where_like($column, $value, $options, true);
    }

    public function where_null(string $column): void {
        $this->conditionparts[] = new where_is_null($column);
    }

    public function or_where_null(string $column): void {
        $this->where_null($column);
        $this->combine_last_two_by_or();
    }

    public function where_notnull(string $column): void {
        $this->conditionparts[] = new where_is_null($column, true);
    }

    public function or_where_notnull(string $column): void {
        $this->where_notnull($column);
        $this->combine_last_two_by_or();
    }

    public function where_in(string $column, array|i_query $values, bool $negate = false): void {
        $this->conditionparts[] = new where_in($column, $values, $negate);
    }

    public function where_not_in(string $column, array|i_query $values): void {
        $this->where_in($column, $values, true);
    }

    private function combine_last_two_by_or(): void {
        if (count($this->conditionparts) < 2) {
            return;
        }

        $aclause = array_pop($this->conditionparts);
        $bclause = array_pop($this->conditionparts);
        $orclause = null;

        if ($aclause instanceof or_where_group) {
            $aclause->add_clauses($bclause);
            $orclause = $aclause;
        } else {
            $orclause = new or_where_group(
                $aclause,
                $bclause,
            );
        }

        $this->conditionparts[] = $orclause;
    }

    public function where_currently_active(string $columntimestart, string $columntimeend): void {
        $currenttime = di::get(clock::class)->time();

        $this->where_null($columntimestart);
        $this->or_where($columntimestart, '<=', $currenttime);
        $this->where_null($columntimeend);
        $this->or_where($columntimeend, '>=', $currenttime);
    }

    public function get_sql(): string {
        if (empty($this->conditionparts)) {
            return '';
        }

        $sqlclause = implode(' AND ', $this->conditionparts);
        return $sqlclause;
    }

    public function get_params(): array {
        $params = array_map(fn (where_expression $expression) => $expression->get_params(), $this->conditionparts);
        return array_merge(...$params);
    }
}
