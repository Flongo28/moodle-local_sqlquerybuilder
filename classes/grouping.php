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

namespace local_sqlquerybuilder;

/**
 * Grouping trait
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait grouping {
    /**
     * @var array of group bu clauses
     */
    protected array $groupby = [];
    /**
     * @var array of havings
     */
    protected array $having = [];

    /**
     * Group by one or more columns
     *
     * @param string ...$column Columns to group by
     * @return $this Instance of the Builder
     */
    public function groupby(...$column) {
        $this->groupby = $column;
        return $this;
    }

    /**
     * Add a HAVING condition with AND logic.
     *
     * @param string $column The column name
     * @param string $operator The comparison operator (=, !=, >, <, >=, <=, LIKE, etc.)
     * @param mixed $value The value to compare against
     * @return $this Instance of the Builder
     */
    public function having($column, $operator, $value) {
        $this->having[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];
        return $this;
    }

    /**
     * Add a HAVING condition with OR logic.
     *
     * @param string $column The column name
     * @param string $operator The comparison operator (=, !=, >, <, >=, <=, LIKE, etc.)
     * @param mixed $value The value to compare against
     * @return $this Instance of the Builder
     */
    public function orhaving($column, $operator, $value) {
        $this->having[] = [
            'type' => 'OR',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];
        return $this;
    }

    /**
     * Export the GROUP BY clause as a SQL string.
     *
     * @return string The complete GROUP BY clause SQL string
     */
    protected function export_grouping(): string {
        if (empty($this->groupby)) {
            return '';
        }
        $groupbyclause = 'GROUP BY ' . implode(', ', $this->groupby);
        if (empty($this->having)) {
            return $groupbyclause;
        }
        $firstiteration = true;
        foreach ($this->having as $having) {
            if ($firstiteration) {
                $groupbyclause .= ' HAVING ' . $having['column'] . ' ' . $having['operator'] .
                    ' ' . $having['value'] . ' ';
                $firstiteration = false;
            } else {
                $groupbyclause .= $having['type'] . ' ' . $having['column'] . ' ' . $having['operator'] .
                    ' ' . $having['value'] . ' ';
            }
        }
        return preg_replace('/\s{2,}/', ' ', $groupbyclause);
    }
}
