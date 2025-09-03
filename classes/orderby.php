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

use local_sqlquerybuilder\orderings\ordering;

/**
 * Trait that builds a sql statement, that can be exported via
 * export_orderby()
 *
 * @copyright   Konrad Ebel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait orderby {
    protected $orderings = [];

    /**
     * Orders the query by the columns (ascending order)
     *
     * The first sort order
     *
     * @param string ...$columns
     * @return static Itself
     */
    public function order_asc(string ...$columns): static {
        foreach ($columns as $column) {
            $this->orderings[] = new ordering(
                $column,
                true
            );
        }

        return $this;
    }

    /**
     * Orders the query by the columns (descending order)
     *
     * The first sort order
     *
     * @param string ...$columns
     * @return static Itself
     */
    public function order_desc(string ...$columns): static {
        foreach ($columns as $column) {
            $this->orderings[] = new ordering(
                $column,
                false
            );
        }

        return $this;
    }

    /**
     * Deletes all orders
     *
     * @return static Itself
     */
    public function clear_order(): static {
        $this->orderings = [];
        return $this;
    }

    /**
     * Exports the “order by” part as sql
     *
     * Is an empty string if no columns are set
     *
     * @return string
     */
    protected function export_orderby(): string {
        if (empty($this->orderings)) {
            return '';
        }

        $formattedorderings = array_map(fn (ordering $order) => $order->export(), $this->orderings);

        return "ORDER BY " . implode(', ', $formattedorderings);
    }
}
