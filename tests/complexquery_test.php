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

use core\clock;
use core\di;

/**
 * Testing the SQL generation
 *
 * @package     local_sqlquerybuilder
 * @category    test
 * @covers      \local_sqlquerybuilder\query
 * @copyright   2025 Matthias Opitz <m.opitz@ucl.ac.uk>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class complexquery_test extends \advanced_testcase {

    public function test_a_complex_query(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user1 = $generator->create_user(['firstname' => 'Paul']);
        $user2 = $generator->create_user(['firstname' => 'John']);

        // Enrol both users.
        $studentrole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);
        $manualplugin = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        $manualinstance = array_values(array_filter($enrolinstances, fn($e) => $e->enrol === 'manual'))[0];
        $manualplugin->enrol_user($manualinstance, $user1->id, $studentrole->id);
        $manualplugin->enrol_user($manualinstance, $user2->id, $studentrole->id);

        $now = di::get(clock::class)->time();

        // Expected result using raw SQL.
        $sql = "SELECT DISTINCT ue.userid
              FROM {enrol} e
              JOIN {user_enrolments} ue ON ue.enrolid = e.id
              JOIN {user} u ON u.id = ue.userid
             WHERE ue.status = 0
               AND u.deleted = 0
               AND u.suspended = 0
               AND (ue.timestart = 0 OR ue.timestart <= :now1)
               AND (ue.timeend = 0 OR ue.timeend >= :now2)";
        $params = [
            'now1' => $now,
            'now2' => $now,
        ];
        $expected = $DB->get_records_sql($sql, $params);

        // Actual result using query builder.
        $actual = db::table('enrol', 'e')
            ->distinct()
            ->join('user_enrolments', 'ue.enrolid', '=', 'e.id', 'ue')
            ->join('user', 'u.id', '=', 'ue.userid', 'u')
            ->select('ue.userid')
            ->where('ue.status', '=', 0)
            ->where('u.deleted', '=', 0)
            ->where('u.suspended', '=', 0)
            ->where('ue.timestart', '=', 0)
            ->orwhere('ue.timestart', '<=', $now)
            ->where('ue.timeend', '=', 0)
            ->orwhere('ue.timeend', '>=', $now)
            ->get();

        // Compare IDs returned.
        $expectedids = array_keys($expected);
        $actualids   = array_map(fn($r) => $r->userid, $actual);

        sort($expectedids);
        sort($actualids);

        $this->assertEquals($expectedids, $actualids, 'QueryBuilder must return the same enrolled user IDs as raw SQL');
    }

}
