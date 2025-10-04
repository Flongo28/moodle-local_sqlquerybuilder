<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_sqlquerybuilder\query;



/**
 * A class handles limit and offset
 *
 * @package   local_sqlquerybuilder
 * @copyright 2025 Daniel Meißner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pagination implements expression {
    private ?int $limit = null;
    private ?int $offset = null;

    public function limit(int $limit): void {
        $this->limit = $limit;
    }

    public function offset(int $offset): void {
        $this->offset = $offset;
    }

    public function page(int $pagecount, int $pagesize): void {
        $this->limit = $pagesize;
        $this->offset = $pagecount * $this->limit;
    }

    public function get_params(): array {
        return [];
    }

    public function get_sql(): string {
        $pagination = "";

        if (!is_null($this->limit)) {
            $pagination .= "LIMIT ". $this->limit;
        }

        if (!is_null($this->offset)) {
            $pagination .= "OFFSET ". $this->offset;
        }

        return $pagination;
    }
}
