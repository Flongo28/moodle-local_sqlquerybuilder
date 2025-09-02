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

use local_sqlquerybuilder\columns\column_expression;
use local_sqlquerybuilder\columns\column_raw;

/**
 * Trait that builds a sql statement, that can be exported via
 * build_select()
 *
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait select {
    /** @var column_expression[] SQL Select Parts */
    public array $select = [];

    /** @var bool Whether to use DISTINCT OR ALL */
    public bool $distinct = false;

    /**
     * Selects an array of columns
     *
     * @param column_expression[] $columns
     * @return $this Instance of the Builder
     */
    public function select(array $columns): static {
        $this->select = $columns;
        return $this;
    }

    /**
     * Selects all columns
     *
     * @return $this Instance of the Builder
     */
    public function select_all(): static {
        $this->select = [new column_raw('*')];
        return $this;
    }

    /**
     * Only distinct columns are returned
     *
     * @return $this Instance of the Builder
     */
    public function distinct(): static {
        $this->distinct = true;
        return $this;
    }

    /**
     * Builds the select part for a sql statement
     *
     * @return string sql select statement
     */
    protected function export_select(): string {
        $select = 'SELECT ';

        if ($this->distinct) {
            $select .= 'DISTINCT ';
        }

        if (empty($this->select)) {
            $this->select_all();
        }

        $exportedcolumns = array_map(fn (column_expression $col) => $col->export(), $this->select);
        $select .= implode(', ', $exportedcolumns);

        return $select;
    }
}
