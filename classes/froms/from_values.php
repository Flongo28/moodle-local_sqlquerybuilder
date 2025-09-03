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

namespace local_sqlquerybuilder\froms;

use Stringable;

/**
 * Data select from custom given values
 *
 * @package local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class from_values implements from_expression {
    /**
     * Constructor
     *
     * @param Stringable[][] $table Table with the structure of row[entry]
     * @param string[]|null $aliases List of aliases for the columns, it needs to have the same size as each entry
     * @param string $tablename Name of the table, only used if aliases are given
     */
    public function __construct(
        /**
         * @var array|null table
         */
        private array $table,
        /**
         * @var array|null table asliases
         */
        private ?array $aliases,
        /**
         * @var string table name
         */
        private string $tablename,
    ) {
    }

    /**
     * Exports as sql
     *
     * @param bool $rawsql Has no changes here
     * @return string column for select as sql
     */
    public function export(bool $rawsql = false): string {
        $from = "VALUES(\n";

        foreach ($this->table as $row) {
            $from .= "(";
            $formattedrow = array_map(fn ($colval) => "($colval)", $row);
            $from .= implode(', ', $formattedrow);
            $from .= ")\n";
        }

        $from .= ")";

        if (!is_null($this->aliases)) {
            $from .= " AS $this->tablename(" . implode(',', $this->aliases) . ") ";
        }

        return $from;
    }
}
