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

use local_sqlquerybuilder\contracts\i_query;

/**
 * Data select from custom given values
 *
 * @package local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class from_values implements from_expression {
    public function __construct(
        private array $table,
        private ?string $tablename = null,
        private ?array $rowaliases = null,
    ) {
    }

    private function format_row(array $row): string {
        $formattedrow = [];

        foreach ($row as $value) {
            if ($value instanceof i_query) {
                $formattedrow[] = "($value)";
            } else if (is_string($value)) {
                $formattedrow[] = "'$value'";
            } else {
                $formattedrow[] = $value;
            }
        }

        return "(" . implode(', ', $formattedrow) . ")";
    }

    public function get_sql(): string {
        $from = "(VALUES ";

        $formattedrows = array_map(fn ($row) => $this->format_row($row), $this->table);
        $from .= implode(", ", $formattedrows);
        $from .= ") ";

        if (!is_null($this->tablename)) {
            $from .= "AS $this->tablename";

            if (!is_null($this->rowaliases)) {
                $from .= "(" . implode(',', $this->rowaliases) . ") ";
            }
        }

        return $from;
    }

    public function get_params(): array {
        $params = [];

        foreach ($this->table as $row) {
            foreach ($row as $colval) {
                if ($colval instanceof i_query) {
                    $params[] = $colval->get_params();
                }
            }
        }

        return array_merge(...$params);
    }
}
