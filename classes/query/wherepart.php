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

namespace local_sqlquerybuilder\query;

use core\clock;
use core\di;
use local_sqlquerybuilder\contracts\i_query;
use local_sqlquerybuilder\contracts\i_expression;
use local_sqlquerybuilder\query\where\like_options;
use local_sqlquerybuilder\query\where\where_column_comparison;
use local_sqlquerybuilder\query\where\where_expression;
use local_sqlquerybuilder\query\where\where_comparison;
use local_sqlquerybuilder\query\where\or_where_group;
use local_sqlquerybuilder\query\where\where_fulltext;
use local_sqlquerybuilder\query\where\where_is_null;
use local_sqlquerybuilder\query\where\where_in;
use local_sqlquerybuilder\query\where\where_like;

/**
 * Builds an where expression (Including WHERE itself).
 *
 * @package     local_sqlquerybuilder
 * @copyright   2025 Konrad Ebel <despair2400@proton.me>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wherepart extends condition {

    public function get_sql(): string {
        if (empty($this->conditionparts)) {
            return '';
        }

        $whereclause = ' WHERE ' . parent::get_sql();
        return $whereclause;
    }
}
