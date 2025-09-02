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
 * @package     local_sql_query_builder
 * @copyright   2025 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sql_query_builder;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait for handling WHERE conditions in SQL queries.
 *
 * This trait provides methods for building WHERE clauses with AND and OR conditions.
 */
trait where {

    /**
     * Array to store WHERE conditions.
     *
     * @var array
     */
    protected $whereconditions = [];

    /**
     * Add a WHERE condition with AND logic.
     *
     * @param string $column The column name
     * @param string $operator The comparison operator (=, !=, >, <, >=, <=, LIKE, etc.)
     * @param mixed $value The value to compare against
     * @return $this For method chaining
     */

     ### TODO column koennte auch ein Array sein -> where([['status', '=', '1'],['subscribed', '<>', '1'] , dann gibt es keinen direkt operator/value
    public function where($column, $operator, $value) {
        $this->whereconditions[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'negative' => false,
        ];

        return $this;
    }

    /**
     * Add a WHERE condition with OR logic.
     *
     * @param string $column The column name
     * @param string $operator The comparison operator (=, !=, >, <, >=, <=, LIKE, etc.)
     * @param mixed $value The value to compare against
     * @return $this For method chaining
     */
    public function orwhere($column, $operator, $value) {
        $this->whereconditions[] = [
            'type' => 'OR',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'negative' => false,
        ];

        return $this;
    }

       /**
     * Add a WHERE  not condition with AND logic.
     *
     * @param string $column The column name
     * @param string $operator The comparison operator (=, !=, >, <, >=, <=, LIKE, etc.)
     * @param mixed $value The value to compare against
     * @return $this For method chaining
     */

     ### TODO column koennte auch ein Array sein -> where([['status', '=', '1'],['subscribed', '<>', '1'] , dann gibt es keinen direkt operator/value
     public function notwhere($column, $operator, $value) {
        $this->whereconditions[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'negative' => true,
        ];

        return $this;
    }

        /**
     * Add a WHERE NOT condition with OR logic.
     *
     * @param string $column The column name
     * @param string $operator The comparison operator (=, !=, >, <, >=, <=, LIKE, etc.)
     * @param mixed $value The value to compare against
     * @return $this For method chaining
     */
    public function orwherenot($column, $operator, $value) {
        $this->whereconditions[] = [
            'type' => 'OR',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'negative' => true,
        ];

        return $this;
    }

    q
    /**
     * Export the WHERE clause as a SQL string.
     *
     * @return string The complete WHERE clause SQL string
     */
    public function export_where() {
        $whereclause = ' WHERE ';
        $first_iteration = true;
        foreach ($this->whereconditions as $condition) {
            if (!$first_iteration){
                $whereclause .= $condition['type'] . ' ';
            }
            if ($condition['negative']){
                $whereclause .= 'NOT ';
            }
            $whereclause .= $condition['column'] . ' ' . $condition['operator'] . ' ' . $condition['value'] . ' ';
        }
        return $whereclause;
    }

}
