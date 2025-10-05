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

/**
 * Compares fulltext
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025, Konrad Ebel <despair2400@proton.me>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class where_fulltext extends where_expression {

    public function __construct(
        private string $column,
        private string $value,
        private bool $negate = false,
    ) {}

    public function get_sql(): string {
        global $DB;
        $sql = '';
        
        if ($this->negate) {
            $sql = 'NOT ';
        }

        $sql .= $DB->sql_compare_text($this->column, strlen($this->value));
        return $sql . ' = ?';
    }


    public function get_params(): array {
        return [$this->value];
    }
}
