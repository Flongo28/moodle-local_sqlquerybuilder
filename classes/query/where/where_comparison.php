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

namespace local_sqlquerybuilder\query\where;

use InvalidArgumentException;
use local_sqlquerybuilder\query\query;

/**
 * Where expression
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025, Konrad Ebel <despair2400@proton.me>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class where_comparison extends where_expression {
    private string $operator;

    public function __construct(
        private string $column,
        string $operator,
        private mixed $value,
        private bool $negate = false,
    ) {
        if ($operator == "!=") {
            $operator = "<>";
        }

        $validoperators = ["<>", "<", "<=", ">", ">=", "="];
        if (!in_array($operator, $validoperators)) {
            throw new InvalidArgumentException("Operator $operator is not supported by moodle");
        }

        $this->operator = $operator;
    }

    public function get_sql(): string {
        $sql = "";

        if ($this->negate) {
            $sql .= 'NOT ';
        }

        $sql .= "$this->column $this->operator ";

        if ($this->value instanceof query) {
            $sql .= "($this->value)";
        } else {
            $sql .= '?';
        }

        return $sql;
    }

    public function get_params(): array {
        if ($this->value instanceof query) {
            return $this->value->get_params();
        }

        return [$this->value];
    }
}
