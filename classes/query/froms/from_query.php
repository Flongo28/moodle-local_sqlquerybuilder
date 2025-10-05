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

namespace local_sqlquerybuilder\query\froms;

use local_sqlquerybuilder\query\query;

/**
 * Data select from a custom query
 *
 * @package     local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class from_query implements from_expression {
    /**
     * Constructor
     *
     * @param query $sourcequery Query that builds a table
     * @param string $alias Alias for the table builded by the query (alias is needed!)
     */
    public function __construct(
        private query $sourcequery,
        private string $alias,
    ) {
    }

    /**
     * Exports as sql
     *
     * @param bool $rawsql Has no changes here
     * @return string column for select as sql
     */
    public function get_sql(): string {
        $from = "($this->sourcequery) AS $this->alias";
        return $from;
    }

    public function get_params(): array {
        return $this->sourcequery->get_params();
    }
}
