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

use local_sqlquerybuilder\contracts\i_query;

/**
 * Checks if the value is in the array
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025, Konrad Ebel <despair2400@proton.me>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class where_in extends where_expression {
    private string $insql;
    private array|i_query $params;


    public function __construct(
        private string $column,
        array|i_query $values,
        bool $negate = false,
    ) {
        global $DB;
        
        if (is_array($values)) {
            $inorequal = $DB->get_in_or_equal(
                $values,
                equal: !$negate,
                onemptyitems: true,
            );
            $this->insql = $inorequal[0];
            $this->params = $inorequal[1];
        } else {
            $this->insql = "IN ($values)";
            $this->params = $values->get_params();
        }
    }

    public function get_sql(): string {
        return "$this->column $this->insql";
    }


    public function get_params(): array {
        return $this->params;
    }
}
