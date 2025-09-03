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

namespace local_sqlquerybuilder;

use Stringable;
use local_sqlquerybuilder\froms\from_table;
use local_sqlquerybuilder\froms\from_values;

/**
 * Syntactic sugar for the query object
 *
 * @package   local_sqlquerybuilder
 * @copyright 2025 Daniel Meißner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class db {

    /**
     * Return a new query object for the given table.
     * @param string $name Name the table name
     * @param string|null $alias Alias for the tablename
     * @return query
     */
    public static function table(string $name, ?string $alias = null): query {
        return new query(new from_table($name, $alias));
    }

    /**
     * Creates a query on a custom made query
     *
     * @param Stringable[][] $table Table with the structure of row[entry]
     * @param string[]|null $aliases List of aliases for the columns, it needs to have the same size as each entry
     * @param string $tablename Name of the table, only used if aliases are given
     */
    public static function from_values(
        array $table,
        ?array $aliases = null,
        string $tablename = "custom_value_table",
    ) {
        return new query(new from_values($table, $aliases, $tablename));
    }
}
