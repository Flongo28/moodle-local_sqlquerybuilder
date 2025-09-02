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
use local_sqlquerybuilder\joins\join_type;

/**
 * Trait that builds a sql statement, that can be exported via
 * build_join()
 *
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait join {
    /** @var join_expression[] All join expressions for the request */
    protected array $joins = [];

    protected function get_allowed_table_aliases(): array {

    }
    protected function join($table, $first, $operator, $second , $alias = '') {
        $this->joins[] = [$table, $first, $operator, $second, join_type::INNER, $alias];
    }

    protected function leftjoin($table, $first, $operator, $second, $alias = '') {
        $this->joins[] = [$table, $first, $operator, $second, join_type::LEFT, $alias];
    }
    protected function rightjoin($table, $first, $operator, $second, $alias = '') {
        $this->joins[] = [$table, $first, $operator, $second, join_type::RIGHT, $alias];
    }
    protected function fulljoin($table, $first, $operator, $second, $alias = '') {
        $this->joins[] = [$table, $first, $operator, $second, join_type::FULL, $alias];
    }
/*     protected function crossjoin($table, $first, $operator, $second, $alias = '') {
        $this->joins[] = [$table, $first, $operator, $second, join_type::CROSS, $alias];
    } */
    protected function export_join(): string {
        if (empty($this->joins)){
            return '';
        }
        $joinclause = '';
        foreach ($this->joins as $join) {
            $joinclause .= $join[4]->value . ' JOIN ' . $join[0] . ' ' . $join[5] . ' ON ' . $join[1] . ' ' . $join[2] . ' ' . $join[3] . ' ';
        }
        return $joinclause;
    }
}
