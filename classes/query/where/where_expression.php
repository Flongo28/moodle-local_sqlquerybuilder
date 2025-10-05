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

use local_sqlquerybuilder\contracts\i_expression;

/**
 * Where expression
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025, Konrad Ebel <despair2400@proton.me>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class where_expression implements i_expression {
    public function get_params(): array {
        return [];
    }

    public function __toString() {
        return $this->get_sql();
    }
}
