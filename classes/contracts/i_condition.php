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

namespace local_sqlquerybuilder\contracts;

/**
 * Builds an where expression without WHERE
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025 Konrad Ebel <despair2400@proton.me>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface i_condition extends i_expression {
    public function where(string $column, string $operator, mixed $value): i_condition;
    public function where_column(string $column, string $operator, string $othercolumn): i_condition;
    public function or_where(string $column, string $operator, mixed $value): i_condition;
    public function where_not(string $column, string $operator, mixed $value): i_condition;
    public function or_where_not(string $column, string $operator, mixed $value): i_condition;
    public function where_fulltext(string $column, string $value): i_condition;
    public function where_fulltext_not(string $column, string $value): i_condition;
    public function where_like(string $column, string $value, ?like_options $options = null): i_condition;
    public function where_not_like(string $column, string $value, ?like_options $options = null): i_condition;
    public function where_null(string $column): i_condition;
    public function or_where_null(string $column): i_condition;
    public function where_notnull(string $column): i_condition;
    public function or_where_notnull(string $column): i_condition;
    public function where_in(string $column, array|i_query $values): i_condition;
    public function where_not_in(string $column, array|i_query $values): i_condition;
    public function where_currently_active(string $columntimestart, string $columntimeend): i_condition;
    public function has_no_conditions(): bool;
}
