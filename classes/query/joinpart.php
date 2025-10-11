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

    private function parse_conditions($conditions) {
        // Handle single condition.
        if (!is_array($conditions) || (count($conditions) == 3 && !is_array($conditions[0]))) {
            return [['condition' => $conditions, 'logic' => null]];
        }

        $parsed = [];
        $currentlogic = null;

        foreach ($conditions as $item) {
            if (is_string($item) && (strtoupper($item) === 'AND' || strtoupper($item) === 'OR')) {
                // This is a logic operator.
                $currentlogic = strtoupper($item);
            } else if (is_array($item) && count($item) >= 3) {
                // This is a condition.
                $parsed[] = ['condition' => $item, 'logic' => $currentlogic];
                $currentlogic = 'AND'; // Default for next condition if not specified.
            }
        }

        // If no parsed conditions, try the old format (array of arrays, all AND).
        if (empty($parsed)) {
            foreach ($conditions as $condition) {
                if (is_array($condition) && count($condition) >= 3) {
                    $parsed[] = ['condition' => $condition, 'logic' => count($parsed) === 0 ? null : 'AND'];
                }
            }
        }

        return $parsed;
    }

    public function join(string|i_query $table, $conditions, string $alias = '') {
        $this->joins[] = [$table, $this->parse_conditions($conditions), join_types::INNER, $alias];
    }

    public function left_join(string|i_query $table, $conditions, string $alias = '') {
        $this->joins[] = [$table, $this->parse_conditions($conditions), join_types::LEFT, $alias];
    }

    public function right_join(string|i_query $table, $conditions, string $alias = '') {
        $this->joins[] = [$table, $this->parse_conditions($conditions), join_types::RIGHT, $alias];
    }

    public function full_join(string $table, $conditions, string $alias = '') {
        $this->joins[] = [$table, $this->parse_conditions($conditions), join_types::FULL, $alias];
    }

    public function crossjoin(string $table, $conditions, string $alias = '') {
        $this->joins[] = [$table, $this->parse_conditions($conditions), join_types::CROSS, $alias];
    }

    public function get_sql(): string {
        if (empty($this->joins)) {
            return '';
        }
        $joinclause = '';

        foreach ($this->joins as $join) {
            $table = $join[0];
            $parsedconditions = $join[1];
            $jointype = $join[2];
            $alias = $join[3];

            // Build the table/subquery part.
            if ($table instanceof i_query) {
                $joinclause .= $jointype->value . ' JOIN (' . $table->get_sql() . ') ' . $alias . ' ON ';
            } else {
                $joinclause .= $jointype->value . ' JOIN {' . $table . '} ' . $alias . ' ON ';
            }

            // Build the conditions part with proper AND/OR logic.
            $conditionparts = [];
            foreach ($parsedconditions as $parsedcondition) {
                $condition = $parsedcondition['condition'];
                $logic = $parsedcondition['logic'];

                if (is_array($condition) && count($condition) >= 3) {
                    $conditionstr = $condition[0] . ' ' . $condition[1] . ' ' . $condition[2];

                    if ($logic && !empty($conditionparts)) {
                        $conditionparts[] = $logic . ' ' . $conditionstr;
                    } else {
                        $conditionparts[] = $conditionstr;
                    }
                }
            }

            $joinclause .= implode(' ', $conditionparts) . ' ';
        }

        return preg_replace('/\s{2,}/', ' ', trim($joinclause));
    }

    public function get_params(): array {
        return [];
    }
}
