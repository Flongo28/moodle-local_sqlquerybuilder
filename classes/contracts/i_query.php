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

namespace local_sqlquerybuilder\contracts;

use dml_exception;
use stdClass;

/**
 * The query builder interface
 *
 * @package   local_sqlquerybuilder
 * @copyright 2025 Konrad Ebel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * @method i_query limit(int $limit)
 * @method i_query offset(int $offset)
 * @method i_query page(int $pagecount, int $pagesize)
 * @method i_query where(string $column, string $operator, mixed $value)
 * @method i_query where_column(string $column, $operator, $othercolumn)
 * @method i_query or_where(string $column, string $operator, $value, bool $negate = false)
 * @method i_query where_not(string $column, string $operator, $value)
 * @method i_query or_where_not(string $column, string $operator, $value)
 * @method i_query where_fulltext(string $column, string $value)
 * @method i_query where_fulltext_not(string $column, string $value)
 * @method i_query where_like(string $column, string $value, like_options $options = null)
 * @method i_query where_not_like(string $column, string $value, like_options $options = null)
 * @method i_query where_null(string $column)
 * @method i_query or_where_null(string $column)
 * @method i_query where_notnull(string $column)
 * @method i_query or_where_notnull(string $column)
 * @method i_query where_in(string $column, array|i_query $values, bool $negate = false)
 * @method i_query where_not_in(string $column, array|i_query $values)
 * @method i_query where_currently_active(string $columntimestart, string $columntimeend)
 * @method i_query select_all()
 * @method i_query select(string $name, ?string $alias = null)
 * @method i_query select_count()
 * @method i_query select_max(string $name, ?string $alias = null)
 * @method i_query select_min(string $name, ?string $alias = null)
 * @method i_query select_sum(string $name, ?string $alias = null)
 * @method i_query distinct()
 * @method i_query order_asc(string ...$columns)
 * @method i_query order_desc(string ...$columns)
 * @method i_query clear_order()
 * @method i_query join(string $table, $conditions, string $alias = '')
 * @method i_query leftjoin(string $table, $conditions, string $alias = '')
 * @method i_query rightjoin(string $table, $conditions, string $alias = '')
 * @method i_query fulljoin(string $table, $conditions, string $alias = '')
 * @method i_query joinsub(i_query $query, $conditions, string $alias)
 * @method i_query leftjoinsub(i_query $query, $conditions, string $alias)
 * @method i_query rightjoinsub(i_query $query, $conditions, string $alias)
 * @method i_query crossjoin(string $table, $conditions, string $alias = '')
 * @method i_query groupby(string ...$column)
 * @method i_query having(string $column, string $operator, mixed $value)
 * @method i_query orhaving(string $column, string $operator, mixed $value)
 */
interface i_query extends i_expression {
    /**
     * Get multiple entries from the query
     *
     * @return stdClass[] Entries from the database call
     * @throws dml_exception Database is not reachable
     */
    public function get(): array;

    /**
     * Get the first entry from the query
     *
     * @return stdClass|false An entry if found one
     * @throws dml_exception Database is not reachable
     */
    public function first(): stdClass|false;

    /**
     * Returns the entry searched id
     *
     * @param int $id Search ID
     * @return stdClass|false An entry if found one
     * @throws dml_exception Database is not reachable
     */
    public function find(int $id): stdClass|false;
}
