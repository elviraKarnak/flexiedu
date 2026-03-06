<?php

namespace LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder;

use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\Aggregate;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\CRUD;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\FromClause;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\GroupByStatement;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\HavingClause;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\JoinClause;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\LimitStatement;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\MetaQuery;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\OffsetStatement;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\OrderByStatement;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\SelectStatement;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\TablePrefix;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\UnionOperator;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\Concerns\WhereClause;

/**
 * @since 1.0.0
 */
class QueryBuilder {
	use Aggregate;
	use CRUD;
	use FromClause;
	use GroupByStatement;
	use HavingClause;
	use JoinClause;
	use LimitStatement;
	use MetaQuery;
	use OffsetStatement;
	use OrderByStatement;
	use SelectStatement;
	use TablePrefix;
	use UnionOperator;
	use WhereClause;

	/**
	 * @return string
	 */
	public function getSQL() {
		$sql = array_merge(
			$this->getSelectSQL(),
			$this->getFromSQL(),
			$this->getJoinSQL(),
			$this->getWhereSQL(),
			$this->getGroupBySQL(),
			$this->getHavingSQL(),
			$this->getOrderBySQL(),
			$this->getLimitSQL(),
			$this->getOffsetSQL(),
			$this->getUnionSQL()
		);

		return $this->buildSQL($sql);
	}

	/**
	 * Generate the SQL for a DELETE query. Only the FROM, WHERE, ORDER BY, and LIMIT clauses are included.
	 * RETURNING is not supported.
	 * Note that aliases are supported only on MySQL >= 8.0.24 and MariaDB >= 11.6.
	 *
	 * @see https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/changing-deleting-data/delete
	 * @see https://dev.mysql.com/doc/refman/8.4/en/delete.html
	 *
	 * @return string DELETE query.
	 */
	public function deleteSQL() {
		$sql = array_merge(
			$this->getFromSQL(),
			$this->getWhereSQL(),
			$this->getOrderBySQL(),
			$this->getLimitSQL()
		);

		return 'DELETE ' . $this->buildSQL($sql);
	}

	/**
	 * Build the SQL query from the given parts.
	 *
	 * @param array $sql The SQL query parts.
	 *
	 * @return string SQL query.
	 */
	private function buildSQL( $sql ) {
		return str_replace(
			[ '   ', '  ' ],
			' ',
			implode( ' ', $sql )
		);
	}
}
