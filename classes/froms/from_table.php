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

/**
 * Data select from table
 *
 * e.g. a table from the database
 *
 * @package local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class from_table implements from_expression {
    /**
     * Constructor
     *
     * @param string $table Table name
     * @param string|null $alias Alias for the tablename
     */
    public function __construct(
        /**
         * @var string|null table name
         */
        private string $table,
        /**
         * @var string|null table alias
         */
        private ?string $alias,
    ) {
    }

    /**
     * Exports as sql
     *
     * @param bool $rawsql If set to true it will be exported for a raw sql query
     * @return string column for select as sql
     */
    public function export(bool $rawsql = false): string {
        if (!$rawsql) {
            return $this->table;
        }

        if (is_null($this->alias)) {
            return "{" . $this->table . "} ";
        }

        return "{" . $this->table . "} " . $this->alias . " ";
    }
}
