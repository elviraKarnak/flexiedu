<?php
/**
 * Bootstrapper for the plugin.
 *
 * Makes sure everything is good to go for loading the plugin, and then loads it.
 *
 * @since 1.2.0
 *
 * @package LearnDash_Gradebook
 */

defined( 'ABSPATH' ) || die;

/**
 * Class LearnDash_Gradebook_Bootstrapper
 *
 * Bootstrapper for the plugin.
 *
 * Makes sure everything is good to go for loading the plugin, and then loads it.
 *
 * @since 1.2.0
 */
class LearnDash_Gradebook_Bootstrapper {
	/**
	 * Notices to show if cannot load.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @var array
	 */
	private $notices = [];

	/**
	 * LearnDash_Gradebook_Bootstrapper constructor.
	 *
	 * @since 1.2.0
	 */
	function __construct() {
		// Dependencies that needs to be loaded before the plugin is loaded at `init` hook.
		require_once LEARNDASH_GRADEBOOK_DIR . 'core/library/rbm-field-helpers/rbm-field-helpers.php';

		add_action( 'init', [ $this, 'maybe_load' ], 1 );
	}

	/**
	 * Maybe loads the plugin.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function maybe_load() {
		$php_version = phpversion();
		$wp_version  = get_bloginfo( 'version' );

		// Minimum PHP version
		if ( version_compare( $php_version, '5.3.0' ) === - 1 ) {
			$this->notices[] = sprintf(
				// translators: %s is the currently installed version of PHP.
				_x( 'Minimum PHP version of 5.3.0 required. Current version is %s. Please contact your system administrator to upgrade PHP to its latest version.', 'The plugin was not able to be activated due to an incompatible PHP version. %s is the currently installed version of PHP', 'learndash-gradebook' ),
				$php_version
			);
		}

		// Minimum WordPress version
		if ( version_compare( $wp_version, '4.8.0' ) === - 1 ) {
			$this->notices[] = sprintf(
				_x( 'Minimum WordPress version of 4.8.0 required. Current version is %s. Please contact your system administrator to upgrade WordPress to its latest version.', 'The plugin was not able to be activated due to an incompatible WordPress version. %s is the currently installed version of WordPress', 'learndash-gradebook' ),
				$wp_version
			);
		}

		// LearnDash Activated
		if ( ! defined( 'LEARNDASH_VERSION' ) ) {
			$this->notices[] = _x( 'LearnDash LMS must be installed and activated.', 'The plugin was not able to be activated due to LearnDash not being installed.', 'learndash-gradebook' );
		}

		// LearnDash at version.
		if ( defined( 'LEARNDASH_VERSION' ) ) {
			// Pad in a Patch version if necessary
			$ld_version = ( substr_count( LEARNDASH_VERSION, '.' ) == 1 ) ? LEARNDASH_VERSION . '.0' : LEARNDASH_VERSION;

			if ( version_compare( $ld_version, '4.7.0' ) === -1 ) {
				$this->notices[] = sprintf(
					// translators: placeholder: %s is the currently installed version of LearnDash.
					_x( 'LearnDash LMS must be at least version 4.7.0. Current version is %s.', 'The plugin was not able to be activate due to an incompatible LearnDash version. %s is the currently installed version of LearnDash', 'learndash-gradebook' ),
					$ld_version
				);
			}
		}

		// Don't load and show errors if incompatible environment.
		if ( ! empty( $this->notices ) ) {
			add_action( 'admin_notices', [ $this, 'notices' ] );

			return;
		}

		$this->load();
	}

	/**
	 * Loads the plugin.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	private function load() {
		add_filter( 'default_option_ld_gb_gradebook_non_group_leaders_show_only_group_users', [ $this, 'force_enable_non_group_leaders_show_only_group_users' ], 10, 2 );
		add_filter( 'option_ld_gb_gradebook_non_group_leaders_show_only_group_users', [ $this, 'force_enable_non_group_leaders_show_only_group_users' ], 10, 2 );

		add_filter( 'default_option_ld_gb_gradebook_disable_sorting_by_grades_backend', [ $this, 'force_disable_sorting_by_grades_backend' ], 10, 2 );
		add_filter( 'option_ld_gb_gradebook_disable_sorting_by_grades_backend', [ $this, 'force_disable_sorting_by_grades_backend' ], 10, 2 );

		LearnDash_Gradebook();  }

	/**
	 * If the Constant is enabled to have non-Group Leaders only show their own Group Users within the Gradebook, force any reads to that option to show that it is enabled
	 *
	 * @param string $value   Value of the Option
	 * @param string $option  Name of the Option
	 *
	 * @access public
	 * @since 2.1.0
	 * @return string           Value of the Option
	 */
	public function force_enable_non_group_leaders_show_only_group_users( $value, $option ) {
		if ( ! defined( 'LD_GB_NON_GROUP_LEADERS_ONLY_OWN_GROUP_USERS' ) || ! LD_GB_NON_GROUP_LEADERS_ONLY_OWN_GROUP_USERS ) {
			return $value;
		}

		return 'yes';   }

	/**
	 * If the Constant is enabled to disable sorting by grades in the backend, force any reads to that option to show that it is enabled
	 *
	 * @param string $value   Value of the Option
	 * @param string $option  Name of the Option
	 *
	 * @access public
	 * @since 2.1.0
	 * @return string           Value of the Option
	 */
	public function force_disable_sorting_by_grades_backend( $value, $option ) {
		// Fallback for before migration runs. This way we ensure that the dashboard definitely loads if they have this setting enabled
		// This fallback will be removed on the next major update
		if ( ld_gb_get_option_field( 'gradebook_safe_mode' ) ) {
			return 'yes';
		}

		if ( ! defined( 'LD_GB_DISABLE_SORTING_BY_GRADES_BACKEND' ) || ! LD_GB_DISABLE_SORTING_BY_GRADES_BACKEND ) {
			return $value;
		}

		return 'yes';   }

	/**
	 * Shows notices on failure to load.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function notices() {
		?>
		<div class="notice error">
			<p>
				<?php
				printf(
					_x( '%1$sGradebook by LearnDash%2$s could not load because of the following errors:', 'Activation error messages list heading. %s are HTML tags for bolding the text', 'learndash-gradebook' ),
					'<strong>',
					'</strong>'
				);
				?>
			</p>

			<ul>
				<?php foreach ( $this->notices as $notice ) : ?>
					<li>
						&bull;&nbsp;<?php echo $notice; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}
