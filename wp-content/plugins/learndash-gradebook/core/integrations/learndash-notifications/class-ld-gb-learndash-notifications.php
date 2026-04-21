<?php
/**
 * Integration for LearnDash Notifications
 *
 * @since 4.3.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/core/integrations/learndash-notifications
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_Notifications
 *
 * Integration for LearnDash Notifications
 *
 * @since 4.3.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/core/integrations/learndash-notifications
 */
final class LD_GB_Notifications {
	/**
	 * LD_GB_Notifications constructor.
	 *
	 * @since 4.3.0
	 */
	public function __construct() {
		if ( ! class_exists( 'LearnDash_Notifications' ) ) {
			return;
		}

		// @phpstan-ignore-next-line -- This constant can be changed.
		if ( ! defined( 'LEARNDASH_NOTIFICATIONS_VERSION' ) || version_compare( LEARNDASH_NOTIFICATIONS_VERSION, '1.6.2', '<' ) ) {
			return;
		}

		require_once trailingslashit( __DIR__ ) . 'class-ld-gb-learndash-notifications-manual-grade-added-trigger.php';

		add_action( 'init', [ $this, 'init' ] );

		add_action( 'init', [ $this, 'register_scripts' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

		add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );

		add_filter( 'learndash_notifications_triggers', [ $this, 'learndash_notifications_triggers' ] );

		add_filter( 'learndash_notifications_conditions', [ $this, 'learndash_notifications_conditions' ] );

		add_filter( 'learndash_notifications_object_fields', [ $this, 'learndash_notifications_object_fields' ] );

		add_filter( 'learndash_notifications_shortcodes_instructions', [ $this, 'learndash_notifications_shortcodes_instructions' ], 10, 2 );

		add_action( 'do_meta_boxes', [ $this, 'inject_nonce' ] );

		add_filter( 'learndash_notifications_shortcode_output', [ $this, 'learndash_notifications_shortcode_output' ], 10, 3 );

		add_filter( 'manage_ld-notification_posts_columns', [ $this, 'manage_posts_columns' ], 11 );

		add_action( 'manage_ld-notification_posts_custom_column', [ $this, 'output_post_columns' ], 11, 2 );

		add_action( 'save_post', [ $this, 'erase_post_meta' ], 11 );    }

	/**
	 * Set up our Trigger handler
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public function init() {
		$trigger = new LD_GB_Notifications_Manual_Grade_Added();
		$trigger->listen(); }

	/**
	 * Register the Scripts used by this integration
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public function register_scripts() {
		wp_register_script(
			'ld-gb-learndash-notifications',
			LEARNDASH_GRADEBOOK_URI . 'dist/learndash-notifications-scripts' . learndash_min_asset() . '.js',
			[ 'jquery' ],
			defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : LEARNDASH_GRADEBOOK_VERSION,
			true
		);  }

	/**
	 * Enqueues our Scripts on the Edit Notification screen
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		global $pagenow;
		global $current_screen;

		if ( ( $pagenow !== 'post-new.php' && $pagenow !== 'post.php' ) || $current_screen->post_type !== 'ld-notification' ) {
			return;
		}

		wp_enqueue_script( 'ld-gb-learndash-notifications' );

		wp_localize_script(
			'ld-gb-learndash-notifications',
			'ldGbNotifications',
			[
				'l10n' => [
					'rest' => [
						'get_gradebooks' => trailingslashit( esc_url_raw( rest_url( 'ld-gb/notifications/v1/get-gradebooks/' ) ) ),
					],
				],
			]
		);  }

	/**
	 * Adds REST API Endpoints
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public function add_endpoints() {
		register_rest_route(
			'ld-gb/notifications/v1',
			'/get-gradebooks/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_gradebooks' ],
				'args'                => [
					'term' => [
						'required'          => false,
						'validate_callback' => function ( $value, $request, $param ) {
							return is_string( $value );
						},
					],
					'page' => [
						'required'          => false,
						'validate_callback' => function ( $value, $request, $param ) {
							return is_numeric( $value );
						},
					],
				],
				'permission_callback' => function ( $request ) {
					// LearnDash Notifications has this hardcoded to "manage_options" as well.
					return current_user_can( 'manage_options' );
				},
			]
		);  }

	/**
	 * Allows Select2 to search Gradebooks
	 *
	 * @param \WP_REST_Request $request  Request object.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return \WP_REST_Response          Response object.
	 */
	public function get_gradebooks( $request ) {
		try {
			$search = $request->get_param( 'term' );
			$page   = intval( $request->get_param( 'page' ) );

			if ( empty( $page ) ) {
				$page = 1;
			}

			// By default WP_Query search all post title, content, and excerpt.
			// This filter modify it to only search in post title.
			add_filter(
				'posts_search',
				function ( $search, $wp_query ) {
					if ( isset( $wp_query->query['ld_notifications_action'] ) && $wp_query->query['ld_notifications_action'] === 'ld_notifications_get_gradebook_list' ) {
						$search = preg_replace( '/(OR)\s.*?post_(excerpt|content)\sLIKE\s.*?\)/', '', $search );
					}

					return $search;
				},
				10,
				2
			);

			$posts_per_page = 10;

			$query = new \WP_Query(
				[
					'post_type'               => 'gradebook',
					'post_status'             => 'publish',
					'posts_per_page'          => $posts_per_page,
					'paged'                   => $page,
					's'                       => $search,
					'ld_notifications_action' => 'ld_notifications_get_gradebook_list',
					'fields'                  => 'ids',
				]
			);

			$gradebooks = [];

			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post_id ) {
					$gradebooks[] = [
						'id'   => $post_id,
						'text' => esc_html( get_the_title( $post_id ) ),
					];
				}
			}

			if ( $page === 1 ) {
				$gradebooks = array_merge(
					[
						[
							'id'   => 'all',
							'text' => __( 'Any Gradebook', 'learndash-gradebook' ),
						],
					],
					$gradebooks
				);
			}

			$count_query = new \WP_Query(
				[
					'post_type'               => 'gradebook',
					'post_status'             => 'publish',
					'posts_per_page'          => -1,
					'paged'                   => $page,
					's'                       => $search,
					'ld_notifications_action' => 'ld_notifications_get_gradebook_list',
					'fields'                  => 'ids',
				]
			);

			$gradebook_count = $count_query->found_posts;

			return new \WP_REST_Response(
				[
					'results'    => $gradebooks,
					'pagination' => [
						'more' => $gradebook_count > ( $posts_per_page * $page ),
					],
				]
			);
		} catch ( Exception $exception ) {
			return new \WP_REST_Response(
				[
					'message' => $exception->getMessage(),
				],
				500
			);
		}   }

	/**
	 * Adds new Triggers to LearnDash Notifications
	 *
	 * @param array $triggers  Array of Triggers.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return array            Array of Triggers.
	 */
	public function learndash_notifications_triggers( $triggers ) {
		$triggers['ld_gb_manual_grade_added'] = __( 'Grade manually added to the Gradebook for a Student', 'learndash-gradebook' );

		return $triggers;   }

	/**
	 * Remove our Trigger as a Condition
	 *
	 * @param array $conditions  Array of Conditions.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return array               Array of Conditions.
	 */
	public function learndash_notifications_conditions( $conditions ) {
		if ( isset( $conditions['ld_gb_manual_grade_added'] ) ) {
			unset( $conditions['ld_gb_manual_grade_added'] );
		}

		return $conditions; }

	/**
	 * Add a Field to choose a Gradebook for our Trigger
	 *
	 * @param array $fields  Array of Fields.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return array           Array of Fields.
	 */
	public function learndash_notifications_object_fields( $fields ) {
		$gradebook_query = new WP_Query(
			[
				'post_type'      => 'gradebook',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'post_status'    => 'publish',
			]
		);

		$gradebook_options = [];

		if ( $gradebook_query->have_posts() ) {
			foreach ( $gradebook_query->posts as $gradebook_id ) {
				$gradebook_options[ $gradebook_id ] = esc_html( get_the_title( $gradebook_id ) );
			}
		}

		$current_value = get_post_meta( get_the_ID(), '_ld_notifications_gradebook_id', true );

		$fields['gradebook_id'] = [
			'type'            => 'dropdown',
			'title'           => __( 'Gradebook', 'learndash-gradebook' ),
			'help_text'       => __( 'Gradebook that the notification is assigned to.', 'learndash-gradebook' ),
			'hide'            => 1,
			'disabled'        => 0,
			'parent'          => [ 'ld_gb_manual_grade_added' ],
			'value'           => [],
			'dynamic_options' => 0, // Set to 0 since we are using our own REST API endpoint.
			'trigger_object'  => 1,
			'multiple'        => 1,
			'current_value'   => ( $current_value ) ? $current_value : [],
		];

		return $fields; }

	/**
	 * Add shortcode hints for our Trigger
	 *
	 * @param array $instructions         Array of Shortcodes per Trigger.
	 * @param array $shortcode_groupings  Shortcodes grouped by common categories so that they can be reused.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return array                        Array of Shortcodes per Trigger
	 */
	public function learndash_notifications_shortcodes_instructions( $instructions, $shortcode_groupings ) {
		$instructions['ld_gb_manual_grade_added'] = array_merge(
			$shortcode_groupings['user'],
			[
				'[ld_notifications field="gradebook_manual_grade" show="name"]' => __( "Show the Manual Grade's Name", 'learndash-gradebook' ),
				'[ld_notifications field="gradebook_manual_grade" show="score"]' => __( "Show the Manual Grade's score as a number 0-100, after the Grade Status has been applied", 'learndash-gradebook' ),
				'[ld_notifications field="gradebook_manual_grade" show="original_score"]' => __( "Show the Manual Grade's raw score as a number 0-100", 'learndash-gradebook' ),
				'[ld_notifications field="gradebook_manual_grade" show="score_display"]' => __( "Show the Manual Grade's formatted score", 'learndash-gradebook' ),
				'[ld_notifications field="gradebook_manual_grade" show="status"]' => __( "Show the Manual Grade's entered status", 'learndash-gradebook' ),
				'[ld_notifications field="gradebook_manual_grade" show="gradebook"]' => __( 'Show the Gradebook name for the Manual Grade', 'learndash-gradebook' ),
				'[ld_notifications field="gradebook_manual_grade" show="component"]' => __( 'Show the Gradebook Component name for the Manual Grade', 'learndash-gradebook' ),
				'[ld_notifications field="gradebook_manual_grade" show="timestamp"]' => __( 'Show when the Manual Grade was entered', 'learndash-gradebook' ),
			]
		);

		return $instructions;   }

	/**
	 * Injects a nonce field on the edit screen for Notifications
	 *
	 * @param string $post_type  Post Type.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public function inject_nonce( $post_type ) {
		if ( $post_type !== 'ld-notification' ) {
			return;
		}

		wp_nonce_field( 'wp_rest', 'learndash_gradebook_notifications_nonce' ); }

	/**
	 * Modify the output of the [ld_notifications] Shortcode so it can use our data
	 *
	 * @param string $output  Shortcode output.
	 * @param array  $atts    Shortcode Attributes.
	 * @param array  $data    Args sent by the fired action.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return string           Shortcode output.
	 */
	public function learndash_notifications_shortcode_output( $output, $atts, $data ) {
		$show = strtolower( $atts['show'] );

		if ( $atts['field'] === 'gradebook_manual_grade' ) {
			switch ( $show ) {
				case 'name':
					$output = esc_html( $data['name'] );
					break;
				case 'score':
					$output = esc_html( (int) $data['score'] );
					break;
				case 'original_score':
					$output = esc_html( $data['original_score'] );
					break;
				case 'gradebook':
					$output = esc_html( get_the_title( $data['gradebook'] ) );
					break;
				case 'component':
					$components = ld_gb_get_field( 'components', $data['gradebook'] );

					$components = array_values(
						array_filter(
							$components,
							function ( $component ) use ( $data ) {
								return $component['id'] === $data['component'];                         }
						)
					);

					if ( empty( $components ) ) {
						$output = __( 'No Component found', 'learndash-gradebook' );
					} else {
						$output = $components[0]['name'];
					}

					break;
				case 'status':
					$grade_statuses = ld_gb_get_grade_statuses();

					if ( empty( $data['status'] ) ) {
						$output = __( 'No Special Status', 'learndash-gradebook' );
					} else {
						$output = $grade_statuses[ $data['status'] ]['label'];
					}

					break;
				case 'timestamp':
					$output = esc_html( learndash_adjust_date_time_display( $data['completed'] ) );
					break;
				case 'score_display':
					$output = esc_html( $data['score_display'] );
					break;

				default:
					break;
			}
		}

		return $output; }

	/**
	 * Adds a Gradebook column when viewing all Notifications
	 *
	 * @param string[] $columns  Columns to show.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return string[]           Columns to show.
	 */
	public function manage_posts_columns( $columns ) {
		$trigger_column = '';

		$new_columns = [
			'gradebook' => __( 'Gradebook', 'learndash-gradebook' ),
		];

		if ( isset( $columns['trigger'] ) ) {
			// Save for later, as we want this at the end.
			$new_columns['trigger'] = $columns['trigger'];
			unset( $columns['trigger'] );
		}

		$columns = array_merge(
			$columns,
			$new_columns
		);

		return $columns;    }

	/**
	 * Outputs information for our Gradebook column
	 *
	 * @param string $column   Column key.
	 * @param int    $post_id  Post ID.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public function output_post_columns( $column, $post_id ) {
		if ( $column !== 'gradebook' ) {
			return;
		}

		$current_value = get_post_meta( $post_id, '_ld_notifications_gradebook_id', true );

		$printed_object = '';

		if ( ! empty( $current_value ) ) {
			if ( ! is_array( $current_value ) ) {
				$current_value = [ $current_value ];
			}

			if ( in_array( 'all', $current_value ) ) {
				esc_html_e( 'Any Gradebook', 'learndash-gradebook' );
			} else {
				$printed_object = [];
				foreach ( $current_value as $gradebook_id ) {
					$gradebook = get_post( $gradebook_id );

					if ( is_object( $gradebook ) ) {
						$printed_object[] = esc_html( $gradebook->post_title ) . ' (ID: ' . $gradebook->ID . ')';
					}
				}
			}
		} else {
			$printed_object = '-';
		}

		if ( is_string( $printed_object ) ) {
			echo esc_html( $printed_object );
		} elseif ( is_array( $printed_object ) ) {
			$total         = count( $printed_object );
			$max_displayed = 3;
			$not_displayed = $total - $max_displayed;

			$output = '';
			if ( ! empty( $printed_object ) ) {
				$printed_object = array_slice( $printed_object, 0, 3, true );

				$output .= '<ul class="learndash-object-list">';
				foreach ( $printed_object as $object_string ) {
					$output .= '<li>' . $object_string . '</li>';
				}
				$output .= '</ul>';
			}

			if ( $not_displayed > 0 ) {
				printf(
					// translators: A list of Gradebooks associated with the Notification, followed by a count of how many past the first 3 were not displayed.
					_n( '%1$s %2$sand %3$d other%4$s', '%1$s %2$s and %3$d others%4$s', $not_displayed, 'learndash-gradebook' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$output, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'<p class="learndash-other-description">',
					esc_html( $not_displayed ),
					'</p>'
				);
			} else {
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $output;
			}
		}   }

	/**
	 * Clean up some saved data to prevent possible misfires in the future.
	 *
	 * @param int $post_id  Notification ID.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public function erase_post_meta( $post_id ) {
		$post_data = wp_unslash( $_POST );

		if ( ! isset( $post_data['learndash_notifications_nonce'] ) || ! wp_verify_nonce( $post_data['learndash_notifications_nonce'], 'learndash_notifications_meta_box' ) ) {
			return;
		}

		if ( get_post_type( $post_id ) !== 'ld-notification' ) {
			return;
		}

		if ( $post_data['_ld_notifications_trigger'] !== 'ld_gb_manual_grade_added' ) {
			return;
		}

		$delete_post_meta_keys = [
			'_ld_notifications_course_id',
			'_ld_notifications_lesson_id',
			'_ld_notifications_topic_id',
			'_ld_notifications_quiz_id',
		];

		foreach ( $delete_post_meta_keys as $meta_key ) {
			update_post_meta( $post_id, $meta_key, [] );
		}

		$post_data = wp_parse_args(
			$post_data,
			[
				'_ld_notifications_recipient' => [],
			]
		);

		if ( ! is_array( $post_data['_ld_notifications_recipient'] ) ) {
			$post_data['_ld_notifications_recipient'] = [ $post_data['_ld_notifications_recipient'] ];
		}

		$post_data['_ld_notifications_recipient'] = array_filter(
			$post_data['_ld_notifications_recipient'],
			function ( $recipient ) {
				return in_array( $recipient, [ 'user', 'admin' ], true );           }
		);

		update_post_meta( $post_id, '_ld_notifications_recipient', $post_data['_ld_notifications_recipient'] ); }
}

$integrate = new LD_GB_Notifications();
