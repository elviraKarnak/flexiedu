<?php
/**
 * Database Utility.
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore tinyint longtext ldgp
 */

namespace LearnDash\Groups_Plus\Utility;

/**
 * Class Database
 */
class Database {
	public static $seat_tbl               = 'ldgp_seat_purchases';
	public static $organization_tbl       = 'ldgp_organization_purchases';
	public static $broadcast_messages_tbl = 'ldgp_broadcast_messages';

	/**
	 * Database constructor.
	 */
	function __construct() {
		if ( self::check_if_table_exists() ) {
			self::alter_db_table();
		}
	}

	/**
	 * If needed, add the my_organization_groups column to the Organization Purchases Table.
	 *
	 * @return  void
	 */
	public static function alter_db_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$organization_tbl;
		$db_name    = DB_NAME;

		if ( empty( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = %s AND COLUMN_NAME = 'my_organization_groups' AND TABLE_SCHEMA = %s", $table_name, $db_name ) ) ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
			$wpdb->query( "ALTER TABLE {$table_name} ADD COLUMN `my_organization_groups` text AFTER `my_organization_courses`" );
		}
	}

	/**
	 * Checks if the Seat Purchases Table exists.
	 *
	 * @return bool
	 */
	public static function check_if_table_exists() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$seat_tbl;

		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) === $table_name;
	}

	/**
	 *
	 */
	public static function reset_data() {
		global $wpdb;
		$wpdb->query( "TRUNCATE {$wpdb->prefix}" . self::$seat_tbl );
		$wpdb->query( "TRUNCATE {$wpdb->prefix}" . self::$organization_tbl );

	}

	/**
	 *
	 */
	public static function drop_tables() {
		global $wpdb;
		$wpdb->query( "DROP TABLE {$wpdb->prefix}" . self::$seat_tbl );
		$wpdb->query( "DROP TABLE {$wpdb->prefix}" . self::$organization_tbl );
	}

	/**
	 * Create custom database tables when necessary
	 *
	 * @return null
	 */
	public static function create_tables() {
		if ( self::check_if_table_exists() ) {
			return null;
		}
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->prefix}" . self::$seat_tbl . " (
		  			ID bigint(20) NOT NULL AUTO_INCREMENT,
					user_id bigint(20),
					order_id bigint(20),
					ld_primary_group_id bigint(20),
                    ld_secondary_group_id bigint(20),
					last_seats_count int(20),
                    purchase_datetime datetime NOT NULL,
					order_status varchar(100),
					PRIMARY KEY  (ID),
					INDEX user_id (user_id),
					INDEX ld_primary_group_id (ld_primary_group_id),
                    INDEX ld_secondary_group_id (ld_secondary_group_id)
		) $charset_collate;";

		dbDelta( $sql );

		$sql = "CREATE TABLE {$wpdb->prefix}" . self::$organization_tbl . " (
					ID bigint(20) NOT NULL AUTO_INCREMENT,
					user_id bigint(20),
					order_id bigint(20),
					ld_group_id bigint(20),
					organization_name varchar(1000),
					exclude_from_individual_seat_purchase tinyint(1),
					my_organization_courses text,
					my_organization_groups text,
					purchase_datetime datetime NOT NULL,
					order_status varchar(100),
					PRIMARY KEY  (ID),
					INDEX user_id (user_id)
		) $charset_collate;";

		dbDelta( $sql );

	}

	public static function add_seat_purchase( $attr ) {
		if ( ! self::check_if_table_exists() ) {
			self::create_tables();
		} else {
			self::alter_db_table();
		}

		global $wpdb;

		$now = current_time( 'mysql' );
		$wpdb->insert(
			$wpdb->prefix . self::$seat_tbl,
			array(
				'user_id'               => $attr['user_id'],
				'order_id'              => $attr['order_id'],
				'ld_primary_group_id'   => $attr['ld_primary_group_id'],
				'ld_secondary_group_id' => $attr['ld_secondary_group_id'],
				'purchase_datetime'     => $now,
				'last_seats_count'      => $attr['last_seats_count'],
				'order_status'          => $attr['order_status'],
			),
			array(
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%d',
				'%s',
			)
		);

		$seat_purchase_id = $wpdb->insert_id;

		return $seat_purchase_id;
	}

	public static function add_organization_purchase( $attr ) {
		if ( ! self::check_if_table_exists() ) {
			self::create_tables();
		} else {
			self::alter_db_table();
		}

		global $wpdb;

		$now = current_time( 'mysql' );
		$wpdb->insert(
			$wpdb->prefix . self::$organization_tbl,
			array(
				'user_id'                               => $attr['user_id'],
				'order_id'                              => $attr['order_id'],
				'ld_group_id'                           => $attr['organization_id'],
				'organization_name'                           => $attr['organization_name'],
				'exclude_from_individual_seat_purchase' => $attr['exclude_from_individual_seat_purchase'],
				'my_organization_courses'                     => $attr['my_organization_courses'] ?? '',
				'my_organization_groups'                      => $attr['my_organization_groups'] ?? '',
				'purchase_datetime'                     => $now,
				'order_status'                          => $attr['order_status'],
			),
			array(
				'%d',
				'%d',
				'%d',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);

		$organization_purchase_id = $wpdb->insert_id;

		return $organization_purchase_id;
	}

	public static function create_table_broadcast_message() {
		global $wpdb;
		$qry = "SHOW TABLES LIKE '{$wpdb->prefix}" . self::$broadcast_messages_tbl . "';";

		if ( $wpdb->query( $qry ) ) {
			return null;
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE  {$wpdb->prefix}" . self::$broadcast_messages_tbl . "  (
		  			ID bigint(20) NOT NULL AUTO_INCREMENT,
					group_id bigint(20) NOT NULL,
					group_leader_id bigint(20) NOT NULL,
					message longtext NOT NULL,
					message_datetime datetime NOT NULL,
					PRIMARY KEY (ID)
		) $charset_collate;";

		dbDelta( $sql );
	}

	public static function add_broadcast_message( $attr ) {

		// check and create table if not exists
		self::create_table_broadcast_message();

		global $wpdb;

		$now = current_time( 'mysql' );
		$wpdb->insert(
			$wpdb->prefix . self::$broadcast_messages_tbl,
			array(
				'group_id'         => $attr['group_id'],
				'group_leader_id'  => $attr['group_leader_id'],
				'message'          => $attr['message'],
				'message_datetime' => $now,
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
			)
		);

		return $wpdb->insert_id;
	}
}
