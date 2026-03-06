<?php
/**
 * Plugin Name: LearnDash LMS - Groups Plus
 * Plugin URI: https://www.learndash.com/extensions/groups-plus/
 * Description: Create Groups Plus without the need to sell them. This plugin manages LearnDash native Groups in the web site. Let your client organizations do all the organizational management work and view their details reports.
 * Version: 2.1.5
 * Requires PHP: 7.4
 * Requires at least: 6.6
 * Tested up to: 6.9.1
 * Author: LearnDash
 * Author URI: https://www.learndash.com/
 * Text Domain: learndash-groups-plus
 * Domain Path: /languages
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';

use LearnDash\Groups_Plus\lucatume\DI52\App;
use LearnDash\Groups_Plus\lucatume\DI52\Container;
use LearnDash\Groups_Plus\Plugin;
use LearnDash\Groups_Plus\Service_Provider\Upgrade as Upgrade_Provider;

define( 'LEARNDASH_GROUPS_PLUS_DIR', plugin_dir_path( __FILE__ ) );
define( 'LEARNDASH_GROUPS_PLUS_URL', plugins_url( '/', __FILE__ ) );
define( 'LEARNDASH_GROUPS_PLUS_VERSION', '2.1.5' );
define( 'LEARNDASH_GROUPS_PLUS_PLUGIN_FILE', __FILE__ );
define( 'LICENSING_SITE_URL', 'https://checkout.learndash.com/wp-json/learndash/v1/site/auth_token' );

if ( ! defined( 'LEARNDASH_GROUPS_PLUS_ITEM_REFERENCE' ) ) {
	define( 'LEARNDASH_GROUPS_PLUS_ITEM_REFERENCE', 'LearnDash LMS - Groups Plus' );
}

require_once LEARNDASH_GROUPS_PLUS_DIR . '/activation.php';

$ld_groups_plus_container = new Container();

// Register service providers.
$ld_groups_plus_container->register( Plugin::class );
$ld_groups_plus_container->register( Upgrade_Provider::class );

// Add the container globally.
App::setContainer( $ld_groups_plus_container );

add_action(
	'plugins_loaded',
	function () use ( $ld_groups_plus_container ) {
		$ld_groups_plus_container->boot();
	}
);
