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
use stdClass;
use local_sqlquerybuilder\contracts\i_query;
use local_sqlquerybuilder\query\froms\from_expression;

/**
 * A Query builder
 *
 * @package   local_sqlquerybuilder
 * @copyright 2025 Daniel Meißner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class query implements i_query {
    private selectpart $selectpart;
    private joinpart $joinpart;
    private wherepart $wherepart;
    private grouping $groupingpart;
    private orderby $orderbypart;
    private pagination $pagination;

    public function __construct(
        public from_expression $from
    ) {
        $this->selectpart = new selectpart();
        $this->joinpart = new joinpart();
        $this->wherepart = new wherepart();
        $this->groupingpart = new grouping();
        $this->orderbypart = new orderby();
        $this->pagination = new pagination();
    }

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

    public function get(): array {
        global $DB;
        return $DB->get_records_sql($this->get_sql(), $this->get_params());
    }

    public function first(): stdClass|false {
        global $DB;
        $record = $DB->get_record_sql($this->get_sql(), $this->get_params(), strictness: IGNORE_MULTIPLE);
        return $record;
    }

    public function find(int $id): stdClass|false {
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

    public function __call($method, $args): i_query {
        $parts = $this->get_query_parts();

        foreach ($parts as $part) {
            if (method_exists($part, $method)) {
                $part->$method(...$args);
                return $this;
            }
        }

        throw new BadMethodCallException("Method $method is not defined");
    }

    public function __toString(): string {
        return $this->get_sql();
    }
}
