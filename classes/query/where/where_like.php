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
 * Compares a column with a string
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025, Konrad Ebel <despair2400@proton.me>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class where_like extends where_expression {
    private like_options $options;

    public function __construct(
        private string $column,
        private string $value,
        private bool $negate = false,
        ?like_options $options = null,
    ) {
        if (is_null($options)) {
            $options = new like_options();
        }

        $this->options = $options;
    }

    public function get_sql(): string {
        global $DB;

        return $DB->sql_like(
            $this->column,
            '?',
            $this->options->casesensitive,
            $this->options->accentsensitive,
            $this->negate,
            $this->options->escapestring,
        );
    }


    public function get_params(): array {
        if ($this->options->escape) {
            global $DB;
            return [$DB->sql_like_escape($this->value, $this->options->escapestring)];
        }

        return [$this->value];
    }
}
