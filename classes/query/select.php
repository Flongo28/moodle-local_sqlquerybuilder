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
use local_sqlquerybuilder\query\columns\aggregation;
use local_sqlquerybuilder\query\columns\column_aggregate;
use local_sqlquerybuilder\query\columns\column_raw;
use local_sqlquerybuilder\query\columns\column;

/**
 * Trait that builds a sql statement, that can be exported via
 * build_select()
 *
 * @package     local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class select implements i_expression {
    /** @var i_expression[] Selected columns */
    protected array $columns = [];

    /** @var bool Whether to use DISTINCT OR ALL */
    protected bool $distinct = false;

    /**
     * Selects all columns
     *
     * Should not be used with other selects
     */
    public function select_all(): void {
        $this->columns = [new column_raw('*', [], true)];
    }

    /**
     * Selects an array of columns
     *
     * @param string $name Name of the column
     * @param string|null $alias Alias for the column name
     */
    public function select(string $name, ?string $alias = null): void {
        $this->columns[] = new column($name, $alias);
    }

    /**
     * Gives back the count of all entries
     *
     * Should not be used with other selects
     */
    public function select_count(): void {
        $this->columns = [new column_aggregate(aggregation::COUNT, '1')];
    }

    /**
     * Gives back only the maximum of the defined parameter
     *
     * Should not be used with other selects
     *
     * @param string $name Name of the column
     * @param string|null $alias Alias for the column name
     */
    public function select_max(string $name, ?string $alias = null): void {
        $this->columns = [new column_aggregate(aggregation::MAX, $name, $alias)];
    }

    /**
     * Gives back only the minimum of the defined parameter
     *
     * Should not be used with other selects
     *
     * @param string $name Name of the column
     * @param string|null $alias Alias for the column name
     */
    public function select_min(string $name, ?string $alias = null): void {
        $this->columns = [new column_aggregate(aggregation::MIN, $name, $alias)];
    }

    /**
     * Gives back only the sum of the defined parameter
     *
     * Should not be used with other selects
     *
     * @param string $name Name of the column
     * @param string|null $alias Alias for the column name
     */
    public function select_sum(string $name, ?string $alias = null): void {
        $this->columns = [new column_aggregate(aggregation::SUM, $name, $alias)];
    }

    /**
     * Only distinct columns are returned
     */
    public function distinct(): void {
        $this->distinct = true;
    }

    /**
     * Builds the select part for a sql statement
     *
     * @return string sql select statement
     */
    public function get_sql(): string {
        $select = 'SELECT ';

        if ($this->distinct) {
            $select .= 'DISTINCT ';
        }

        if (empty($this->columns)) {
            $this->select_all();
        }

        $exportedcolumns = array_map(fn (i_expression $col) => $col->get_sql(), $this->columns);
        $select .= implode(', ', $exportedcolumns);

        return $select;
    }

    /**
     * Gives back all params of the select part
     * 
     * @return array All params used in select
     */
    public function get_params(): array {
        $params = [];

        foreach ($this->columns as $col) {
            $params[] = $col->get_params();
        }

        return array_merge(...$params);
    }
}
