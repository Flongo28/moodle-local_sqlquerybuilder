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
 * Basic column with alias for select statements
 *
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column implements column_expression {
    /** @var string|null Name of the table to call the column from */
    private ?string $table;
    /** @var string Name of the column */
    private string $name;
    /** @var string|null Alias for the column name */
    protected ?string $alias;

    /**
     * Constructor
     *
     * @param string $name Name of the column
     * @param string|null $table Name of the table to call from
     * @param string|null $alias Alias for the column name
     */
    public function __construct(string $name, ?string $table = null, ?string $alias = null) {
        $this->table = $table;
        $this->name = $name;
        $this->alias = $alias;
    }

    /**
     * Gets the locator with table and column for a select part
     *
     * @return string locator e.g. u.username
     */
    protected function get_column_locator(): string {
        if ($this->table !== null) {
            return  "$this->table.$this->name";
        }

        return $this->name;
    }

    /**
     * Exports as sql
     *
     * @return string column for select as sql
     */
    public function export(): string {
        $locator = $this->get_column_locator();

        if ($this->alias === null) {
            return $locator;
        }

        return "($locator) AS $this->alias";
    }

    /**
     * Can be used with other columns
     *
     * @return bool False
     */
    public function standalone(): bool {
        return false;
    }
}
