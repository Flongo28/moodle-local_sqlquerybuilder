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
class selectpart implements i_expression {
    protected array $columns = [];
    protected bool $distinct = false;

    public function select_all(): void {
        $this->columns = [new column_raw('*', [])];
    }

    public function select(string $name, ?string $alias = null): void {
        $this->columns[] = new column($name, $alias);
    }

    public function select_count(): void {
        $this->columns = [new column_aggregate(aggregation::COUNT, '1')];
    }

    public function select_max(string $name, ?string $alias = null): void {
        $this->columns = [new column_aggregate(aggregation::MAX, $name, $alias)];
    }

    public function select_min(string $name, ?string $alias = null): void {
        $this->columns = [new column_aggregate(aggregation::MIN, $name, $alias)];
    }

    public function select_sum(string $name, ?string $alias = null): void {
        $this->columns = [new column_aggregate(aggregation::SUM, $name, $alias)];
    }

    public function distinct(): void {
        $this->distinct = true;
    }

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

    public function get_params(): array {
        $params = [];

        foreach ($this->columns as $col) {
            $params[] = $col->get_params();
        }

        return array_merge(...$params);
    }
}
