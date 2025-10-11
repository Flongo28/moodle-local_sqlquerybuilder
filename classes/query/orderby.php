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

use local_sqlquerybuilder\contracts\i_expression;
use local_sqlquerybuilder\query\orderings\ordering;

/**
 * Trait that builds a sql statement, that can be exported via
 * export_orderby()
 *
 * @package     local_sqlquerybuilder
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class orderby implements i_expression {
    protected $orderings = [];

    public function order_asc(string ...$columns): static {
        foreach ($columns as $column) {
            $this->orderings[] = new ordering(
                $column,
                true
            );
        }

        return $this;
    }

    public function order_desc(string ...$columns): static {
        foreach ($columns as $column) {
            $this->orderings[] = new ordering(
                $column,
                false
            );
        }

        return $this;
    }

    public function clear_order(): static {
        $this->orderings = [];
        return $this;
    }

    public function get_sql(): string {
        if (empty($this->orderings)) {
            return '';
        }

        $formattedorderings = array_map(fn (ordering $order) => $order->get_sql(), $this->orderings);

        return ' ORDER BY ' . implode(', ', $formattedorderings);
    }

    public function get_params(): array {
        $params = array_map(fn (ordering $order) => $order->get_params(), $this->orderings);
        return array_merge(...$params);
    }
}
