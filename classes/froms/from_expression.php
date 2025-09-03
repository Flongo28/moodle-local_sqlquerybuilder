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

namespace local_sqlquerybuilder\froms;

/**
 * Interface for from types
 *
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface from_expression {
    /**
     * Exports as sql
     *
     * @param bool $rawsql If set to true it will be exported for a raw sql query
     * @return string from as sql
     */
    public function export(bool $rawsql = false): string;
}
