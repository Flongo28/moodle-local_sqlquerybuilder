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
 * The query_builder_test test class.
 *
 * @package     local_sqlquerybuilder
 * @category    test
 * @copyright   2025 Matthias Opitz <m.opitz@ucl.ac.uk>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class querybuilder_test extends \advanced_testcase {

    public function test_user_table_matches_moodle_db(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a few test users.
        $generator = $this->getDataGenerator();
        $generator->create_user(['username' => 'alice']);
        $generator->create_user(['username' => 'bob']);
        $generator->create_user(['username' => 'carol']);

        // Expected result using Moodle's DB API.
        $expected = $DB->get_records('user');

        // Actual result using our query builder.
        $actual = db::table('user')->get();

        // If your query builder returns associative array with ids as keys,
        // then we can compare directly. Otherwise, normalise both.
        $expectedids = array_keys($expected);
        $actualids   = array_keys($actual);

        sort($expectedids);
        sort($actualids);

        $this->assertEquals($expectedids, $actualids, 'User IDs must match');

        // Optionally compare the full records (cast to arrays if necessary).
        foreach ($expected as $id => $user) {
            $this->assertEqualsCanonicalizing(
                (array)$user,
                (array)$actual[$id],
                "Mismatch in record with id $id"
            );
        }
    }

    public function test_first_user_matches_moodle_db(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create some users.
        $generator = $this->getDataGenerator();
        $u1 = $generator->create_user(['username' => 'alice']);
        $u2 = $generator->create_user(['username' => 'bob']);
        $u3 = $generator->create_user(['username' => 'carol']);

        // Expected "first" record using Moodle DB API.
        $expected = $DB->get_record('user', [], '*', IGNORE_MULTIPLE);

        // Actual "first" record using query builder.
        $actual = db::table('user')->first();

        // Assert both are stdClass and have the same ID.
        $this->assertInstanceOf(\stdClass::class, $actual);
        $this->assertEquals($expected->id, $actual->id);

        // Optionally compare all fields.
        $this->assertEquals((array)$expected, (array)$actual);
    }

    public function test_find_user_by_id(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $user = $generator->create_user(['username' => 'david']);

        // Expected record using Moodle's DB API.
        $expected = $DB->get_record('user', ['id' => $user->id], '*', MUST_EXIST);

        // Actual record using query builder.
        $actual = db::table('user')->find($user->id);

        $this->assertInstanceOf(\stdClass::class, $actual);
        $this->assertEquals((array)$expected, (array)$actual);
    }

    public function test_find_returns_null_for_missing_id(): void {
        $this->resetAfterTest(true);

        $result = db::table('user')->find(999999);
        $this->assertFalse($result, 'Should return null when record not found');
    }

    public function test_where_clause_get(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $paul = $generator->create_user(['firstname' => 'Paul']);
        $john = $generator->create_user(['firstname' => 'John']);

        // Expected result using Moodle DB API.
        $expected = $DB->get_records('user', ['firstname' => 'Paul']);

        // Actual result using query builder.
        $actual = db::table('user')->where('firstname', '=', 'Paul')->get();

        // Compare IDs.
        $this->assertEquals(array_keys($expected), array_keys($actual));

        // Compare record content.
        foreach ($expected as $id => $user) {
            $this->assertEquals((array)$user, (array)$actual[$id]);
        }
    }

    public function test_where_clause_not_equal(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $paul = $generator->create_user(['firstname' => 'Paul']);
        $john = $generator->create_user(['firstname' => 'John']);

        // Expected result: all users where firstname <> 'Paul'.
        $expected = $DB->get_records_select('user', "firstname <> :name", ['name' => 'Paul']);

        // Actual result using query builder.
        $actual = db::table('user')->where('firstname', '<>', 'Paul')->get();

        // Compare IDs.
        $this->assertEquals(array_keys($expected), array_keys($actual));

        // Compare record content.
        foreach ($expected as $id => $user) {
            $this->assertEquals((array)$user, (array)$actual[$id]);
        }
    }

}
