<?php
/**
 * Base class for upgrade modules.
 *
 * @since 1.1.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore ldgp WPINC
 */

namespace LearnDash\Groups_Plus\Module\Upgrade;

if ( ! defined( 'WPINC' ) ) {
    exit;
}

use LearnDash\Groups_Plus\lucatume\DI52\App;
use LearnDash\Groups_Plus\Module\Base as Module_Base;

/**
 * Base class for upgrade modules.
 * 
 * @since 1.1.0
 */
abstract class Base extends Module_Base {
    /**
     * Current version the upgrade is for.
     *
     * @var string
     */
    protected $to_version;

    /**
     * Option key name to store upgraded_to version.
     * 
     * @since 1.1.0
     *
     * @var string
     */
    private $option_name = 'ldgp_upgraded_to';

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Container for action hooks.
     * 
     * @since 1.1.0
     *
     * @return void
     */
    public function hook_actions(): void {
        add_action( 'admin_init', App::callback( $this, 'init' ), 10 );
    }

    /**
     * Abstract method to run upgrade.
     *
     * @since 1.1.0
     *
     * @return void
     */
    abstract protected function upgrade(): void;

    /**
     * Init upgrade check.
     * 
     * @since 1.1.0
     *
     * @return void
     */
    public function init(): void {
        if ( ! $this->was_upgraded() && $this->is_eligible() ) {
            $this->upgrade();

            $this->save_upgraded_version();
        }
    }

    /**
     * Check if site is eligible for upgrade in this class.
     *
     * @since 1.1.0
     *
     * @return boolean
     */
    private function is_eligible(): bool {
        return ! empty( $this->to_version ) && $this->to_version === LEARNDASH_GROUPS_PLUS_VERSION; 
    }

    /**
     * Check if the site has previously run this upgrade.
     * 
     * @since 1.1.0
     *
     * @return boolean
     */
    private function was_upgraded(): bool {
        return boolval( get_option( $this->option_name ) === $this->to_version );
    }

    /**
     * Save upgraded version in database.
     *
     * @since 1.1.0
     *
     * @return void
     */
    private function save_upgraded_version(): void {
        update_option( $this->option_name, $this->to_version, false );
    }
}
