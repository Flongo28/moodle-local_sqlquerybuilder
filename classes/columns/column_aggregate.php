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

namespace local_sqlquerybuilder\columns;

/**
 * Aggregation column with alias for select statements
 *
 * @package local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_aggregate extends column {
    /** @var aggregation Type of aggregation */
    private aggregation $type;

    /**
     * Constructor
     *
     * @param aggregation $type Aggregation type to use
     * @param string $name Name of the column
     * @param string|null $alias Alias for the column name
     */
    public function __construct(aggregation $type, string $name, ?string $alias = null) {
        $this->type = $type;
        parent::__construct($name, $alias);
    }

    /**
     * Exports as sql
     *
     * @return string column for select as sql
     */
    public function export(): string {
        $column = $this->type->value . "($this->name)";

        if ($this->alias !== null) {
            $column .= " AS $this->alias";
        }

        return $column;
    }

    /**
     * Should be the only column used
     *
     * @return bool True
     */
    public function standalone(): bool {
        return true;
    }
}
