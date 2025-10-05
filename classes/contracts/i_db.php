<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_sqlquerybuilder\contracts;

/**
 * Database object to start a query
 *
 * @package   local_sqlquerybuilder
 * @copyright 2025 Konrad Ebel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface i_db {

    /**
     * Return a new query object for the given table.
     * @param string|i_query $nameorquery Name the table name or subquery
     * @param string|null $alias Alias for the tablename
     * @return i_query
     */
    public function table(string|i_query $nameorquery, ?string $alias = null): i_query;

    /**
     * Creates a query on a custom made query
     *
     * @param mixed[][] $table Table with the structure of row[entry]
     * @param string $tablename Name of the table, only used if aliases are given
     * @param string[] $rowaliases List of aliases for the columns, it needs to have the same size as each entry
     */
    public function from_values(array $table, string $tablename, array $rowaliases): i_query;
}
