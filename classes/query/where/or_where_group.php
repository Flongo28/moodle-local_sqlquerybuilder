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
 * Where expression
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025, Konrad Ebel <despair2400@proton.me>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class or_where_group extends where_expression {
    private array $whereclauses;


    public function __construct(
        where_expression ...$whereclauses
    ) {
        $this->whereclauses = $whereclauses;
    }

    public function add_clauses(where_expression ... $whereclauses): void {
        $this->whereclauses = array_merge($this->whereclauses, $whereclauses);
    }

    public function get_sql(): string {
        return implode(" OR ", $this->whereclauses);
    }

    public function get_params(): array {
        $params = array_map(fn (where_expression $expression) => $expression->get_params(), $this->whereclauses);
        return array_merge(...$params);
    }
}
