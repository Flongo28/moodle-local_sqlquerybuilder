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

use stdClass;
use local_sqlquerybuilder\select;
use local_sqlquerybuilder\where;
use local_sqlquerybuilder\join;

/**
 * A Query builder
 *
 * @package   local_sqlquerybuilder
 * @copyright 2025 Daniel Meißner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class query {
    use select, where, join;

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
        $sql = $this->export_select()
            . ' FROM {' . $this->from . '}'
            . $this->export_where();

        return trim($sql);
    }

    public function get(): array {
        global $DB;
        return $DB->get_records_sql($this->to_sql());
    }

    public function first(): ?\stdClass {
        global $DB;

        if (empty($this->wheres)) {
            $record = $DB->get_record($this->from, [], '*', IGNORE_MULTIPLE);
        } else {
            // Only supports simple '=' and '<>' for now.
            [$field, $operator, $value] = $this->wheres[0];
            if ($operator === '=') {
                $record = $DB->get_record($this->from, [$field => $value], '*', IGNORE_MULTIPLE);
            } else if ($operator === '<>') {
                $records = $DB->get_records_select($this->from, "{$field} <> :val", ['val' => $value], 'id ASC', '*', 0, 1);
                $record = reset($records) ?: false;
            } else {
                throw new \coding_exception("Operator $operator not supported in first()");
            }
        }

        return $record === false ? null : $record;
    }

    public function find(int $id): stdClass|bool {
        global $DB;
        return $DB->get_record($this->from, ['id' => $id]);
    }

}
