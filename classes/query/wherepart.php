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

/**
 * Where trait for SQL query building.
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sqlquerybuilder\query;

use core\clock;
use core\di;
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
 * Trait for handling WHERE conditions in SQL queries.
 *
 * This trait provides methods for building WHERE clauses with AND and OR conditions.
 */
class wherepart implements expression {

    /** @var where_expression[] All where expressions */
    protected array $whereconditions = [];

    /**
     * Add a WHERE condition with AND logic.
     *
     * @param string $column The column name
     * @param string $operator The comparison operator (=, !=, >, <, >=, <=, LIKE, etc.)
     * @param mixed $value The value to compare against
     */
    public function where(string $column, string $operator, mixed $value, bool $negate = false) {
        if ($operator == 'like') {
            $this->whereconditions[] = new where_like($column, $value, $negate);    
        }

        $this->whereconditions[] = new where_comparison($column, $operator, $value, $negate);
    }

    /**
     * Add a WHERE condition with AND logic.
     *
     * @param string $column The column name
     * @param string $operator The comparison operator (=, !=, >, <, >=, <=, etc.)
     * @param mixed $othercolumn The column to compare against
     */
    public function where_column(string $column, $operator, $othercolumn, bool $negate = false) {
        $this->whereconditions[] = new where_column_comparison($column, $operator, $othercolumn, $negate);
    }

    /**
     * Add a WHERE condition with OR logic.
     *
     * @param string $column The column name
     * @param string $operator The comparison operator (=, !=, >, <, >=, <=, LIKE, etc.)
     * @param mixed $value The value to compare against
     */
    public function or_where(string $column, string $operator, $value, bool $negate = false) {
        $this->where($column, $operator, $value, $negate);
        $this->combine_last_two_by_or();
    }

    // Todo column koennte auch ein Array sein -> where([['status', '=', '1'],['subscribed', '<>', '1'] ,
    // dann gibt es keinen direkt operator/value.
    /**
     * Add a WHERE  not condition with AND logic.
     *
     * @param string $column The column name
     * @param string $operator The comparison operator (=, !=, >, <, >=, <=, LIKE, etc.)
     * @param mixed $value The value to compare against
     */
    public function where_not(string $column, string $operator, $value) {
        $this->where($column, $operator, $value, true);
    }

    /**
     * Add a WHERE NOT condition with OR logic.
     *
     * @param string $column The column name
     * @param string $operator The comparison operator (=, !=, >, <, >=, <=, LIKE, etc.)
     * @param mixed $value The value to compare against
     */
    public function or_where_not(string $column, string $operator, $value) {
        $this->or_where($column, $operator, $value, true);
    }

    public function where_fulltext(string $column, string $value, bool $negate = false) {
        $this->whereconditions[] = new where_fulltext($column, $value, $negate);
    }

    public function where_fulltext_not(string $column, string $value) {
        $this->whereconditions[] = new where_fulltext($column, $value, true);
    }

    public function where_like(string $column, string $value, like_options $options = null, bool $negate = false) {
        $this->whereconditions[] = new where_like($column, $value, $negate, $options);
    }

    public function where_not_like(string $column, string $value, like_options $options = null) {
        $this->where_like($column, $value, $options, true);
    }

    /**
     * Add a WHERE NULL condition with AND logic.
     *
     * @param string $column The column name
     */
    public function where_null(string $column) {
        $this->whereconditions[] = new where_is_null($column);
    }

    /**
     * Add a WHERE NULL condition with OR logic.
     *
     * @param string $column The column name
     */
    public function or_where_null(string $column) {
        $this->where_null($column);
        $this->combine_last_two_by_or();
    }

    /**
     * Add a WHERE NOT NULL condition with AND logic.
     *
     * @param string $column The column name
     */
    public function where_notnull(string $column) {
        $this->whereconditions[] = new where_is_null($column, true);
    }

    /**
     * Add a WHERE NOT NULL condition with OR logic.
     *
     * @param string $column The column name
     */
    public function or_where_notnull(string $column) {
        $this->where_notnull($column);
        $this->combine_last_two_by_or();
    }

    public function where_in(string $column, array $values, bool $negate = false) {
        $this->whereconditions[] = new where_in($column, $values, $negate);
    }

    public function where_not_in(string $column, array $values) {
        $this->where_in($column, $values, true);
    }

    private function combine_last_two_by_or(): void {
        if (count($this->whereconditions) < 2) {
            return;
        }

        $aclause = array_pop($this->whereconditions);
        $bclause = array_pop($this->whereconditions);
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

        $this->whereconditions[] = $orclause;
    }

    /**
     * Checks if the given time is between the two columns
     * If any of these columns are 0, they will not be checked
     *
     * @param string $columntimestart Column with start time
     * @param string $columntimeend Column with end time
     * @param int|null $timebetween Timestamp which will be checked to be between the start and end
     *                              If null checks for the current time
     */
    public function time_between(string $columntimestart, string $columntimeend, ?int $timebetween = null): void {
        if (is_null($timebetween)) {
            $timebetween = di::get(clock::class)->time();
        }

        $this->where($columntimestart, '=', 0);
        $this->or_where($columntimestart, '<=', $timebetween);
        $this->where($columntimeend, '=', 0);
        $this->or_where($columntimeend, '>=', $timebetween);
    }

    /**
     * Export the WHERE clause as a SQL string.
     *
     * @return string The complete WHERE clause SQL string
     */
    public function get_sql(): string {
        $whereclause = ' WHERE ';
        $firstiteration = true;

        if (empty($this->whereconditions)) {
            return '';
        }

        $whereclause .= implode(' AND ', $this->whereconditions);
        $whereclause .= ' ';

        return preg_replace('/\s{2,}/', ' ', $whereclause);
    }

    public function get_params(): array {
        $params = array_map(fn (where_expression $expression) => $expression->get_params(), $this->whereconditions);
        return array_merge(...$params);
    }
}
