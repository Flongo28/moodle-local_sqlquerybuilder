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

use core\di;
use local_sqlquerybuilder\contracts\i_db;
use advanced_testcase;
use stdClass;

/**
 * The query_builder_test test class.
 *
 * @package     local_sqlquerybuilder
 * @category    test
 * @covers      \local_sqlquerybuilder\query
 * @copyright   2025 Matthias Opitz <m.opitz@ucl.ac.uk>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class querybuilder_test extends advanced_testcase {
    private i_db $db;
    private array $users = [];


    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);

        $this->db = di::get(i_db::class);

        $generator = $this->getDataGenerator();
        $this->users['muellerpaul'] = $generator->create_user(['username' => 'muellerpaul', 'firstname' => 'Paul']);
        $this->users['schneiderjohn'] = $generator->create_user(['username' => 'schneiderjohn', 'firstname' => 'John']);
    }


    public function test_user_table_matches_moodle_db(): void {
        global $DB;

        // Actual result using our query builder.
        $actual = $this->db->table('user')
            ->get();

        $this->assertCount(4, $actual);
        foreach ($this->users as $user) {
            $this->assertEquals($user, $actual[$user->id]);
        }
    }

    public function test_first_user_matches_moodle_db(): void {
        global $DB;

        // Actual "first" record using query builder.
        $actual = $this->db->table('user')->offset(2)->first();

        $this->assertInstanceOf(stdClass::class, $actual);
        $this->assertEquals($this->users['muellerpaul'], $actual);
    }

    public function test_find_user_by_id(): void {
        $john = $this->users['schneiderjohn'];

        // Actual record using query builder.
        $actual = $this->db->table('user')->find($john->id);

        $this->assertInstanceOf(stdClass::class, $actual);
        $this->assertEquals($john, $actual);
    }

    public function test_find_returns_null_for_missing_id(): void {
        $result = $this->db->table('user')->find(999999);
        $this->assertFalse($result, 'Should return false when record not found');
    }

    public function test_where_clause_get(): void {
        // Actual result using query builder.
        $actual = $this->db->table('user')->where('firstname', '=', 'Paul')->get();

        $paul = $this->users['muellerpaul'];
        $this->assertEquals([$paul->id => $paul], $actual);
    }

    public function test_where_clause_not_equal(): void {
        // Actual result using query builder.
        $actual = $this->db->table('user')->where('firstname', '<>', 'Paul')->get();

        // Compare record content.
        $john = $this->users['schneiderjohn'];
        $this->assertCount(3, $actual);
        $this->assertEquals($john, $actual[$john->id]);
    }

    public function test_from_query(): void {
        $subquery = $this->db->table('user', 'u')
            ->where('u.firstname', '=', 'Paul');

        $actual = $this->db->table($subquery, 'paul');
        $actual = $actual->first();

        $this->assertEquals($this->users['muellerpaul'], $actual);
    }
}
