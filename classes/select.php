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

use local_sqlquerybuilder\columns\aggregation;
use local_sqlquerybuilder\columns\column_aggregate;
use local_sqlquerybuilder\columns\column_expression;
use local_sqlquerybuilder\columns\column_raw;
use local_sqlquerybuilder\columns\column;

/**
 * Trait that builds a sql statement, that can be exported via
 * build_select()
 *
 * @package     local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait select {
    /** @var column_expression[] SQL Select Parts */
    protected array $select = [];

    /** @var bool Whether to use DISTINCT OR ALL */
    protected bool $distinct = false;

    /**
     * Selects all columns
     *
     * Should not be used with other selects
     *
     * @return $this Instance of the Builder
     */
    public function select_all(): static {
        $this->select = [new column_raw('*', true)];
        return $this;
    }

    /**
     * Selects an array of columns
     *
     * @param string $name Name of the column
     * @param string|null $alias Alias for the column name
     * @return $this Instance of the Builder
     */
    public function select(string $name, ?string $alias = null): static {
        $this->select[] = new column($name, $alias);
        return $this;
    }

    /**
     * Gives back the count of all entries
     *
     * Should not be used with other selects
     *
     * @return $this Instance of the Builder
     */
    public function select_count(): static {
        $this->select[] = new column_aggregate(aggregation::COUNT, '1');
        return $this;
    }

    /**
     * Gives back only the maximum of the defined parameter
     *
     * Should not be used with other selects
     *
     * @param string $name Name of the column
     * @param string|null $alias Alias for the column name
     * @return $this Instance of the Builder
     */
    public function select_max(string $name, ?string $alias = null): static {
        $this->select[] = new column_aggregate(aggregation::MAX, $name, $alias);
        return $this;
    }

    /**
     * Gives back only the minimum of the defined parameter
     *
     * Should not be used with other selects
     *
     * @param string $name Name of the column
     * @param string|null $alias Alias for the column name
     * @return $this Instance of the Builder
     */
    public function select_min(string $name, ?string $alias = null): static {
        $this->select[] = new column_aggregate(aggregation::MIN, $name, $alias);
        return $this;
    }

    /**
     * Gives back only the sum of the defined parameter
     *
     * Should not be used with other selects
     *
     * @param string $name Name of the column
     * @param string|null $alias Alias for the column name
     * @return $this Instance of the Builder
     */
    public function select_sum(string $name, ?string $alias = null): static {
        $this->select[] = new column_aggregate(aggregation::SUM, $name, $alias);
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
