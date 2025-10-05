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

use advanced_testcase;
use local_sqlquerybuilder\contracts\i_db;
use local_sqlquerybuilder\query\db;

/**
 * Testing the SQL generation
 *
 * @package     local_sqlquerybuilder
 * @category    test
 * @covers      \local_sqlquerybuilder\query
 * @copyright   2025 Daniel Meißner
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class sqlgeneration_test extends advanced_testcase {
    private i_db $db;


    public function setUp(): void {
        $this->db = new db();
    }

    /**
     * Test order by
     *
     * @return void
     */
    public function test_order_by(): void {
        $expected = "SELECT * FROM {users} WHERE deleted = ? ORDER BY email DESC, timecreated ASC";
        $expectedparams = [0];

        $actual = $this->db->table('users')
            ->where('deleted', '=', 0)
            ->order_desc('email')
            ->order_asc('timecreated');

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Test custom query from
     *
     * @return void
     */
    public function test_custom_query_from(): void {
        $expected = 'SELECT * FROM (VALUES ((SELECT * FROM {users} WHERE id = ?), (SELECT * FROM {entries} WHERE id = ?), \'Tryit\')) AS custom(a,b,tryit)';
        $expectedparams = [1, 2]; 

        $subquerya = $this->db->table('users')
            ->where('id', '=', 1);
        $subqueryb = $this->db->table('entries')
            ->where('id', '=', 2);

        $actual = $this->db->from_values([[$subquerya, $subqueryb, 'Tryit']], 'custom', ["a", "b", "tryit"]);

        $sql = $actual->get_sql();
        $sql = str_replace("\n", '', $sql);

        $this->assertEquals($expected, $sql);
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Tests if everything get selected if no calls where made
     *
     * @return void
     */
    public function test_no_select(): void {
        $expected = "SELECT * FROM {user}";
        $expectedparams = [];

        $actual = $this->db->table('user');

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Tests if selecting a count is possible
     *
     * @return void
     */
    public function test_count(): void {
        $expected = "SELECT COUNT(1) FROM {user}";
        $expectedparams = [];

        $actual = $this->db->table('user')
            ->select_count();

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Tests if selecting a sum is possible
     *
     * @return void
     */
    public function test_sum(): void {
        $expected = "SELECT SUM(suspended) AS count_suspended FROM {user}";
        $expectedparams = [];

        $actual = $this->db->table('user')
            ->select_sum('suspended', 'count_suspended');

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Tests if selecting a maximum is possible
     *
     * @return void
     */
    public function test_maximum(): void {
        $expected = "SELECT MAX(timecreated) AS lastcreated FROM {user}";
        $expectedparams = [];

        $actual = $this->db->table('user')
            ->select_max('timecreated', 'lastcreated');

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Tests if selecting a minimum is possible
     *
     * @return void
     */
    public function test_minimum(): void {
        $expected = "SELECT MIN(timecreated) AS firstcreated FROM {user}";
        $expectedparams = [];

        $actual = $this->db->table('user')
            ->select_min('timecreated', 'firstcreated');

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Test limit
     *
     * @return void
     */
    public function test_limit(): void {
        $expected = "SELECT * FROM {user} LIMIT 5";
        $expectedparams = [];

        $actual = $this->db->table('user')
            ->limit(5);

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    public function test_offset(): void {
        $expected = "SELECT * FROM {user} OFFSET 5";
        $expectedparams = [];

        $actual = $this->db->table('user')
            ->offset(5);

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Tests if multiple selects are possible
     *
     * @return void
     */
    public function test_multiple_selects(): void {
        $expected = "SELECT (username) AS uname, (email) AS mail, (deleted) AS d FROM {user}";
        $expectedparams = [];

        $actual = $this->db->table('user')
            ->select('username', 'uname')
            ->select('email', 'mail')
            ->select('deleted', 'd');

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Tests if the alias in selects is working
     *
     * @return void
     */
    public function test_alias(): void {
        $expected = "SELECT (username) AS uname FROM {user}";
        $expectedparams = [];

        $actual = $this->db->table('user')
            ->select('username', 'uname');

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Test a simple query
     *
     * @return void
     */
    public function test_a_simple_query(): void {
        $expected = "SELECT username FROM {user} WHERE suspended = ?";
        $expectedparams = [1];

        $actual = $this->db->table('user')
            ->select('username')
            ->where('suspended', '=', 1);

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Test simple query with alias from
     *
     * @return void
     */
    public function test_a_simple_query_with_from_alias(): void {
        $expected = "SELECT username FROM {user} u WHERE suspended = ?";
        $expectedparams = [1];

        $actual = $this->db->table('user', 'u')
            ->select('username')
            ->where('suspended', '=', 1);

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Test string in where clause is quoted
     *
     * @return void
     */
    public function test_that_a_string_in_a_where_clause_is_quoted(): void {
        $expected = "SELECT username FROM {user} WHERE username = ?";
        $expectedparams = ['Paul'];

        $actual = $this->db->table('user')
            ->select('username')
            ->where('username', '=', 'Paul');

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    public function test_simple_where_in_clause(): void {
        $expected = "SELECT * FROM {unknown} WHERE field IN (?,?,?)";
        $expectedparams = [1, 2, 3];

        $actual = $this->db->table('unknown')
            ->where_in('field', [1, 2, 3]);

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    public function test_subquery_where_in_clause(): void {
        $expected = "SELECT * FROM {unknown} WHERE field IN (SELECT id FROM {course} WHERE timestart > ?)";
        $expectedparams = [1];

        $latestcourses = $this->db->table('course')
            ->select('id')
            ->where('timestart', '>', 1);

        $actual = $this->db->table('unknown')
            ->where_in('field', $latestcourses);

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    public function test_from_with_subquery(): void {
        $expected = "SELECT * FROM (SELECT username FROM {user} u WHERE u.id = ?) AS thirduser";
        $expectedparams = [3];

        $subquery = $this->db->table('user', 'u')
            ->select('username')
            ->where('u.id', '=', 3);

        $actual = $this->db->table($subquery, 'thirduser');

        $sql = $actual->get_sql();
        $sql = str_replace("\n", '', $sql);
        $this->assertEquals($expected, $sql);
        $this->assertEquals($expectedparams, $actual->get_params());
    }

    /**
     * Test query with joins
     *
     * @return void
     */
    public function test_a_query_with_joins(): void {
        $expected = "SELECT * FROM {user} "
            . "JOIN {user_enrolments} ON user_enrolments.id = user.id";
        $expectedparams = [];

        $actual = $this->db->table('user')
            ->join('user_enrolments', ['user_enrolments.id', '=', 'user.id']);

        $this->assertEquals($expected, $actual->get_sql());
        $this->assertEquals($expectedparams, $actual->get_params());
    }
}
