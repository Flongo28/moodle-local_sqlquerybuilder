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

namespace local_sql_query_builder;

/**
 * A Query builder
 *
 * @package   local_sql_query_builder
 * @copyright 2025 Daniel Meißner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class query {
    /**
     * @param string $from table which concerns the query
     */
    public function __construct(private string $from) {
    }

    /**
     * Compile the current builder state to a SQL query
     * @return string the SQL query
     */
    public function to_sql(): string {
        $sql = 'SELECT ' . $this->export_select()
            . 'FROM {' . $from . '}';

        return $sql;
    }
}
