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

namespace local_sqlquerybuilder\query\columns;

use local_sqlquerybuilder\query\expression;

/**
 * Raw column select
 *
 * @package local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_raw implements column_expression, expression {

    /**
     * Constructor
     *
     * @param string $sql raw sql column
     * @param mixed[] $params a list of params in the same order like the raw sql
     * @param bool $onlycolumn true if this should be the only column selected
     */
    public function __construct(
        private string $sql,
        private array $params,
        private bool $onlycolumn = false
    ) {}

    /**
     * Exports as sql
     *
     * @return string column for select as sql
     */
    public function get_sql(): string {
        return $this->sql;
    }

    /**
     * Exports as sql
     *
     * @return string column for select as sql
     */
    public function get_params(): array {
        return $this->params;
    }

    /**
     * Whether this should be the only column used
     *
     * @return bool Specified by programmer
     */
    public function standalone(): bool {
        return $this->onlycolumn;
    }
}
