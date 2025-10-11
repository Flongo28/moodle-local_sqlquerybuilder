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

use local_sqlquerybuilder\contracts\i_expression;
use local_sqlquerybuilder\contracts\i_query;
use local_sqlquerybuilder\query\joins\join_expression;
use local_sqlquerybuilder\query\joins\join_types;

/**
 * Trait that builds a sql statement, that can be exported via
 * build_join()
 *
 * @package local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class joinpart implements i_expression {
    /** @var join_expression[] All join expressions for the request */
    protected array $joins = [];

    private function parse_condition(array $condition): condition {
        $parsedcondition = new condition();

        // Handle single condition.
        if (count($condition) == 3) {
            $parsedcondition->where_column($condition[0], $condition[1], $condition[2]);
            return $parsedcondition;
        }

        throw new ValueError("Condition should have length of 3: " . var_export($condition, true));
    }

    public function join(string|i_query $table, array $condition, string $alias = '') {
        $this->joins[] = [$table, $this->parse_condition($condition), join_types::INNER, $alias];
    }

    public function left_join(string|i_query $table, array $condition, string $alias = '') {
        $this->joins[] = [$table, $this->parse_condition($condition), join_types::LEFT, $alias];
    }

    public function right_join(string|i_query $table, array $condition, string $alias = '') {
        $this->joins[] = [$table, $this->parse_condition($condition), join_types::RIGHT, $alias];
    }

    public function full_join(string $table, array $condition, string $alias = '') {
        $this->joins[] = [$table, $this->parse_condition($condition), join_types::FULL, $alias];
    }

    public function crossjoin(string $table, array $condition, string $alias = '') {
        $this->joins[] = [$table, $this->parse_condition($condition), join_types::CROSS, $alias];
    }

    public function get_sql(): string {
        if (empty($this->joins)) {
            return '';
        }
        $joinclause = '';

        foreach ($this->joins as $join) {
            $table = $join[0];
            $condition = $join[1];
            $jointype = $join[2];
            $alias = $join[3];

            // Build the table/subquery part.
            if ($table instanceof i_query) {
                $joinclause .= $jointype->value . ' JOIN (' . $table->get_sql() . ') ' . $alias . ' ON ';
            } else {
                $joinclause .= $jointype->value . ' JOIN {' . $table . '} ' . $alias . ' ON ';
            }

            // Build the conditions part with proper AND/OR logic.
            $joinclause .= $condition->get_sql();
        }

        return preg_replace('/\s{2,}/', ' ', trim($joinclause));
    }

    public function get_params(): array {
        return [];
    }
}
