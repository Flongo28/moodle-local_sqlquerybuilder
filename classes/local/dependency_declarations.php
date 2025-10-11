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

namespace local_sqlquerybuilder\local;

use core\hook\di_configuration;
use local_sqlquerybuilder\contracts\i_db;
use local_sqlquerybuilder\query\database;

class dependency_declarations {
    public static function configure_dependencies(di_configuration $hook): void {
        // Define i_db
        $hook->add_definition(
            id: i_db::class,
            definition: function (): i_db {
                return new database();
            }
        );
    }
}
