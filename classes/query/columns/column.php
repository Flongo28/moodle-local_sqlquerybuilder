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

namespace local_sqlquerybuilder\query\columns;

/**
 * Basic column with alias for select statements
 *
 * @package local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column implements column_expression {
    /**
     * Constructor
     */
    public function __construct(
        /** @var string $name Name of the column */
        protected string $name,
        /** @var string|null $alias Alias for the column name */
        protected ?string $alias = null
    ) {}

    /**
     * Exports as sql
     *
     * @return string column for select as sql
     */
    public function get_sql(): string {
        if ($this->alias === null) {
            return $this->name;
        }

        return "($this->name) AS $this->alias";
    }

    /**
     * Exports the params used
     *
     * @return array No params needed
     */
    public function get_params(): array {
        return [];
    }
}
