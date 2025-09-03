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

use local_sqlquerybuilder\db;
use local_sqlquerybuilder\columns\column;

/**
 * Testing the SQL generation
 *
 * @package     local_sqlquerybuilder
 * @category    test
 * @covers      \local_sqlquerybuilder\query
 * @copyright   2025 Daniel Meißner
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class sqlgeneration_test extends \advanced_testcase {
    /**
     * Test custom query from
     *
     * @return void
     */
    public function test_custom_query_from(): void {
        $expected = 'SELECT * FROM VALUES(((SELECT * FROM {users} WHERE id = 1),
                      (SELECT * FROM {entries} WHERE id = 2), ("Tryit")))';

        $subquerya = db::table('users')
            ->where('id', '=', 1);
        $subqueryb = db::table('entries')
            ->where('id', '=', 2);

        $actual = db::from_values([[$subquerya, $subqueryb, '"Tryit"']])->to_sql();
        $actual = str_replace("\n", '', $actual);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests if everything get selected if no calls where made
     *
     * @return void
     */
    public function test_no_select(): void {
        $expected = "SELECT * FROM {user}";

        $actual = db::table('user')
            ->to_sql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests if selecting a count is possible
     *
     * @return void
     */
    public function test_count(): void {
        $expected = "SELECT COUNT(1) FROM {user}";

        $actual = db::table('user')
            ->select_count()
            ->to_sql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests if selecting a sum is possible
     *
     * @return void
     */
    public function test_sum(): void {
        $expected = "SELECT SUM(suspended) AS count_suspended FROM {user}";

        $actual = db::table('user')
            ->select_sum('suspended', 'count_suspended')
            ->to_sql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests if selecting a maximum is possible
     *
     * @return void
     */
    public function test_maximum(): void {
        $expected = "SELECT MAX(timecreated) AS lastcreated FROM {user}";

        $actual = db::table('user')
            ->select_max('timecreated', 'lastcreated')
            ->to_sql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests if selecting a minimum is possible
     *
     * @return void
     */
    public function test_minimum(): void {
        $expected = "SELECT MIN(timecreated) AS firstcreated FROM {user}";

        $actual = db::table('user')
            ->select_min('timecreated', 'firstcreated')
            ->to_sql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests if multiple selects are possible
     *
     * @return void
     */
    public function test_multiple_selects(): void {
        $expected = "SELECT (username) AS uname, (email) AS mail, (deleted) AS d FROM {user}";

        $actual = db::table('user')
            ->select('username', 'uname')
            ->select('email', 'mail')
            ->select('deleted', 'd')
            ->to_sql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests if the alias in selects is working
     *
     * @return void
     */
    public function test_alias(): void {
        $expected = "SELECT (username) AS uname FROM {user}";

        $actual = db::table('user')
            ->select('username', 'uname')
            ->to_sql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test a simple query
     *
     * @return void
     */
    public function test_a_simple_query(): void {
        $expected = "SELECT username FROM {user} WHERE suspended = 1";

        $actual = db::table('user')
            ->select('username')
            ->where('suspended', '=', 1)
            ->to_sql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test simple query with alias from
     *
     * @return void
     */
    public function test_a_simple_query_with_from_alias(): void {
        $expected = "SELECT username FROM {user} u WHERE suspended = 1";

        $actual = db::table('user', 'u')
            ->select('username')
            ->where('suspended', '=', 1)
            ->to_sql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test string in where clause is quoted
     *
     * @return void
     */
    public function test_that_a_string_in_a_where_clause_is_quoted(): void {
        $expected = "SELECT username FROM {user} WHERE username = 'Paul'";

        $actual = db::table('user')
            ->select('username')
            ->where('username', '=', 'Paul')
            ->to_sql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test query with joins
     *
     * @return void
     */
    public function test_a_query_with_joins(): void {
        $expected = "SELECT * FROM {user} "
            . "JOIN {user_enrolments} ON user_enrolments.id = user.id";

        $actual = db::table('user')
            ->join('user_enrolments', 'user_enrolments.id', '=', 'user.id')
            ->to_sql();

        $this->assertEquals($expected, $actual);
    }
}
