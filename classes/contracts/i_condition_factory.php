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
 * Interface for creating conditions
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025 Konrad Ebel <despair2400@proton.me>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface i_condition_factory {
    /**
     * Creates an condition
     * 
     * needed cause moodle only instantiates one class in ci
     * the object is always static
     */
    public function create(): i_condition;
}
