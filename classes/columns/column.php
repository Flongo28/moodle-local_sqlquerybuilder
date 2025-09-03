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
 * @package local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column implements column_expression {
    /** @var string Name of the column */
    protected string $name;
    /** @var string|null Alias for the column name */
    protected ?string $alias;

    /**
     * Constructor
     *
     * @param string $name Name of the column
     * @param string|null $alias Alias for the column name
     */
    public function __construct(string $name, ?string $alias = null) {
        $this->name = $name;
        $this->alias = $alias;
    }

    /**
     * Exports as sql
     *
     * @return string column for select as sql
     */
    public function export(): string {
        if ($this->alias === null) {
            return $this->name;
        }

        return "($this->name) AS $this->alias";
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
