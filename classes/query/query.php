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

namespace local_sqlquerybuilder\query;

use BadMethodCallException;
use dml_exception;
use local_sqlquerybuilder\query\froms\from_expression;
use stdClass;
use moodle_database;

/**
 * A Query builder
 *
 * @package   local_sqlquerybuilder
 * @copyright 2025 Daniel Meißner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class query implements expression {
    private select $selectpart;
    private join $joinpart;
    private wherepart $wherepart;
    private grouping $groupingpart;
    private orderby $orderbypart;
    private pagination $pagination;

    /**
     * Constructor
     *
     * @param from_expression $from table which concerns the query
     */
    public function __construct(
        public from_expression $from
    ) {
        $this->selectpart = new select();
        $this->joinpart = new join();
        $this->wherepart = new wherepart();
        $this->groupingpart = new grouping();
        $this->orderbypart = new orderby();
        $this->pagination = new pagination();
    }

    /**
     * Compile the current builder state to a SQL query
     * @return string the SQL query
     */
    public function get_sql(): string {
        $sql = $this->selectpart->get_sql() . " "
            . "FROM " . $this->from->get_sql()
            . $this->joinpart->get_sql()
            . $this->wherepart->get_sql()
            . $this->groupingpart->get_sql()
            . $this->orderbypart->get_sql()
            . $this->pagination->get_sql();

        return trim(preg_replace('/\s{2,}/', ' ', $sql));
    }

    public function get_params(): array {
        $params = [];

        foreach ($this->get_query_parts() as $part) {
            $params[] = $part->get_params();
        }

        return array_merge(...$params);
    }

    /**
     * Get multiple entries from the query
     *
     * @return stdClass[] Entries from the database call
     * @throws dml_exception Database is not reachable
     */
    public function get(): array {
        global $DB;
        return $DB->get_records_sql($this->get_sql(), $this->get_params());
    }

    /**
     * Get the first entry from the query
     *
     * @return stdClass|null An entry if found one
     * @throws dml_exception Database is not reachable
     */
    public function first(): ?stdClass {
        global $DB;
        $record = $DB->get_record_sql($this->get_sql(), $this->get_params(), strictness: IGNORE_MULTIPLE);
        return $record === false ? null : $record;
    }

    /**
     * Returns the entry searched id
     *
     * @param int $id Search ID
     * @return stdClass|null An entry if found one
     * @throws dml_exception Database is not reachable
     */
    public function find(int $id): ?stdClass {
        $this->wherepart->where('id', '=', $id);
        return $this->first();
    } 

    private function get_query_parts(): array {
        return [
            $this->selectpart,
            $this->from,
            $this->joinpart,
            $this->wherepart,
            $this->groupingpart,
            $this->orderbypart,
            $this->pagination,
        ];
    }

    public function __call($method, $args): static {
        $parts = $this->get_query_parts();

        foreach ($parts as $part) {
            if (method_exists($part, $method)) {
                $part->$method(...$args);
                return $this;
            }
        }

        throw new BadMethodCallException("Method $method is not defined");
    }

    /**
     * Returns the sql of this query
     *
     * @return string Converts the query to sql
     */
    public function __toString(): string {
        return $this->get_sql();
    }
}
