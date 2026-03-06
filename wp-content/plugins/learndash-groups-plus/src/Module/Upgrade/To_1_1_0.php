<?php
/**
 * 1.1.0 upgrade module.
 *
 * @since 1.1.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore WPINC
 */

namespace LearnDash\Groups_Plus\Module\Upgrade;

if ( ! defined( 'WPINC' ) ) {
    exit;
}

use LearnDash\Groups_Plus\Module\Upgrade\Base as Upgrade_Base;
use LearnDash\Groups_Plus\Utility\Database;

/**
 * Upgrade module to v1.0.0.
 * 
 * @since 1.1.0
 */
class To_1_1_0 extends Upgrade_Base {
    /**
     * Plugin version where the upgrade is for.
     *
     * @since 1.1.0
     *
     * @var string
     */
    protected $to_version = '1.1.0';

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Abstract method to run upgrade.
     *
     * @since 1.1.0
     *
     * @return void
     */
    protected function upgrade(): void {
        $this->rename_db_tables();
    }

    /**
     * Rename database tables.
     *
     * @since 1.1.0
     *
     * @return void
     */
    private function rename_db_tables(): void {
        global $wpdb;

        $old_tables = array(
            $wpdb->prefix . 'broadcast_messages',
            $wpdb->prefix . 'seat_purchases',
            $wpdb->prefix . 'organization_purchases',
        );

        $new_tables = array(
            $wpdb->prefix . Database::$broadcast_messages_tbl,
            $wpdb->prefix . Database::$seat_tbl,
            $wpdb->prefix . Database::$organization_tbl,
        );
        
        foreach ( $old_tables as $key => $old_table ) {
            if ( $this->table_exists( $old_table ) ) {
                $wpdb->query( "RENAME TABLE {$old_table} TO {$new_tables[ $key ]}" );
            }
        }
    }

    /**
     * Check if a table exists.
     *
     * @since 1.1.0
     *
     * @param string $table Table name.
     * @return bool
     */
    private function table_exists( string $table ): bool {
        global $wpdb;

        return boolval( $wpdb->query( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) ) );
    }
}
