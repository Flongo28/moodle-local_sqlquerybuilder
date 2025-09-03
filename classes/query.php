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

use dml_exception;
use local_sqlquerybuilder\froms\from_expression;
use stdClass;

/**
 * A Query builder
 *
 * @package   local_sqlquerybuilder
 * @copyright 2025 Daniel Meißner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class query {
    use select;
    use where;
    use join;
    use orderby;
    use grouping;

    /**
     * Constructor
     *
     * @param from_expression $from table which concerns the query
     */
    public function __construct(public from_expression $from) {
    }

    /**
     * Compile the current builder state to a SQL query
     * @return string the SQL query
     */
    public function to_sql(): string {
        $sql = $this->export_select() . " "
            . "FROM " . $this->from->export(true)
            . $this->export_join()
            . $this->export_where()
            . $this->export_grouping()
            . $this->export_orderby();

        return trim($sql);
    }

    /**
     * Get multiple entries from the query
     *
     * @return stdClass[] Entries from the database call
     * @throws dml_exception Database is not reachable
     */
    public function get(): array {
        global $DB;
        return $DB->get_records_sql($this->to_sql());
    }

    /**
     * Get the first entry from the query
     *
     * @return stdClass|null An entry if found one
     * @throws dml_exception Database is not reachable
     */
    public function first(): ?stdClass {
        global $DB;
        $record = $DB->get_record_sql($this->to_sql(), strictness: IGNORE_MULTIPLE);
        return $record === false ? null : $record;
    }

    /**
     * Returns the entry searched id
     *
     * @param int $id Search ID
     * @return stdClass|bool An entry if found one
     * @throws dml_exception Database is not reachable
     */
    public function find(int $id): stdClass|bool {
        $this->where('id', '=', $id);
        return $this->first();
    }

    /**
     * Returns the sql of this query
     *
     * @return string Converts the query to sql
     */
    public function __toString(): string {
        return $this->to_sql();
    }
}
