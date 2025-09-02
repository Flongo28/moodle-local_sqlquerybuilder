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

namespace local_sqlquerybuilder;

use local_sqlquerybuilder\query;
use local_sqlquerybuilder\columns\column;

/**
 * Testing the SQL generation
 *
 * @package     local_sqlquerybuilder
 * @category    test
 * @copyright   2025 Daniel Meißner
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class query_test extends \advanced_testcase {

    public function test_a_simple_query(): void {
        $expected = "SELECT username FROM {user} WHERE suspended = 1";

        $q = new query('user');
        // TODO: we want nicer syntax: $q->select('username');
        $q->select([new column('username')]);
        $q->where('suspended', '=', 1);

        $actual = $q->to_sql();
        $this->assertEquals($expected, $actual);
    }
}
