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


use local_sqlquerybuilder\joins\join_expression;
use local_sqlquerybuilder\joins\join_types;
use local_sqlquerybuilder\query;

/**
 * Trait that builds a sql statement, that can be exported via
 * build_join()
 *
 * @package local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait join {
    /** @var join_expression[] All join expressions for the request */
    protected array $joins = [];

    /**
     * Get the allowed table aliases
     *
     * @return array
     */
    protected function get_allowed_table_aliases(): array {

    }

    /**
     * Parse conditions array to support AND/OR logic
     *
     * @param mixed $conditions
     * @return array Parsed conditions with logic operators
     */
    private function parse_conditions($conditions) {
        // Handle single condition
        if (!is_array($conditions) || (count($conditions) == 3 && !is_array($conditions[0]))) {
            return [['condition' => $conditions, 'logic' => null]];
        }
        
        $parsed = [];
        $currentLogic = null;
        
        foreach ($conditions as $item) {
            if (is_string($item) && (strtoupper($item) === 'AND' || strtoupper($item) === 'OR')) {
                // This is a logic operator
                $currentLogic = strtoupper($item);
            } else if (is_array($item) && count($item) >= 3) {
                // This is a condition
                $parsed[] = ['condition' => $item, 'logic' => $currentLogic];
                $currentLogic = 'AND'; // Default for next condition if not specified
            }
        }
        
        // If no parsed conditions, try the old format (array of arrays, all AND)
        if (empty($parsed)) {
            foreach ($conditions as $condition) {
                if (is_array($condition) && count($condition) >= 3) {
                    $parsed[] = ['condition' => $condition, 'logic' => count($parsed) === 0 ? null : 'AND'];
                }
            }
        }
        
        return $parsed;
    }

    /**
     * join - supports both single condition and array of conditions with AND/OR logic
     *
     * @param string $table
     * @param mixed $conditions Single condition or array of conditions
     *                         Format: [['first', 'op', 'second'], 'AND', ['first2', 'op2', 'second2'], 'OR', ...]
     *                         OR: [['first', 'op', 'second']] (defaults to AND between multiple conditions)
     * @param string $alias
     * @return $this
     */
    public function join(string $table, $conditions, string $alias = '') {
        $this->joins[] = [$table, $this->parse_conditions($conditions), join_types::INNER, $alias];
        return $this;
    }

    /**
     * left join - supports both single condition and array of conditions with AND/OR logic
     *
     * @param string $table
     * @param mixed $conditions Single condition or array of conditions with AND/OR logic
     * @param string $alias
     * @return $this
     */
    public function leftjoin(string $table, $conditions, string $alias = '') {
        $this->joins[] = [$table, $this->parse_conditions($conditions), join_types::LEFT, $alias];
        return $this;
    }

    /**
     * right join - supports both single condition and array of conditions with AND/OR logic
     *
     * @param string $table
     * @param mixed $conditions Single condition or array of conditions with AND/OR logic
     * @param string $alias
     * @return $this
     */
    public function rightjoin(string $table, $conditions, string $alias = '') {
        $this->joins[] = [$table, $this->parse_conditions($conditions), join_types::RIGHT, $alias];
        return $this;
    }

    /**
     * full join - supports both single condition and array of conditions with AND/OR logic
     *
     * @param string $table
     * @param mixed $conditions Single condition or array of conditions with AND/OR logic
     * @param string $alias
     * @return $this
     */
    public function fulljoin(string $table, $conditions, string $alias = '') {
        $this->joins[] = [$table, $this->parse_conditions($conditions), join_types::FULL, $alias];
        return $this;
    }

    /**
     * sub join - supports both single condition and array of conditions with AND/OR logic
     *
     * @param \local_sqlquerybuilder\query $query
     * @param mixed $conditions Single condition or array of conditions with AND/OR logic
     * @param string $alias
     * @return $this
     */
    public function joinsub(query $query, $conditions, string $alias) {
        $this->joins[] = [$query, $this->parse_conditions($conditions), join_types::INNER, $alias];
        return $this;
    }

    /**
     * sub left join - supports both single condition and array of conditions with AND/OR logic
     *
     * @param \local_sqlquerybuilder\query $query
     * @param mixed $conditions Single condition or array of conditions with AND/OR logic
     * @param string $alias
     * @return $this
     */
    public function leftjoinsub(query $query, $conditions, string $alias) {
        $this->joins[] = [$query, $this->parse_conditions($conditions), join_types::LEFT, $alias];
        return $this;
    }

    /**
     * sub right join - supports both single condition and array of conditions with AND/OR logic
     *
     * @param \local_sqlquerybuilder\query $query
     * @param mixed $conditions Single condition or array of conditions with AND/OR logic
     * @param string $alias
     * @return $this
     */
    public function rightjoinsub(query $query, $conditions, string $alias) {
        $this->joins[] = [$query, $this->parse_conditions($conditions), join_types::RIGHT, $alias];
        return $this;
    }

    // Todo: preliminary function - do not use.
    /**
     * cross join - supports both single condition and array of conditions with AND/OR logic
     *
     * @param string $table
     * @param mixed $conditions Single condition or array of conditions with AND/OR logic
     * @param string $alias
     * @return $this
     */
    public function crossjoin(string $table, $conditions, string $alias = '') {
        $this->joins[] = [$table, $this->parse_conditions($conditions), join_types::CROSS, $alias];
        return $this;
    }

    /**
     * export join string
     *
     * @return string
     */
    protected function export_join(): string {
        if (empty($this->joins)) {
            return '';
        }
        $joinclause = '';
        
        foreach ($this->joins as $join) {
            $table = $join[0];
            $parsedConditions = $join[1];
            $jointype = $join[2];
            $alias = $join[3];
            
            // Build the table/subquery part
            if ($table instanceof query) {
                $joinclause .= $jointype->value . ' JOIN (' . $table->to_sql() . ') ' . $alias . ' ON ';
            } else {
                $joinclause .= $jointype->value . ' JOIN {' . $table . '} ' . $alias . ' ON ';
            }
            
            // Build the conditions part with proper AND/OR logic
            $conditionParts = [];
            foreach ($parsedConditions as $parsedCondition) {
                $condition = $parsedCondition['condition'];
                $logic = $parsedCondition['logic'];
                
                if (is_array($condition) && count($condition) >= 3) {
                    $conditionStr = $condition[0] . ' ' . $condition[1] . ' ' . $condition[2];
                    
                    if ($logic && !empty($conditionParts)) {
                        $conditionParts[] = $logic . ' ' . $conditionStr;
                    } else {
                        $conditionParts[] = $conditionStr;
                    }
                }
            }
            
            $joinclause .= implode(' ', $conditionParts) . ' ';
        }
        
        return preg_replace('/\s{2,}/', ' ', trim($joinclause));
    }
}
