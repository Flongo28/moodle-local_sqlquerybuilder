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

namespace local_sqlquerybuilder\query\orderings;

use local_sqlquerybuilder\contracts\i_expression;

/**
 * Represents an ordering
 *
 * @package     local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ordering implements i_expression {
    /**
     * Constructor
     *
     * @param string $column Expressions to order by
     * @param bool $ascending Whether to filter ascending or descending
     */
    public function __construct(
        private string $column,
        private bool $ascending,
        private array $params = []
    ) {
    }

    /**
     * Exports as sql
     *
     * @return string order by as sql
     */
    public function get_sql(): string {
        if ($this->ascending) {
            return "$this->column ASC";
        } else {
            return "$this->column DESC";
        }
    }

    /**
     * Returns the used params
     *
     * @return array Params used in the expression
     */
    public function get_params(): array {
        return $this->params;
    }
}
