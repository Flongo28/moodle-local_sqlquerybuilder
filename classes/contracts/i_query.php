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
 * @method i_query limit(int $limit)                                   Limit the number of returned records.
 * @method i_query offset(int $offset)                                 Skip a specific number of records in the result set.
 * @method i_query page(int $pagecount, int $pagesize)                 Paginate results based on page number and size.
 * @method i_query where(string $column, string $operator, mixed $value) Add a WHERE condition comparing a column to a value (Never use for TEXT only VARCHAR).
 * @method i_query where_column(string $column, $operator, $othercolumn) Add a WHERE condition comparing two columns (Never use for TEXT only VARCHAR).
 * @method i_query or_where(string $column, string $operator, $value)   Add an OR WHERE condition comparing a column to a value (Never use for TEXT only VARCHAR).
 * @method i_query where_not(string $column, string $operator, $value)  Add a WHERE NOT condition (Never use for TEXT only VARCHAR).
 * @method i_query or_where_not(string $column, string $operator, $value) Add an OR WHERE NOT condition (Never use for TEXT only VARCHAR).
 * @method i_query where_fulltext(string $column, string $value)        Add a full-text search condition.
 * @method i_query where_fulltext_not(string $column, string $value)    Add a negated full-text search condition.
 * @method i_query where_like(string $column, string $value, like_options $options = null) Add a LIKE condition for partial matches.
 * @method i_query where_not_like(string $column, string $value, like_options $options = null) Add a NOT LIKE condition.
 * @method i_query where_null(string $column)                           Add a condition for the column should be NULL.
 * @method i_query or_where_null(string $column)                        Add an OR condition for the column should be NULL.
 * @method i_query where_notnull(string $column)                        Add a condition for a column shouldnt be NULL.
 * @method i_query or_where_notnull(string $column)                     Add an OR condition for a column shouldnt be NULL.
 * @method i_query where_in(string $column, array|i_query $values)      Add a WHERE IN condition.
 * @method i_query where_not_in(string $column, array|i_query $values)  Add a WHERE NOT IN condition.
 * @method i_query where_currently_active(string $columntimestart, string $columntimeend) Add a condition for current time to be between timestart and timeend column.
 * @method i_query select_all()                                         Select all columns.
 * @method i_query select(string $name, ?string $alias = null)          Select a specific column, optionally with an alias.
 * @method i_query select_count()                                       Select a COUNT(*) aggregate.
 * @method i_query select_max(string $name, ?string $alias = null)      Select the maximum value of a column.
 * @method i_query select_min(string $name, ?string $alias = null)      Select the minimum value of a column.
 * @method i_query select_sum(string $name, ?string $alias = null)      Select the sum of a column.
 * @method i_query distinct()                                           Select only distinct (unique) records.
 * @method i_query order_asc(string ...$columns)                        Order results ascending by one or more columns.
 * @method i_query order_desc(string ...$columns)                       Order results descending by one or more columns.
 * @method i_query clear_order()                                        Remove any ORDER BY clauses.
 * @method i_query join(string|i_query $table, $conditions, string $alias = '') Join another table with INNER JOIN.
 * @method i_query left_join(string|i_query $table, $conditions, string $alias = '') Join another table with LEFT JOIN.
 * @method i_query right_join(string|i_query $table, $conditions, string $alias = '') Join another table with RIGHT JOIN.
 * @method i_query full_join(string|i_query $table, $conditions, string $alias = '') Join another table with FULL OUTER JOIN.
 * @method i_query group_by(string ...$column)                           Group results by one or more columns.
 * @method i_query having(string $column, string $operator, mixed $value) Add a HAVING condition for grouped queries.
 * @method i_query or_having(string $column, string $operator, mixed $value) Add an OR HAVING condition for grouped queries.
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
