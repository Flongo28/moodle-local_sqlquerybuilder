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
     * join
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $alias
     * @return $this
     */
    public function join(string $table, string $first, string $operator, string $second , string $alias = '') {
        $this->joins[] = [$table, $first, $operator, $second, join_types::INNER, $alias];
        return $this;
    }

    /**
     * left join
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $alias
     * @return $this
     */
    public function leftjoin(string $table, string $first, string $operator, string $second , string $alias = '') {
        $this->joins[] = [$table, $first, $operator, $second, join_types::LEFT, $alias];
        return $this;
    }

    /**
     * right join
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $alias
     * @return $this
     */
    public function rightjoin(string $table, string $first, string $operator, string $second , string $alias = '') {
        $this->joins[] = [$table, $first, $operator, $second, join_types::RIGHT, $alias];
        return $this;
    }

    /**
     * full join
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $alias
     * @return $this
     */
    public function fulljoin(string $table, string $first, string $operator, string $second , string $alias = '') {
        $this->joins[] = [$table, $first, $operator, $second, join_types::FULL, $alias];
        return $this;
    }

    /**
     * sub join
     *
     * @param \local_sqlquerybuilder\query $query
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $alias
     * @return $this
     */
    public function joinsub(query $query, string $first, string $operator, string $second, string $alias) {
        $this->joins[] = [$query, $first, $operator, $second, join_types::INNER, $alias];
        return $this;
    }

    /**
     * sub left join
     *
     * @param \local_sqlquerybuilder\query $query
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $alias
     * @return $this
     */
    public function leftjoinsub(query $query, string $first, string $operator, string $second, string $alias) {
        $this->joins[] = [$query, $first, $operator, $second, join_types::LEFT, $alias];
        return $this;
    }

    /**
     * sub right join
     *
     * @param \local_sqlquerybuilder\query $query
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $alias
     * @return $this
     */
    public function rightjoinsub(query $query, string $first, string $operator, string $second, string $alias) {
        $this->joins[] = [$query, $first, $operator, $second, join_types::RIGHT, $alias];
        return $this;
    }

    // Todo: preliminary function - do not use.
    /**
     * cross join
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $alias
     * @return $this
     */
    public function crossjoin(string $table, string $first, string $operator, string $second, string $alias = '') {
        $this->joins[] = [$table, $first, $operator, $second, join_types::CROSS, $alias];
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
            if ($join[0] instanceof query) {
                $joinclause .= $join[4]->value . ' JOIN (' . $join[0]->to_sql() . ') ' . $join[5] . ' ON ' . $join[1] .
                    ' ' . $join[2] . ' ' . $join[3] . ' ';
            } else {
                $joinclause .= $join[4]->value . ' JOIN {' . $join[0] . '} ' . $join[5] . ' ON ' . $join[1] .
                    ' ' . $join[2] . ' ' . $join[3] . ' ';
            }
        }
        return $joinclause;
    }
}
