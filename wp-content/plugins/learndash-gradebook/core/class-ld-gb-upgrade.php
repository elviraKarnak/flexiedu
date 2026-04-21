<?php
/**
 * Handles plugin upgrades.
 *
 * @since 1.2.0
 *
 * @package LearnDash_Gradebook
 */

defined( 'ABSPATH' ) || die();

use LearnDash\Gradebook\WPTRT\AdminNotices\Notices;

/**
 * Class LD_GB_Upgrade
 *
 * Handles plugin upgrades.
 *
 * @since 1.2.0
 */
class LD_GB_Upgrade {
	/**
	 * Used to generate Admin Notices
	 *
	 * @since 4.2.0
	 *
	 * @var \WPTRT\AdminNotices\Notices;
	 */
	private $notices;

	/**
	 * LD_GB_Upgrade constructor.
	 *
	 * @since 1.2.0
	 *
	 * @return bool True if needs to upgrade, false if does not.
	 */
	function __construct() {
		$this->notices = new Notices();

		add_action( 'admin_init', [ $this, 'check_upgrades' ] );

		if ( isset( $_GET['ld_gb_upgrade'] ) ) {
			add_action( 'admin_init', [ $this, 'do_upgrades' ] );
		}

		add_action( 'admin_init', [ $this, 'display_notices' ], 11 );

		add_action( 'wp_ajax_wptrt_dismiss_notice', [ $this, 'delete_persistent_notice' ], 11 );
	}

	/**
	 * Checks for upgrades and migrations.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function check_upgrades() {
		$last_upgrade = get_option( 'ld_gb_last_upgrade', 0 );

		// If LD GB Version isn't at 2.0.0 or higher but Last Upgrade is, assume bad upgrade script
		if ( $last_upgrade == '2.0.0' &&
			version_compare( LEARNDASH_GRADEBOOK_VERSION, '2.0.0' ) === -1 ) {
			$last_upgrade = '1.2.0';
			update_option( 'ld_gb_last_upgrade', $last_upgrade );
		}

		foreach ( $this->get_upgrades() as $upgrade_version => $upgrade_callback ) {
			if ( version_compare( $last_upgrade, $upgrade_version ) === - 1 ) {
				if ( in_array( $upgrade_version, $this->get_no_prompt_upgrades() ) ) {
					try {
						call_user_func( $upgrade_callback );
						update_option( 'ld_gb_last_upgrade', $upgrade_version );
						update_option( 'ld_gb_last_upgraded', time() );
					} catch ( Exception $exception ) {
						$this->save_notice(
							[
								'id'      => $exception->getCode() !== 403 ? 'upgrade_error' : 'auth_error',
								'title'   => false,
								'message' => $exception->getMessage(),
								'options' => [
									'type' => 'error',
								],
							]
						);

						break;
					}
				} else {
					$this->notices->add(
						'upgrade_needed',
						false,
						'' .
						__( 'Gradebook by LearnDash needs to upgrade the database. It is strongly recommended you backup your database first.', 'learndash-gradebook' ) .
						' <a href="' . add_query_arg(
							'ld_gb_upgrade',
							'1'
						) . '">' .
							_x( 'Upgrade', 'Run database upgrade button text', 'learndash-gradebook' ) .
						'</a>',
						[
							'type' => 'warning',
						]
					);

					break;
				}
			}
		}

		if ( isset( $_REQUEST['ld_gb_upgraded'] ) && $_REQUEST['ld_gb_upgraded'] ) {
			$this->notices->add(
				'upgraded',
				false,
				__( 'Gradebook by LearnDash has successfully upgraded!', 'learndash-gradebook' ),
				[
					'type' => 'success',
				]
			);
		}   }

	/**
	 * Runs upgrades.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function do_upgrades() {
		$last_upgrade = get_option( 'ld_gb_last_upgrade', 0 );

		$all_upgrades = $this->get_upgrades();

		// Allow retriggering upgrade routines starting from a specific version.
		if ( array_key_exists( $_GET['ld_gb_upgrade'], $all_upgrades ) ) {
			while ( key( $all_upgrades ) !== $_GET['ld_gb_upgrade'] ) {
				next( $all_upgrades );
			}

			// Reset array pointer to the previous value
			prev( $all_upgrades );
			$last_upgrade = key( $all_upgrades );
		}

		try {
			foreach ( $this->get_upgrades() as $upgrade_version => $upgrade_callback ) {
				if ( version_compare( $last_upgrade, $upgrade_version ) === - 1 ) {
					call_user_func( $upgrade_callback );
					update_option( 'ld_gb_last_upgrade', $upgrade_version );
					update_option( 'ld_gb_last_upgraded', time() );
				}
			}

			self::clear_notices( 'upgrade_error' );

			wp_safe_redirect( admin_url( 'index.php?ld_gb_upgraded=true' ) );
			exit();
		} catch ( Exception $exception ) {
			$this->save_notice(
				[
					'id'      => $exception->getCode() !== 403 ? 'upgrade_error' : 'auth_error',
					'title'   => false,
					'message' => $exception->getMessage(),
					'options' => [
						'type' => 'error',
					],
				]
			);
		}   }

	/**
	 * Returns an array of all versions that require an upgrade.
	 *
	 * @since 1.2.2
	 * @access private
	 *
	 * @return array
	 */
	function get_upgrades() {
		return [
			'1.2.0' => [ $this, 'upgrade_1_2_0' ],
			'1.3.7' => [ $this, 'upgrade_1_3_7' ],
			'1.4.0' => [ $this, 'upgrade_1_4_0' ],
			'2.0.0' => [ $this, 'upgrade_2_0_0' ],
			'2.0.4' => [ $this, 'upgrade_2_0_4' ],
			'2.1.0' => [ $this, 'upgrade_2_1_0' ],
			'3.0.0' => [ $this, 'upgrade_3_0_0' ],
			'4.2.0' => [ $this, 'upgrade_4_2_0' ],
			'4.3.0' => [ $this, 'upgrade_4_3_0' ],
		];
	}

	/**
	 * These upgrades will not require a prompt from the user and will run immediately
	 *
	 * @since 4.2.0
	 * @access private
	 * @return array
	 */
	function get_no_prompt_upgrades() {
		return [
			'4.2.0',
			'4.3.0',
		];  }

	/**
	 * Clears saved Notices under a certain key
	 *
	 * @param string $key  Notice Key
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public static function clear_notices( string $key = 'upgrade_error' ): void {
		$saved_notices = get_option( 'ld_gb_notices' );
		$saved_notices = json_decode( $saved_notices, true );

		if ( ! isset( $saved_notices[ $key ] ) ) {
			return;
		}

		unset( $saved_notices[ $key ] );

		update_option( 'ld_gb_notices', json_encode( $saved_notices ) );

		// Remove hide flag as we may reuse this ID in the future. Additionally, non-persistent Notices would store a flag here which isn't especially useful.
		delete_option( "ld_gb_{$key}" );    }

	/**
	 * Save Admin Notices to persist across page loads (until dismissed)
	 *
	 * @param array $notice  \WPTRT\AdminNotices\Notices->add() arguments as an associative array
	 *
	 * @since 4.2.0
	 * @return void
	 */
	public function save_notice( $notice ) {
		$notice = wp_parse_args(
			$notice,
			[
				'id'      => '',
				'title'   => false,
				'message' => '',
				'options' => [],
			]
		);

		$notice['options'] = wp_parse_args(
			$notice['options'],
			[
				'option_prefix' => 'ld_gb',
			]
		);

		if ( ! $notice['id'] || ! $notice['message'] ) {
			return;
		}

		$saved_notices = get_option( 'ld_gb_notices' );

		$saved_notices = json_decode( $saved_notices, true );

		if ( empty( $saved_notices ) ) {
			$saved_notices = [];
		}

		$saved_notices[ $notice['id'] ] = [
			'id'      => $notice['id'],
			'title'   => $notice['title'],
			'message' => $notice['message'],
			'options' => $notice['options'],
		];

		update_option( 'ld_gb_notices', json_encode( $saved_notices ) );    }

	/**
	 * Output all single load and persistent notices
	 *
	 * @access public
	 * @since 4.2.0
	 * @return void
	 */
	public function display_notices() {
		$saved_notices = get_option( 'ld_gb_notices' );

		$saved_notices = json_decode( $saved_notices, true );

		if ( empty( $saved_notices ) ) {
			$saved_notices = [];
		}

		foreach ( $saved_notices as $notice ) {
			$notice = wp_parse_args(
				$notice,
				[
					'id'      => '',
					'title'   => false,
					'message' => '',
					'options' => [],
				]
			);

			$this->notices->add(
				$notice['id'],
				$notice['title'],
				$notice['message'],
				$notice['options']
			);
		}

		$this->notices->boot(); }

	/**
	 * Ensure that persistent notices get removed from our option once dismissed
	 *
	 * @access public
	 * @since 4.2.0
	 * @return void
	 */
	public function delete_persistent_notice() {
		if ( ! isset( $_POST['id'] ) ) {
			return;
		}

		$id = $_POST['id'];

		check_ajax_referer( 'wptrt_dismiss_notice_' . $id, 'nonce', true );

		self::clear_notices( $id ); }

	/**
	 * 1.2.0 upgrade script.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @global WPDB $wpdb
	 */
	function upgrade_1_2_0() {
		global $wpdb;

		$term_rows = $wpdb->get_results(
			"
			SELECT * FROM {$wpdb->prefix}term_taxonomy
			WHERE taxonomy = 'gradebook-type'
			"
		);

		// If Types don't exist, no need to migrate
		if ( is_wp_error( $term_rows ) || empty( $term_rows ) ) {
			return;
		}

		// Also, make sure there are no Gradebooks yet, that's not right
		$gradebooks = get_posts(
			[
				'post_type'   => 'gradebook',
				'numberposts' => - 1,
				'post_status' => 'any',
			]
		);

		if ( ! empty( $gradebooks ) ) {
			return;
		}

		// Migrate license key option
		$old_license = get_option( 'ld_gb_license_key' );
		if ( $old_license ) {
			update_option( 'learndash_gradebook_license_key', $old_license );
			update_option( 'learndash_gradebook_license_status', get_option( 'ld_gb_license_status', '' ) );
			delete_option( 'ld_gb_license_key' );
		}

		// Register so we can get them to use and then delete
		register_taxonomy(
			'gradebook-type',
			[ 'sfwd-quiz', 'sfwd-assignment' ],
			[
				'public'            => false,
				'show_ui'           => false,
				'hierarchical'      => true,
				'show_in_menu'      => false,
				'show_in_nav_menus' => false,
				'show_tagcloud'     => false, // cspell: disable-line.
			]
		);

		$gradebook_ID = wp_insert_post(
			[
				'post_type'   => 'gradebook',
				'post_status' => 'publish',
				'post_title'  => 'Gradebook',
			]
		);

		// Migrate Types to Components
		$types = get_terms(
			[
				'taxonomy'   => 'gradebook-type',
				'hide_empty' => false,
			]
		);

		// Should not be possible due to above DB check, but gotta be sure
		if ( ! $types ) {
			return;
		}

		$components = [];
		foreach ( $types as $component_ID => $type ) {
			$component = [
				'id'              => $component_ID,
				'name'            => $type->name,
				'weight'          => get_term_meta( $type->term_id, 'weight', true ),
				'lessons_all'     => '',
				'topics_all'      => '',
				'quizzes_all'     => '',
				'assignments_all' => '',
				'lessons'         => [],
				'topics'          => [],
				'quizzes'         => [],
				'assignments'     => [],
			];

			$posts = get_posts(
				[
					'post_type'   => [ 'sfwd-assignment', 'sfwd-quiz' ],
					'numberposts' => - 1,
					'tax_query'   => [
						[
							'taxonomy' => 'gradebook-type',
							'field'    => 'id',
							'terms'    => $type->term_id,
						],
					],
				]
			);

			foreach ( $posts as $post ) {
				switch ( $post->post_type ) {
					case 'sfwd-assignment':
						$component['assignments'][] = $post->ID;
						break;

					case 'sfwd-quiz':
						$component['quizzes'][] = $post->ID;
						break;
				}

				// Migrate grade statuses
				$wpdb->get_results(
					"
                    UPDATE {$wpdb->prefix}usermeta
                    SET meta_key = 'ld_gb_grade_status_{$gradebook_ID}_{$post->ID}'
                    WHERE meta_key = 'ld_gb_grade_status_{$post->ID}'
                    "
				);
			}

			$components[] = $component;

			// Migrate manual grades
			$wpdb->get_results(
				"
                UPDATE {$wpdb->prefix}usermeta
                SET meta_key = 'ld_gb_manual_grades_{$gradebook_ID}_{$component_ID}' 
                WHERE meta_key = 'ld_gb_manual_grades_{$type->term_id}'
                "
			);

			wp_delete_term( $type->term_id, 'gradebook-type' );
		}

		// Migrate component grades
		$wpdb->get_results(
			"
            UPDATE {$wpdb->prefix}usermeta
            SET meta_key = 'ld_gb_component_grades_{$gradebook_ID}'
            WHERE meta_key = 'ld_gb_component_grades'
            "
		);

		$component_override_results = $wpdb->get_results(
			"
            SELECT * FROM {$wpdb->prefix}usermeta
            WHERE meta_key = 'ld_gb_component_grades_{$gradebook_ID}'
            "
		);

		if ( $component_override_results ) {
			$types_map     = wp_list_pluck( $types, 'term_id' );
			$new_overrides = [];
			foreach ( $component_override_results as $result ) {
				$component_overrides = maybe_unserialize( $result->meta_value );

				foreach ( $component_overrides as $type_ID => $override ) {
					$new_overrides[ array_search( $type_ID, $types_map ) ] = $override;
				}

				update_user_meta( $result->user_id, "ld_gb_component_grades_{$gradebook_ID}", $new_overrides );
			}
		}

		$is_weighted = ld_gb_get_option_field( 'weight_type' ) === 'weighted';
		delete_option( 'ld_gb_weight_type' );

		update_post_meta( $gradebook_ID, 'ld_gb_gradebook_weighting_enable', $is_weighted ? '1' : '' );
		update_post_meta( $gradebook_ID, 'ld_gb_components', $components );
		update_post_meta( $gradebook_ID, 'last_component_id', $component_ID );

		// Assignment grades to new points system
		$assignments = get_posts(
			[
				'post_type'    => 'sfwd-assignment',
				'numberposts'  => - 1,
				'post_status'  => 'any',
				'meta_key'     => 'assignment_grade',
				'meta_compare' => 'EXISTS',
			]
		);

		if ( $assignments ) {
			foreach ( $assignments as $assignment ) {
				$grade = (int) get_post_meta( $assignment->ID, 'assignment_grade', true );

				if ( $grade === false || $grade === null || $grade === '' ) {
					continue;
				}

				$lesson_ID      = get_post_meta( $assignment->ID, 'lesson_id', true );
				$points_enabled = learndash_get_setting( $lesson_ID, 'lesson_assignment_points_enabled' );
				$points_amount  = (int) learndash_get_setting( $lesson_ID, 'lesson_assignment_points_amount' );

				if ( $points_enabled !== 'on' ) {
					learndash_update_setting( $lesson_ID, 'lesson_assignment_points_enabled', 'on' );
				}

				if ( ! $points_amount ) {
					$points_amount = 100;
					learndash_update_setting( $lesson_ID, 'lesson_assignment_points_amount', $points_amount );
				}

				update_post_meta( $assignment->ID, 'points', $points_amount / 100 * round( $grade ) );

				delete_post_meta( $assignment->ID, 'assignment_grade' );
			}
		}
	}

	/**
	 * 1.3.7 upgrade script.
	 *
	 * @since 1.3.7
	 * @access private
	 */
	function upgrade_1_3_7() {
		// Fix edit_others_gradebooks not being granted to Administrators
		LD_GB_Install::setup_capabilities();    }

	/**
	 * 1.4.0 upgrade script.
	 *
	 * @since 1.4.0
	 * @access private
	 */
	function upgrade_1_4_0() {
		// Fix view_gradebook possibly being revoked from Group Leaders
		LD_GB_Install::setup_capabilities();    }

	/**
	 * 2.0.0 upgrade script
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function upgrade_2_0_0() {
		$letter_grade_scale = ld_gb_get_option_field( 'letter_grade_scale', [] );

		$migrated_letter_grade_scale = [];

		// The Grade Cutoff was used as an index in the old version
		foreach ( $letter_grade_scale as $grade => $letter ) {
			// No need to migrate. The data is already in the right format.
			if ( is_array( $letter ) ) {
				break;
			}

			$migrated_letter_grade_scale[] = [
				'grade'  => $grade,
				'letter' => $letter,
			];
		}

		if ( ! empty( $migrated_letter_grade_scale ) ) {
			update_option( 'ld_gb_letter_grade_scale', $migrated_letter_grade_scale );
		}

		$color_grade_scale          = ld_gb_get_option_field( 'grade_color_scale', [] );
		$migrated_color_grade_scale = [];

		// The Grade Cutoff was used as an index in the old version
		foreach ( $color_grade_scale as $grade => $color ) {
			// No need to migrate. The data is already in the right format.
			if ( is_array( $color ) ) {
				break;
			}

			$migrated_color_grade_scale[] = [
				'grade' => $grade,
				'color' => $color,
			];
		}

		if ( ! empty( $migrated_color_grade_scale ) ) {
			update_option( 'ld_gb_grade_color_scale', $migrated_color_grade_scale );
		}   }

	/**
	 * 2.0.4 upgrade script
	 *
	 * @since 2.0.4
	 * @access private
	 */
	function upgrade_2_0_4() {
		$user_columns = ld_gb_get_option_field( 'user_columns', [] );

		if ( ! $user_columns ) {
			return;
		}

		$user_columns = array_map(
			function ( $row ) {
				if ( $row['column'] == 'email_address' ) {
						$row['column'] = 'user_email';
				}

				return $row;        },
			$user_columns
		);

		update_option( 'ld_gb_user_columns', $user_columns );   }

	/**
	 * 2.1.0 upgrade script
	 *
	 * @since 2.1.0
	 * @access private
	 * @return void
	 */
	function upgrade_2_1_0() {
		global $wpdb;

		// Migrate "Safe Mode" option to the new option name
		$wpdb->update(
			"{$wpdb->prefix}options",
			[
				'option_name' => 'ld_gb_gradebook_disable_sorting_by_grades_backend',
			],
			[
				'option_name' => 'ld_gb_gradebook_safe_mode',
			]
		);  }

	/**
	 * 3.0.0 upgrade script
	 *
	 * @since 3.0.0
	 * @access private
	 * @return void
	 */
	function upgrade_3_0_0() {
		$role = get_role( 'subscriber' );

		// Generally, the Subscriber Role should exist. But just in case I guess.
		if ( ! $role ) {
			return;
		}

		// It was previously possible to grant Subscribers the ability to view the Gradebook, which is a problem
		// This is a nuclear option to just ensure Subscribers can't do anything they shouldn't be able to. This is stuff for more privileged roles
		$caps = [
			'view_gradebook',
			'read_gradebook',
			'read_private_gradebooks',
			'publish_gradebooks',
			'edit_gradebook',
			'edit_gradebooks',
			'edit_private_gradebooks',
			'edit_published_gradebooks',
			'delete_gradebooks',
			'delete_private_gradebooks',
			'delete_published_gradebooks',
			'delete_gradebook',
		];

		foreach ( $caps as $cap ) {
			$role->remove_cap( $cap );
		}   }

	/**
	 * 4.2.0 upgrade script
	 *
	 * @since 4.2.0
	 * @access private
	 * @return void
	 */
	function upgrade_4_2_0() {
		try {
			// Migrate over License Key to auth-token.php.
			LD_GB_Install::authenticate();

			// Delete old data that isn't used anymore.
			delete_option( 'learndash_gradebook_license_status' );
			delete_transient( 'learndash_gradebook_license_validity' );
		} catch ( Exception $exception ) {
			// Re-throw exception with a new Code so that we can more intelligently save our Notice.
			throw new Exception( $exception->getMessage(), 403 );
		}   }

	/**
	 * 4.3.0 upgrade script
	 *
	 * @since 4.3.0
	 * @access private
	 * @return void
	 */
	function upgrade_4_3_0(): void {
		// Help ensure that anyone who may have encountered an issue with the license migration won't have a deceptively stuck notice.
		self::clear_notices( 'upgrade_error' );

		// Re-run the authentication script from the 4.2.0 upgrade routine, which will properly categorize a hit authentication error.
		$this->upgrade_4_2_0(); }
}
