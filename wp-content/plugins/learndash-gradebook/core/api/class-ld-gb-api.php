<?php
/**
 * API functionality.
 *
 * @since 1.2.0
 *
 * @package LearnDash_Gradebook
 */

defined( 'ABSPATH' ) || die;

/**
 * Class LD_GB_API
 *
 * API functionality.
 *
 * @since 1.2.0
 */
class LD_GB_API {
	/**
	 * LD_GB_API constructor.
	 *
	 * @since 1.2.0
	 */
	function __construct() {
		add_filter( 'ld_gb_admin_script_data', [ $this, 'script_data' ] );
		add_filter( 'rest_user_query', [ $this, 'remove_has_published_posts' ], 100, 2 );

		add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );  }

	/**
	 * Gets the active group ID for the Attendance, if one at all.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @param array $data  Localized data.
	 *
	 * @return array
	 */
	public function script_data( $data ): array {
		if ( ! isset( $data['l10n'] ) ) {
			$data['l10n'] = [];
		}

		$data['l10n']['nonce'] = wp_create_nonce( 'wp_rest' );

		if ( ! isset( $data['l10n']['rest'] ) ) {
			$data['l10n']['rest'] = [];
		}

		$data['l10n']['rest'] = wp_parse_args(
			$data['l10n']['rest'],
			[
				'base'                   => trailingslashit( esc_url_raw( rest_url( 'ld-gb/v1' ) ) ),
				'select2_get_gradebooks' => trailingslashit( esc_url_raw( rest_url( 'ld-gb/v1/select2-get-gradebooks/' ) ) ),
			]
		);

		$data['l10n']['wp_rest'] = trailingslashit( esc_url_raw( rest_url( 'wp/v2' ) ) );

		return $data;
	}

	/**
	 * Removes `has_published_posts` from the query args so even users who have not published content are returned by
	 * the request.
	 *
	 * Silly WordPress...
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_user_query/
	 *
	 * @param array           $prepared_args Array of arguments for WP_User_Query.
	 * @param WP_REST_Request $request The current request.
	 *
	 * @return array
	 */
	function remove_has_published_posts( $prepared_args, $request ) {
		if ( $request['has_published_posts'] === 'false' ) {
			unset( $prepared_args['has_published_posts'] );
		}

		return $prepared_args;
	}

	/**
	 * Adds some Rest API endpoints.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function add_endpoints() {
		register_rest_route(
			'ld-gb/v1',
			'/get-gradebook-data/(?P<gradebook_id>\d+)/(?P<group_id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_gradebook_data' ],
				'permission_callback' => function ( $request ) {
					return self::permission_callback_can_view_gradebook( $request );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/export-gradebook-component-data/(?P<gradebook_id>\d+)/(?P<group_id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'export_gradebook_component_data' ],
				'permission_callback' => function ( $request ) {
					return self::permission_callback_can_view_gradebook( $request );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/export-gradebook-all-grades/(?P<gradebook_id>\d+)/(?P<group_id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'export_gradebook_all_grades' ],
				'permission_callback' => function ( $request ) {
					return self::permission_callback_can_view_gradebook( $request );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/get-frontend-gradebook/(?P<gradebook_id>\d+)/(?P<group_id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_frontend_gradebook' ],
				'permission_callback' => function ( $request ) {
					return self::permission_callback_can_view_gradebook( $request );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/get-formatted-gradebook-data/(?P<gradebook_id>\d+)/(?P<group_id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_formatted_gradebook_data' ],
				'permission_callback' => function ( $request ) {
					return self::permission_callback_can_view_gradebook( $request );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/get-frontend-user-grades/(?P<user_id>\d+)/(?P<gradebook_id>\d+)/(?P<group_id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_frontend_user_grades' ],
				'permission_callback' => function ( $request ) {
					return self::permission_callback_can_view_gradebook( $request );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/add-manual-grade/(?P<user_id>\d+)/(?P<gradebook_id>\d+)/(?P<component_id>\d+)',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_manual_grade' ],
				'permission_callback' => function ( $request ) {
					return self::permission_callback_can_view_gradebook( $request );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/delete-manual-grade/(?P<user_id>\d+)/(?P<gradebook_id>\d+)/(?P<component_id>\d+)',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'delete_manual_grade' ],
				'permission_callback' => function ( $request ) {
					return self::permission_callback_can_view_gradebook( $request );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/edit-grade/(?P<user_id>\d+)/(?P<gradebook_id>\d+)/(?P<component_id>\d+)',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'edit_grade' ],
				'permission_callback' => function ( $request ) {
					return self::permission_callback_can_view_gradebook( $request );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/override-component-grade/(?P<user_id>\d+)/(?P<gradebook_id>\d+)/(?P<component_id>\d+)',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'override_component_grade' ],
				'permission_callback' => function ( $request ) {
					return self::permission_callback_can_view_gradebook( $request );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/delete-component-override/(?P<user_id>\d+)/(?P<gradebook_id>\d+)/(?P<component_id>\d+)',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'delete_component_override' ],
				'permission_callback' => function ( $request ) {
					return self::permission_callback_can_view_gradebook( $request );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/gutenberg-get-gradebooks/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'gutenberg_get_gradebooks' ],
				'args'                => [
					's'        => [
						'required' => true,
					],
					'offset'   => [],
					'per_page' => [],
				],
				'permission_callback' => function ( $request ) {
					return current_user_can( 'view_gradebook' );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/gutenberg-get-gradebook/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'gutenberg_get_gradebook' ],
				'args'                => [
					'id' => [
						'required' => true,
					],
				],
				'permission_callback' => function ( $request ) {
					return current_user_can( 'view_gradebook' );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/gutenberg-get-users/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'gutenberg_get_users' ],
				'args'                => [
					's'        => [
						'required' => true,
					],
					'offset'   => [],
					'per_page' => [],
				],
				'permission_callback' => function ( $request ) {
					return current_user_can( 'view_gradebook' );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/gutenberg-get-user/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'gutenberg_get_user' ],
				'args'                => [
					'id' => [
						'required' => true,
					],
				],
				'permission_callback' => function ( $request ) {
					return current_user_can( 'view_gradebook' );
				},
			]
		);

		register_rest_route(
			'ld-gb/v1',
			'/select2-get-gradebooks/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'select2_get_gradebooks' ],
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
					return current_user_can( 'view_gradebook' );
				},
			]
		);
	}

	/**
	 * Adds a simple endpoint to grab the Gradebook Data for a given Gradebook
	 *
	 * @param array $request  Params passed to the Request Object
	 *
	 * @access public
	 * @since 2.0.0
	 * @return WP_REST_Response REST Response
	 */
	public function get_gradebook_data( $request ) {
		try {
			$params = wp_parse_args(
				$_GET,
				[
					'per_page' => 30,
				]
			);

			$data = learndash_gradebook_get_gradebook_data( $request['gradebook_id'], $request['group_id'], $params );

			ld_gb_update_largest_total_users( $data['query']->total_users );

			return new WP_REST_Response(
				[
					'gradebook_id' => (int) $request['gradebook_id'],
					'group_id'     => (int) $request['group_id'],
					'grades'       => isset( $data['grades'] ) ? $data['grades'] : [],
					'components'   => isset( $data['components'] ) ? $data['components'] : [],
					'total_users'  => $data['query']->total_users,
					'total_pages'  => ceil( $data['query']->total_users / $params['per_page'] ),
				]
			);
		} catch ( Exception $exception ) {
			ob_clean();
			ob_start();
			do_action( 'ld_gb_frontend_gradebook_notice', $exception->getMessage(), 'alert' );
			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'html'      => $html,
					'exception' => [
						'message' => $exception->getMessage(),
						'trace'   => $exception->getTraceAsString(),
					],
				],
				500
			);
		}   }

	/**
	 * Adds an endpoint to grab CSV Component Data for a Gradebook.
	 *
	 * This will only return as many results as there are per_page, so you may need to hit this multiple times and merge the results yourself.
	 *
	 * If you've requested a page with no results, a 500 error will be returned.
	 *
	 * @since 2.0.0
	 *
	 * @param array $request Params passed to the Request Object.
	 *
	 * @return WP_REST_Response REST Response.
	 */
	public function export_gradebook_component_data( $request ) {
		try {
			$params = wp_parse_args(
				$_GET,
				[
					'per_page' => 30,
				]
			);

			$data = learndash_gradebook_get_gradebook_data( $request['gradebook_id'], $request['group_id'], $params );

			if ( ! isset( $data['grades'] ) || empty( $data['grades'] ) ) {
				$message = _x( 'No Grades found.', 'Component CSV Export: No Grades Found Error', 'learndash-gradebook' );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			}

			if ( ! isset( $data['components'] ) || ! $data['components'] ) {
				$data['components'] = [];
			}

			$csv_headers = [
				'ID'           => _x( 'User ID', 'Component CSV Export: User ID Header', 'learndash-gradebook' ),
				'display_name' => _x( 'Display Name', 'Component CSV Export: Display Name Header', 'learndash-gradebook' ),
				'first_name'   => _x( 'First Name', 'Component CSV Export: First Name Header', 'learndash-gradebook' ),
				'last_name'    => _x( 'Last Name', 'Component CSV Export: Last Name Header', 'learndash-gradebook' ),
				'user_email'   => _x( 'Email Address', 'Component CSV Export: Email Address Header', 'learndash-gradebook' ),
				'user_login'   => _x( 'Username', 'Component CSV Export: Username Header', 'learndash-gradebook' ),
				'grade'        => _x( 'Overall Grade', 'Component CSV Export: Overall Grade Header', 'learndash-gradebook' ),
			];

			foreach ( $data['components'] as $component_id => $component_name ) {
				$csv_headers[ $component_id ] = $component_name;
			}

			$csv_headers = apply_filters( 'ld_gb_export_data_csv_headers', $csv_headers, $data, (int) $request['gradebook_id'], (int) $request['group_id'] );

			$csv_data = [];

			foreach ( $data['grades'] as $index => $row ) {
				$csv_data[ $index ] = [];

				foreach ( $csv_headers as $key => $header ) {
					$csv_data[ $index ][ $header ] = ( isset( $row[ $key ] ) ) ? $row[ $key ] : '';
				}
			}

			$csv_data = array_values( $csv_data );

			$csv_data = apply_filters( 'ld_gb_export_component_data_results', $csv_data, $data, (int) $request['gradebook_id'], (int) $request['group_id'] );

			$csv_data = self::array_to_csv( $csv_data );

			return new WP_REST_Response(
				[
					'gradebook_id' => (int) $request['gradebook_id'],
					'group_id'     => (int) $request['group_id'],
					'csv_data'     => $csv_data,
					'total_users'  => $data['query']->total_users,
					'total_pages'  => ceil( $data['query']->total_users / $params['per_page'] ),
				]
			);
		} catch ( Exception $exception ) {
			ob_clean();
			ob_start();
			do_action( 'ld_gb_frontend_gradebook_notice', $exception->getMessage(), 'alert' );
			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'html'      => $html,
					'exception' => [
						'message' => $exception->getMessage(),
						'trace'   => $exception->getTraceAsString(),
					],
				],
				500
			);
		}   }

	/**
	 * Adds an endpoint to grab CSV Data for every Grade in a Gradebook.
	 *
	 * This will only return as many results as there are per_page, so you may need to hit this multiple times and merge the results yourself.
	 *
	 * If you've requested a page with no results, a 500 error will be returned.
	 *
	 * @since 2.0.0
	 *
	 * @param array $request Params passed to the Request Object.
	 *
	 * @return WP_REST_Response REST Response.
	 */
	public function export_gradebook_all_grades( $request ) {
		try {
			$params = wp_parse_args(
				$_GET,
				[
					'per_page' => 30,
				]
			);

			$data = learndash_gradebook_get_gradebook_data( $request['gradebook_id'], $request['group_id'], $params );

			if ( ! isset( $data['grades'] ) || empty( $data['grades'] ) ) {
				$message = _x( 'No Grades found.', 'All Grades CSV Export: No Grades Found Error', 'learndash-gradebook' );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			}

			$csv_data = [];

			$csv_user_headers = [
				'ID'           => _x( 'User ID', 'All Grades CSV Export: User ID Header', 'learndash-gradebook' ),
				'display_name' => _x( 'Display Name', 'All Grades CSV Export: Display Name Header', 'learndash-gradebook' ),
				'first_name'   => _x( 'First Name', 'All Grades CSV Export: First Name Header', 'learndash-gradebook' ),
				'last_name'    => _x( 'Last Name', 'All Grades CSV Export: Last Name Header', 'learndash-gradebook' ),
				'user_email'   => _x( 'Email Address', 'All Grades CSV Export: Email Address Header', 'learndash-gradebook' ),
				'user_login'   => _x( 'Username', 'All Grades CSV Export: Username Header', 'learndash-gradebook' ),
			];

			$csv_user_headers = apply_filters( 'ld_gb_export_data_user_csv_headers', $csv_user_headers, $data, (int) $request['gradebook_id'], (int) $request['group_id'] );

			$csv_grade_headers = [
				'name'      => _x( 'Grade Name', 'All Grades CSV Export: Grade Name Header', 'learndash-gradebook' ),
				'type'      => _x( 'Grade Type', 'All Grades CSV Export: Grade Type Header', 'learndash-gradebook' ),
				'score'     => _x( 'Grade Score', 'All Grades CSV Export: Grade Score Header', 'learndash-gradebook' ),
				'completed' => _x( 'Grade Completion Timestamp', 'All Grades CSV Export: Grade Completion Timestamp Header', 'learndash-gradebook' ),
				'status'    => _x( 'Grade Status', 'All Grades CSV Export: Grade Status Header', 'learndash-gradebook' ),
			];

			$csv_grade_headers = apply_filters( 'ld_gb_export_data_grade_csv_headers', $csv_grade_headers, $data, (int) $request['gradebook_id'], (int) $request['group_id'] );

			$datetimestamp_format = ld_gb_get_datetimestamp_format();
			$timezone_offset      = ld_gb_get_timezone_offset();

			foreach ( $data['grades'] as $index => $user ) {
				$user_grade = new LD_GB_UserGrade( $user['ID'], $request['gradebook_id'] );

				if ( ! $user_grade->get_components() ) {
					continue;
				}

				foreach ( $user_grade->get_components() as $component ) {
					$row = [];

					foreach ( $component['grades'] as $grade ) {
						// Populate User data per Row
						foreach ( $csv_user_headers as $key => $label ) {
							if ( isset( $user[ $key ] ) && $user[ $key ] ) {
								$row[ $label ] = $user[ $key ];
							} else {
								$row[ $label ] = '';
							}
						}

						// Populate this Row's Grade data
						foreach ( $csv_grade_headers as $key => $label ) {
							if ( isset( $grade[ $key ] ) ) {
								if ( $key == 'completed' ) {
									$timestamp = ld_gb_get_datetimestamp( $grade['completed'], $datetimestamp_format, $timezone_offset );

									$row[ $label ] = $timestamp;
								} else {
									$row[ $label ] = $grade[ $key ];
								}
							} else {
								$row[ $label ] = '';
							}
						}

						$csv_data = array_merge( $csv_data, [ $row ] );
					}
				}
			}

			$csv_data = array_values( $csv_data );

			$csv_data = apply_filters( 'ld_gb_export_all_grades_results', $csv_data, $data, (int) $request['gradebook_id'], (int) $request['group_id'] );

			/**
			 * If a page would have no results, return nothing for the CSV so that the page gets skipped
			 * This only happens if there are more than per_page Users total and a page of Users has 0 grades entered
			 *
			 * For instance, if per_page is set to 1 and 3 Users exist for a Gradebook, but only the first User found has any Grades at all, this will allow the second and third pages to still return results and not break the API call
			 */
			if ( empty( $csv_data ) ) {
				return new WP_REST_Response(
					[
						'gradebook_id' => (int) $request['gradebook_id'],
						'group_id'     => (int) $request['group_id'],
						'csv_data'     => false,
						'total_users'  => $data['query']->total_users,
						'total_pages'  => ceil( $data['query']->total_users / $params['per_page'] ),
					]
				);
			}

			$csv_data = self::array_to_csv( $csv_data );

			return new WP_REST_Response(
				[
					'gradebook_id' => (int) $request['gradebook_id'],
					'group_id'     => (int) $request['group_id'],
					'csv_data'     => $csv_data,
					'total_users'  => $data['query']->total_users,
					'total_pages'  => ceil( $data['query']->total_users / $params['per_page'] ),
				]
			);
		} catch ( Exception $exception ) {
			ob_clean();
			ob_start();
			do_action( 'ld_gb_frontend_gradebook_notice', $exception->getMessage(), 'alert' );
			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'html'      => $html,
					'exception' => [
						'message' => $exception->getMessage(),
						'trace'   => $exception->getTraceAsString(),
					],
				],
				500
			);
		}   }

	/**
	 * Returns the HTML for the Frontend Gradebook.
	 *
	 * @since 2.0.0
	 *
	 * @param array $request Params passed to the Request Object.
	 *
	 * @return WP_REST_Response REST Response.
	 */
	public function get_frontend_gradebook( $request ) {
		try {
			$params = wp_parse_args(
				$_GET,
				[
					'grade_format' => apply_filters( 'ld_gb_frontend_gradebook_grade_format', ld_gb_get_option_field( 'grade_display_mode', 'letter' ), $request['gradebook_id'], $request['group_id'] ),
				]
			);

			ob_start();

			/**
			 * Gradebook Results
			 * On first load, this will be the default Gradebook and Group Combo
			 * This same hook is called when loading a new Gradebook or changing Groups
			 *
			 * @hooked LD_GB_SC_FrontendGradebook::change_gradebook() 10
			 */
			do_action( 'ld_gb_frontend_gradebook_results', (int) $request['gradebook_id'], (int) $request['group_id'], $params['grade_format'] );

			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'gradebook_id' => (int) $request['gradebook_id'],
					'group_id'     => (int) $request['group_id'],
					'grade_format' => $params['grade_format'],
					'html'         => $html,
				]
			);
		} catch ( Exception $exception ) {
			ob_clean();
			ob_start();
			do_action( 'ld_gb_frontend_gradebook_notice', $exception->getMessage(), 'alert' );
			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'html'      => $html,
					'exception' => [
						'message' => $exception->getMessage(),
						'trace'   => $exception->getTraceAsString(),
					],
				],
				500
			);
		}   }

	/**
	 * Adds a simple endpoint to grab the Gradebook Data for a given Gradebook
	 * This returns the data formatted for the Frontend Gradebook
	 *
	 * @param array $request  Params passed to the Request Object
	 *
	 * @access public
	 * @since 2.0.0
	 * @return WP_REST_Response REST Response
	 */
	public function get_formatted_gradebook_data( $request ) {
		try {
			$params = wp_parse_args(
				$_GET,
				[
					'per_page'     => 30,
					'grade_format' => apply_filters( 'ld_gb_frontend_gradebook_grade_format', ld_gb_get_option_field( 'grade_display_mode', 'letter' ), $request['gradebook_id'], $request['group_id'] ),
				]
			);

			$data = learndash_gradebook_get_gradebook_data( $request['gradebook_id'], $request['group_id'], $params );

			$user_columns = learndash_gradebook_get_user_columns( $request['gradebook_id'], $request['group_id'] );

			$primary_column = 'display_name';
			foreach ( $user_columns as $key => $name ) {
				$primary_column = $key;
				break;
			}

			if ( isset( $data['grades'] ) ) {
				foreach ( $data['grades'] as $user_id => &$user_grade ) {
					if ( is_numeric( $user_grade['grade'] ) ) {
						$user_grade['grade'] = learndash_gradebook_get_grade_display( $user_grade['grade'], $params['grade_format'] );
					} elseif ( is_bool( $user_grade['grade'] ) && ! $user_grade['grade'] ) {
						$user_grade['grade'] = '';
					}

					if ( isset( $data['components'] ) ) {
						foreach ( $data['components'] as $key => $name ) {
							if ( is_numeric( $user_grade[ $key ] ) ) {
								$user_grade[ $key ] = learndash_gradebook_get_grade_display( $user_grade[ $key ], $params['grade_format'] );
							} elseif ( is_bool( $user_grade[ $key ] ) && ! $user_grade[ $key ] ) {
								$user_grade[ $key ] = '';
							}
						}
					}

					if ( isset( $user_grade[ $primary_column ] ) ) {
						ob_start();

						?>

						<a href="#open-edit-panel" class="open-edit-panel" data-user_id="<?php echo esc_attr( $user_id ); ?>" data-gradebook_id="<?php echo esc_attr( $request['gradebook_id'] ); ?>" data-group_id="<?php echo esc_attr( $request['group_id'] ); ?>" data-grade_format="<?php echo esc_attr( $params['grade_format'] ); ?>">

						<?php

						$user_grade[ $primary_column ] = ob_get_clean() . $user_grade[ $primary_column ];

						ob_start();

						?>

						</a>

						<div class="hover-link">
							<a href="#open-edit-panel" class="open-edit-panel" data-user_id="<?php echo esc_attr( $user_id ); ?>" data-gradebook_id="<?php echo esc_attr( $request['gradebook_id'] ); ?>" data-group_id="<?php echo esc_attr( $request['group_id'] ); ?>" data-grade_format="<?php echo esc_attr( $params['grade_format'] ); ?>">
								<?php _ex( 'View/Edit User Grades', 'View/Edit User Grades link text for formatted Gradebook Results used on the Frontend Gradebook during Pagination', 'learndash-gradebook' ); ?>
							</a>
						</div>

						<?php

						$user_grade[ $primary_column ] .= ob_get_clean();
					}
				}
			}

			/**
			 * If you're overriding frontend-gradebook/table-row.php, you'll likely need to adjust this as well for any subsequently loaded pages
			 *
			 * @var array
			 * @since 2.0.0
			 */
			$data = apply_filters( 'ld_gb_get_formatted_gradebook_data', $data, (int) $request['gradebook_id'], (int) $request['group_id'], $params );

			return new WP_REST_Response(
				[
					'gradebook_id' => (int) $request['gradebook_id'],
					'group_id'     => (int) $request['group_id'],
					'grades'       => isset( $data['grades'] ) ? $data['grades'] : [],
					'components'   => isset( $data['components'] ) ? $data['components'] : [],
					'total_users'  => $data['query']->total_users,
					'total_pages'  => ceil( $data['query']->total_users / $params['per_page'] ),
				]
			);
		} catch ( Exception $exception ) {
			ob_clean();
			ob_start();
			do_action( 'ld_gb_frontend_gradebook_notice', $exception->getMessage(), 'alert' );
			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'html'      => $html,
					'exception' => [
						'message' => $exception->getMessage(),
						'trace'   => $exception->getTraceAsString(),
					],
				],
				500
			);
		}   }

	/**
	 * Returns the HTML for the User Grade Panel
	 *
	 * @param array $request  Params passed to the Request Object
	 *
	 * @access public
	 * @since 2.0.0
	 * @return WP_REST_Response REST Response
	 */
	public function get_frontend_user_grades( $request ) {
		$params = wp_parse_args(
			$_GET,
			[
				'grade_format' => apply_filters( 'ld_gb_frontend_gradebook_grade_format', ld_gb_get_option_field( 'grade_display_mode', 'letter' ), $request['gradebook_id'], 0 ),
			]
		);

		try {
			ob_start();

			/**
			 * User Grade Panel
			 *
			 * @hooked LD_GB_SC_FrontendGradebook::edit_panel() 10
			 */
			do_action( 'ld_gb_frontend_gradebook_edit_panel', (int) $request['user_id'], (int) $request['gradebook_id'], (int) $request['group_id'], $params['grade_format'] );

			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'gradebook_id' => (int) $request['gradebook_id'],
					'user_id'      => (int) $request['user_id'],
					'grade_format' => $params['grade_format'],
					'html'         => $html,
				]
			);
		} catch ( Exception $exception ) {
			ob_clean();
			ob_start();
			do_action( 'ld_gb_frontend_gradebook_notice', $exception->getMessage(), 'alert' );
			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'html'      => $html,
					'exception' => [
						'message' => $exception->getMessage(),
						'trace'   => $exception->getTraceAsString(),
					],
				],
				500
			);
		}   }

	/**
	 * Adds a Manual Grade for a given User+Gradebook+Component
	 *
	 * @param array $request  Params passed to the Request Object
	 *
	 * @access public
	 * @since 2.0.0
	 * @return WP_REST_Response REST Response
	 */
	public function add_manual_grade( $request ) {
		try {
			if ( ld_gb_get_option_field( 'disable_manual_grades', false ) ) {
				$message = __( 'Manual Grades are disabled', 'learndash-gradebook' );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			}

			if ( ! isset( $_POST['grade'] ) || ! $_POST['grade'] ) {
				$message = _x( 'No grade data was passed to the API Endpoint', 'Manual Grade Add: No Manual Grade Data was passed to the API Endpoint error', 'learndash-gradebook' );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			}

			$grade = wp_parse_args(
				$_POST['grade'],
				[
					'score'     => 0,
					'name'      => '',
					'status'    => '',
					'component' => $request['component_id'],
					'gradebook' => $request['gradebook_id'],
					'user_id'   => $request['user_id'],
					'type'      => 'manual',
				]
			);

			$result = learndash_gradebook_update_manual_grade( $grade, false );

			if ( $result['type'] == 'error' ) {
				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $result['data']['error'] );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $result['error'],
						'html'    => $html,
					],
					500
				);
			}

			$grade_format = ( isset( $_POST['grade_format'] ) && $_POST['grade_format'] ? $_POST['grade_format'] : apply_filters( 'ld_gb_frontend_gradebook_grade_format', ld_gb_get_option_field( 'grade_display_mode', 'letter' ), $request['gradebook_id'], 0 ) );

			$user_row = $this->user_row( $request['user_id'], $request['gradebook_id'], $grade_format );

			$user_grade = new LD_GB_UserGrade( $request['user_id'], $request['gradebook_id'] );

			$edit_panel_user_grade = $this->edit_panel_user_grade( $request['user_id'], $request['gradebook_id'], $user_grade, $grade_format );

			$edit_panel_component_grade = $this->edit_panel_component_grade( $request['user_id'], $request['gradebook_id'], $request['group_id'], $request['component_id'], $user_grade, $grade_format );

			$edit_panel_grade_row = $this->edit_panel_grade_row( $request['user_id'], $request['gradebook_id'], $request['group_id'], $request['component_id'], $grade, $user_grade, $grade_format );

			$result['user_row']                   = $user_row;
			$result['edit_panel_user_grade']      = $edit_panel_user_grade;
			$result['edit_panel_component_grade'] = $edit_panel_component_grade;
			$result['edit_panel_grade_row']       = $edit_panel_grade_row;

			return new WP_REST_Response( $result );
		} catch ( Exception $exception ) {
			ob_clean();
			ob_start();
			do_action( 'ld_gb_frontend_gradebook_notice', $exception->getMessage(), 'alert' );
			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'html'      => $html,
					'exception' => [
						'message' => $exception->getMessage(),
						'trace'   => $exception->getTraceAsString(),
					],
				],
				500
			);
		}   }

	/**
	 * Deletes a Manual Grade for a given User+Gradebook+Component
	 *
	 * @since 2.0.0
	 *
	 * @param array $request Params passed to the Request Object
	 *
	 * @return WP_REST_Response REST Response
	 */
	public function delete_manual_grade( $request ) {
		try {
			if ( ld_gb_get_option_field( 'disable_manual_grades', false ) ) {
				$message = __( 'Manual Grades are disabled', 'learndash-gradebook' );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			}

			if ( ! isset( $_POST['grade'] ) || ! $_POST['grade'] ) {
				$message = _x( 'No grade data was passed to the API Endpoint', 'Manual Grade Delete: No Manual Grade Data was passed to the API Endpoint error', 'learndash-gradebook' );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			}

			$grade = wp_parse_args(
				$_POST['grade'],
				[
					'name'      => '',
					'component' => $request['component_id'],
					'gradebook' => $request['gradebook_id'],
					'user_id'   => $request['user_id'],
				]
			);

			$result = learndash_gradebook_delete_manual_grade( $grade );

			if ( $result['type'] == 'error' ) {
				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $result['error'] );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $result['error'],
						'html'    => $html,
					],
					500
				);
			}

			$grade_format = ( isset( $_POST['grade_format'] ) && $_POST['grade_format'] ? $_POST['grade_format'] : apply_filters( 'ld_gb_frontend_gradebook_grade_format', ld_gb_get_option_field( 'grade_display_mode', 'letter' ), $request['gradebook_id'], 0 ) );

			$user_row = $this->user_row( $request['user_id'], $request['gradebook_id'], $grade_format );

			$user_grade = new LD_GB_UserGrade( $request['user_id'], $request['gradebook_id'] );

			$edit_panel_user_grade = $this->edit_panel_user_grade( $request['user_id'], $request['gradebook_id'], $user_grade, $grade_format );

			$edit_panel_component_grade = $this->edit_panel_component_grade( $request['user_id'], $request['gradebook_id'], $request['group_id'], $request['component_id'], $user_grade, $grade_format );

			ob_start();

			$component = $this->get_component( $user_grade, $request['component_id'] );

			if ( empty( $component['grades'] ) ) {
				/**
				 * Edit Panel No Grades
				 *
				 * @hooked LD_GB_SC_FrontendGradebook::edit_panel_no_grades() 10
				 */
				do_action( 'ld_gb_frontend_gradebook_edit_panel_no_grades', $user_grade, $component, $request['user_id'], $request['gradebook_id'], $grade_format );
			}

			$edit_panel_no_grades = ob_get_clean();

			$result['user_row']                   = $user_row;
			$result['edit_panel_user_grade']      = $edit_panel_user_grade;
			$result['edit_panel_component_grade'] = $edit_panel_component_grade;
			$result['edit_panel_no_grades']       = $edit_panel_no_grades;

			return new WP_REST_Response( $result );
		} catch ( Exception $exception ) {
			ob_clean();
			ob_start();
			do_action( 'ld_gb_frontend_gradebook_notice', $exception->getMessage(), 'alert' );
			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'html'      => $html,
					'exception' => [
						'message' => $exception->getMessage(),
						'trace'   => $exception->getTraceAsString(),
					],
				],
				500
			);
		}   }

	/**
	 * Edits a Grade for a given User+Gradebook+Component
	 *
	 * @param array $request  Params passed to the Request Object
	 *
	 * @access public
	 * @since 2.0.0
	 * @return WP_REST_Response REST Response
	 */
	public function edit_grade( $request ) {
		try {
			if ( ! isset( $_POST['grade'] ) || ! $_POST['grade'] ) {
				$message = __( 'No grade data was passed to the API Endpoint', 'learndash-gradebook' );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			}

			$grade = wp_parse_args(
				$_POST['grade'],
				[
					'score'         => 0,
					'name'          => '',
					'previous_name' => '',
					'status'        => '',
					'component'     => $request['component_id'],
					'gradebook'     => $request['gradebook_id'],
					'user_id'       => $request['user_id'],
					'type'          => 'manual',
				]
			);

			if ( $grade['type'] == 'manual' && ld_gb_get_option_field( 'disable_manual_grades', false ) ) {
				$message = __( 'Manual Grades are disabled', 'learndash-gradebook' );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			}

			if ( $grade['type'] == 'manual' ) {
				$result = learndash_gradebook_update_manual_grade( $grade, true );
			} else {
				$result = learndash_gradebook_edit_post_driven_grade( $grade );
			}

			if ( $result['type'] == 'error' ) {
				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $result['error'] );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $result['error'],
						'html'    => $html,
					],
					500
				);
			}

			$grade_format = ( isset( $_POST['grade_format'] ) && $_POST['grade_format'] ? $_POST['grade_format'] : apply_filters( 'ld_gb_frontend_gradebook_grade_format', ld_gb_get_option_field( 'grade_display_mode', 'letter' ), $request['gradebook_id'], 0 ) );

			$user_row = $this->user_row( $request['user_id'], $request['gradebook_id'], $grade_format );

			$user_grade = new LD_GB_UserGrade( $request['user_id'], $request['gradebook_id'] );

			$edit_panel_user_grade = $this->edit_panel_user_grade( $request['user_id'], $request['gradebook_id'], $user_grade, $grade_format );

			$edit_panel_component_grade = $this->edit_panel_component_grade( $request['user_id'], $request['gradebook_id'], $request['group_id'], $request['component_id'], $user_grade, $grade_format );

			$edit_panel_grade_row = $this->edit_panel_grade_row( $request['user_id'], $request['gradebook_id'], $request['group_id'], $request['component_id'], $grade, $user_grade, $grade_format );

			$result['user_row']                   = $user_row;
			$result['edit_panel_user_grade']      = $edit_panel_user_grade;
			$result['edit_panel_component_grade'] = $edit_panel_component_grade;
			$result['edit_panel_grade_row']       = $edit_panel_grade_row;

			return new WP_REST_Response( $result );
		} catch ( Exception $exception ) {
			ob_clean();
			ob_start();
			do_action( 'ld_gb_frontend_gradebook_notice', $exception->getMessage(), 'alert' );
			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'html'      => $html,
					'exception' => [
						'message' => $exception->getMessage(),
						'trace'   => $exception->getTraceAsString(),
					],
				],
				500
			);
		}   }

	/**
	 * Overrides the Component Grade for a given User within a Gradebook
	 *
	 * @param array $request  Params passed to the Request Object
	 *
	 * @access public
	 * @since 2.0.0
	 * @return WP_REST_Response REST Response
	 */
	public function override_component_grade( $request ) {
		try {
			if ( ld_gb_get_option_field( 'disable_component_override', false ) ) {
				$message = __( 'Component Grade Overrides are disabled', 'learndash-gradebook' );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			}

			if ( ! isset( $_POST['score'] ) || $_POST['score'] === false ) {
				$message = _x( 'No Component grade data was passed to the API Endpoint', 'Component Grade Override: No Component Grade Data was passed to the API Endpoint error', 'learndash-gradebook' );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			}

			$result = learndash_gradebook_update_component_grade_override( $request['user_id'], $request['gradebook_id'], $request['component_id'], $_POST['score'] );

			$grade_format = ( isset( $_POST['grade_format'] ) && $_POST['grade_format'] ? $_POST['grade_format'] : apply_filters( 'ld_gb_frontend_gradebook_grade_format', ld_gb_get_option_field( 'grade_display_mode', 'letter' ), $request['gradebook_id'], 0 ) );

			$user_row = $this->user_row( $request['user_id'], $request['gradebook_id'], $grade_format );

			$user_grade = new LD_GB_UserGrade( $request['user_id'], $request['gradebook_id'] );

			$edit_panel_user_grade = $this->edit_panel_user_grade( $request['user_id'], $request['gradebook_id'], $user_grade, $grade_format );

			$edit_panel_component_grade = $this->edit_panel_component_grade( $request['user_id'], $request['gradebook_id'], $request['group_id'], $request['component_id'], $user_grade, $grade_format );

			$result['user_row']                   = $user_row;
			$result['edit_panel_user_grade']      = $edit_panel_user_grade;
			$result['edit_panel_component_grade'] = $edit_panel_component_grade;

			return new WP_REST_Response( $result );
		} catch ( Exception $exception ) {
			ob_clean();
			ob_start();
			do_action( 'ld_gb_frontend_gradebook_notice', $exception->getMessage(), 'alert' );
			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'html'      => $html,
					'exception' => [
						'message' => $exception->getMessage(),
						'trace'   => $exception->getTraceAsString(),
					],
				],
				500
			);
		}   }

	/**
	 * Deletes a Component Grade Override for a given User within a Gradebook
	 *
	 * @param array $request  Params passed to the Request Object
	 *
	 * @access public
	 * @since 2.0.0
	 * @return WP_REST_Response REST Response
	 */
	public function delete_component_override( $request ) {
		try {
			if ( ld_gb_get_option_field( 'disable_component_override', false ) ) {
				$message = __( 'Component Grade Overrides are disabled', 'learndash-gradebook' );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			}

			$result = learndash_gradebook_delete_component_grade_override( $request['user_id'], $request['gradebook_id'], $request['component_id'] );

			if ( is_wp_error( $result ) ) {
				$message = implode( ';', $result->get_error_messages() );

				ob_start();
				do_action( 'ld_gb_frontend_gradebook_notice', $message );
				$html = ob_get_clean();

				return new WP_REST_Response(
					[
						'message' => $message,
						'html'    => $html,
					],
					500
				);
			} elseif ( $result === true ) {
				// All overrides were deleted
				$result = [];
			}

			$grade_format = ( isset( $_POST['grade_format'] ) && $_POST['grade_format'] ? $_POST['grade_format'] : apply_filters( 'ld_gb_frontend_gradebook_grade_format', ld_gb_get_option_field( 'grade_display_mode', 'letter' ), $request['gradebook_id'], 0 ) );

			$user_row = $this->user_row( $request['user_id'], $request['gradebook_id'], $grade_format );

			$user_grade = new LD_GB_UserGrade( $request['user_id'], $request['gradebook_id'] );

			$edit_panel_user_grade = $this->edit_panel_user_grade( $request['user_id'], $request['gradebook_id'], $user_grade, $grade_format );

			$edit_panel_component_grade = $this->edit_panel_component_grade( $request['user_id'], $request['gradebook_id'], $request['group_id'], $request['component_id'], $user_grade, $grade_format );

			$result['user_row']                   = $user_row;
			$result['edit_panel_user_grade']      = $edit_panel_user_grade;
			$result['edit_panel_component_grade'] = $edit_panel_component_grade;

			return new WP_REST_Response( $result );
		} catch ( Exception $exception ) {
			ob_clean();
			ob_start();
			do_action( 'ld_gb_frontend_gradebook_notice', $exception->getMessage(), 'alert' );
			$html = ob_get_clean();

			return new WP_REST_Response(
				[
					'html'      => $html,
					'exception' => [
						'message' => $exception->getMessage(),
						'trace'   => $exception->getTraceAsString(),
					],
				],
				500
			);
		}   }

	/**
	 * Returns a list of Gradebooks to be used within the Gutenberg Block
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request Object.
	 *
	 * @return WP_REST_Response Response Object
	 */
	public function gutenberg_get_gradebooks( $request ) {
		try {
			$query_args = [
				'post_type' => 'gradebook',
				's'         => $request->get_param( 's' ),
			];

			$total_query = new WP_Query(
				array_merge(
					$query_args,
					[
						'posts_per_page' => -1,
						'fields'         => 'ids',
					]
				)
			);

			$total = count( $total_query->posts );

			$per_page = $request->get_param( 'per_page' );
			$per_page = ( $per_page ) ? (int) $per_page : 10;

			$offset = $request->get_param( 'offset' );
			$offset = ( $offset ) ? (int) $offset : 0;

			$results = new WP_Query(
				array_merge(
					$query_args,
					[
						'offset'         => $offset,
						'posts_per_page' => $per_page,
					]
				)
			);

			$has_more = false;
			$options  = [];

			if ( $results->have_posts() ) {
				$posts = wp_list_pluck( $results->posts, 'post_title', 'ID' );

				// Converts the data to something that our Select field will tolerate
				$options = array_values(
					array_map(
						function ( $key, $value ) {
							return [
								'value' => $key,
								'label' => $value,
							];
						},
						array_keys( $posts ),
						$posts
					)
				);

				$processed_count = count( $results->posts ) + $offset;

				// Check if there are more to be found
				if ( $processed_count < $total ) {
					$has_more = true;
				}
			}

			return new WP_REST_Response(
				[
					'options' => $options,
					'hasMore' => $has_more,
					'total'   => (int) $total,
				]
			);
		} catch ( Exception $exception ) {
			return new WP_REST_Response(
				[
					'options' => [],
					'hasMore' => false,
				],
				500
			);
		}   }

	/**
	 * Returns a specific Gradebook to be used when populating a default value
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request Object.
	 *
	 * @return WP_REST_Response Response Object
	 */
	public function gutenberg_get_gradebook( $request ) {
		try {
			global $wpdb;

			$results = $wpdb->get_row( $wpdb->prepare( "SELECT id as value, post_title as label FROM {$wpdb->prefix}posts WHERE id = %d", $request->get_param( 'id' ) ) );

			return new WP_REST_Response(
				[
					'gradebook' => ( $results ) ? $results : [],
				]
			);
		} catch ( Exception $exception ) {
			return new WP_REST_Response(
				[
					'gradebook' => [],
				],
				500
			);
		}   }

	/**
	 * Returns a list of Users to be used within the Gutenberg Block
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request Object.
	 *
	 * @return WP_REST_Response Response Object
	 */
	public function gutenberg_get_users( $request ) {
		try {
			$query_args = [
				'search' => "*{$request->get_param( 's' )}*",
			];

			$total_query = new WP_User_Query(
				array_merge(
					$query_args,
					[
						'number' => -1,
						'fields' => 'ID',
					]
				)
			);

			$total = $total_query->get_total();

			$per_page = $request->get_param( 'per_page' );
			$per_page = ( $per_page ) ? (int) $per_page : 10;

			$offset = $request->get_param( 'offset' );
			$offset = ( $offset ) ? (int) $offset : 0;

			$current_query = new WP_User_Query(
				array_merge(
					$query_args,
					[
						'offset' => $offset,
						'number' => $per_page,
					]
				)
			);

			$has_more = false;
			$options  = [];

			if ( $results = $current_query->get_results() ) {
				// Converts the data to something that our Select field will tolerate
				$options = array_map(
					function ( $user ) {
						$user = new WP_User( $user->ID );

						$label = $user->user_login;

						if ( $user->user_email ) {
								$label .= " | {$user->user_email}";
						}

						return [
							'value' => $user->ID,
							'label' => $label,
						];              },
					$results
				);

				$processed_count = count( $results ) + $offset;

				// Check if there are more to be found
				if ( $processed_count < $total ) {
					$has_more = true;
				}
			}

			return new WP_REST_Response(
				[
					'options' => $options,
					'hasMore' => $has_more,
					'total'   => (int) $total,
				]
			);
		} catch ( Exception $exception ) {
			return new WP_REST_Response(
				[
					'options' => [],
					'hasMore' => false,
				],
				500
			);
		}   }

	/**
	 * Allows Select2 to search Gradebooks
	 *
	 * @param \WP_REST_Request $request  Request object.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return \WP_REST_Response          Response object.
	 */
	public function select2_get_gradebooks( $request ): \WP_REST_Response {
		try {
			$search = $request->get_param( 'term' );
			$page   = $request->get_param( 'page' );

			if ( empty( $page ) ) {
				$page = 1;
			}

			// By default WP_Query search all post title, content, and excerpt.
			// This filter modify it to only search in post title.
			add_filter(
				'posts_search',
				function ( $search, $wp_query ) {
					if ( isset( $wp_query->query['ld_gb_action'] ) && $wp_query->query['ld_gb_action'] === 'ld_gb_get_gradebook_list' ) {
						$search = preg_replace( '/(OR)\s.*?post_(excerpt|content)\sLIKE\s.*?\)/', '', $search );
					}

					return $search;
				},
				10,
				2
			);

			$posts_per_page = 10;

			$query = new \WP_Query(
				/**
				 * Filters query args used when generating aa dropdown for a Gradebook dropdown
				 *
				 * @param array $args   \WP_Query args.
				 */
				apply_filters(
					'ld_gb_adminpage_gradebook_select_query_args',
					[
						'post_type'      => 'gradebook',
						'post_status'    => 'publish',
						'posts_per_page' => $posts_per_page,
						'paged'          => ( $page ),
						's'              => $search,
						'ld_gb_action'   => 'ld_gb_get_gradebook_list',
						'fields'         => 'ids',
					]
				)
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

			$count_query = new \WP_Query(
				/** This filter is documented above */
				apply_filters(
					'ld_gb_adminpage_gradebook_select_query_args',
					[
						'post_type'      => 'gradebook',
						'post_status'    => 'publish',
						'posts_per_page' => -1,
						's'              => $search,
						'ld_gb_action'   => 'ld_gb_get_gradebook_list',
						'fields'         => 'ids',
					]
				)
			);

			$gradebook_count = $count_query->found_posts;

			return new \WP_REST_Response(
				[
					'results'    => $gradebooks,
					'pagination' => [
						'more' => $gradebook_count > ( $posts_per_page * ( $page ) ),
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
	 * Returns a specific User to be used when populating a default value
	 *
	 * @param WP_REST_Request $request   Request Object.
	 *
	 * @access public
	 * @since 3.0.0
	 * @return WP_REST_Response            Response Object
	 */
	public function gutenberg_get_user( $request ) {
		try {
			global $wpdb;

			$results = $wpdb->get_row( $wpdb->prepare( "SELECT id as value, CONCAT( user_login, ' | ', user_email ) as label FROM {$wpdb->prefix}users WHERE id = %d", $request->get_param( 'id' ) ) );

			// In case an email address is not stored
			$results->label = rtrim( $results->label, ' | ' );

			return new WP_REST_Response(
				[
					'user' => ( $results ) ? $results : [],
				]
			);
		} catch ( Exception $exception ) {
			return new WP_REST_Response(
				[
					'user' => [],
				],
				500
			);
		}   }

	/**
	 * Returns the Gradebook User Row HTML
	 * Used by many of the API Endpoints, so this is here to DRY it up
	 *
	 * @param integer $user_id       WP_User ID
	 * @param integer $gradebook_id  WP_Post ID
	 * @param string  $grade_format  Grade Display Format
	 *
	 * @access protected
	 * @since 2.0.0
	 * @return string                  HTML
	 */
	protected function user_row( $user_id, $gradebook_id, $grade_format = 'letter' ) {
		ob_start();

		$user_columns = learndash_gradebook_get_user_columns( $gradebook_id, 0 );

		// Generate Gradebook Data for this user Specifically
		$data = learndash_gradebook_get_gradebook_data(
			$gradebook_id,
			0,
			[
				'include' => [ $user_id ],
			]
		);

		$user_grade = $data['grades'][ $user_id ];

		$components = ld_gb_get_field( 'components', $gradebook_id );

		/**
		 * Gradebook User Row
		 *
		 * @hooked LD_GB_SC_FrontendGradebook::table_row() 10
		 */
		do_action( 'ld_gb_frontend_gradebook_table_row', $user_id, $user_columns, $user_grade, $grade_format, $components, $gradebook_id, 0 );

		$user_row = ob_get_clean();

		return $user_row;   }

	/**
	 * Returns the Edit Panel User Grade HTML
	 * Used by many of the API Endpoints, so this is here to DRY it up
	 *
	 * @param integer $user_id       WP_User ID
	 * @param integer $gradebook_id  WP_Post ID
	 * @param object  $user_grade    LD_GB_UserGrade object
	 * @param string  $grade_format  Grade Display Format
	 *
	 * @access protected
	 * @since 2.0.0
	 * @return string                  HTML
	 */
	protected function edit_panel_user_grade( $user_id, $gradebook_id, $user_grade, $grade_format = 'letter' ) {
		ob_start();

		/**
		 * Edit Panel User Overall Grade
		 *
		 * @hooked LD_GB_SC_FrontendGradebook::table_row() 10
		 */
		do_action( 'ld_gb_frontend_gradebook_edit_panel_user_grade', $user_id, $gradebook_id, $user_grade, $grade_format );

		$edit_panel_user_grade = ob_get_clean();

		return $edit_panel_user_grade;  }

	/**
	 * Returns the Edit Panel Component Grade HTML
	 * Used by many of the API Endpoints, so this is here to DRY it up
	 *
	 * @param integer $user_id       WP_User ID
	 * @param integer $gradebook_id  WP_Post ID
	 * @param integer $group_id      Group ID
	 * @param integer $component_id  Component Index
	 * @param object  $user_grade    LD_GB_UserGrade Object
	 * @param string  $grade_format  Grade Display Format
	 *
	 * @access protected
	 * @since 2.0.0
	 * @updated 4.1.2
	 * @return string                  HTML
	 */
	protected function edit_panel_component_grade( $user_id, $gradebook_id, $group_id, $component_id, $user_grade, $grade_format = 'letter' ) {
		ob_start();

		$component = $this->get_component( $user_grade, $component_id );

		/**
		 * Edit Panel Component Grade
		 *
		 * @hooked LD_GB_SC_FrontendGradebook::edit_panel_component_grade() 10
		 */
		do_action( 'ld_gb_frontend_gradebook_edit_panel_component_grade', $component, $grade_format, $user_id, $gradebook_id, $group_id );

		$edit_panel_component_grade = ob_get_clean();

		return $edit_panel_component_grade; }

	/**
	 * Edit Panel Grade Row
	 * Used by many of the API Endpoints, so this is here to DRY it up
	 *
	 * @param integer $user_id       WP_User ID
	 * @param integer $gradebook_id  WP_Post ID
	 * @param integer $component_id  Component Index
	 * @param array   $grade         Grade Data Array passed to the Endpoint
	 * @param object  $user_grade    LD_GB_UserGrade Object
	 * @param string  $grade_format  Grade Display Format
	 *
	 * @access protected
	 * @since 2.0.0
	 * @updated 4.1.2
	 * @return string                  HTML
	 */
	protected function edit_panel_grade_row( $user_id, $gradebook_id, $group_id, $component_id, $grade, $user_grade, $grade_format = 'letter' ) {
		$component = $this->get_component( $user_grade, $component_id );

		// Get the latest Grade data
		$found = false;
		foreach ( $component['grades'] as $component_grade ) {
			if ( $grade['type'] == 'manual' ) {
				if ( $grade['name'] == $component_grade['name'] ) {
					$found = true;
				}
			} elseif ( $grade['post_id'] == $component_grade['post_id'] ) {
				$found = true;
			}

			if ( $found ) {
				$grade = $component_grade;
				break;
			}
		}

		ob_start();

		/**
		 * Edit Panel Grade Row
		 *
		 * @hooked LD_GB_SC_FrontendGradebook::edit_panel_grade_row() 10
		 */
		do_action( 'ld_gb_frontend_gradebook_edit_panel_grade_row', $grade, $user_grade, $component, $user_id, $gradebook_id, $group_id, $grade_format );

		$edit_panel_grade_row = ob_get_clean();

		return $edit_panel_grade_row;   }

	/**
	 * Gets Component Data from a User Grade by Index
	 *
	 * @param object $user_grade    LD_GB_UserGrade Object
	 * @param array  $component_id  Component Index
	 *
	 * @access protected
	 * @since 2.0.0
	 * @return array                  Component Data
	 */
	protected function get_component( $user_grade, $component_id ) {
		$components = $user_grade->get_components();

		$component = [];
		foreach ( $components as $component ) {
			if ( $component['id'] == $component_id ) {
				break;
			}
		}

		return $component;  }

	/**
	 * Converts an Array of associative Arrays to CSV
	 * Each interior associative Array is equivalent to one row of the CSV
	 * Array Keys in the first Array are used for the first row of the CSV
	 *
	 * @param array  $array      Array of associative Arrays
	 * @param string $separator  CSV separator
	 * @param string $delimiter  String delimiter
	 *
	 * @access protected
	 * @since 2.0.0
	 * @return string              CSV
	 */
	public static function array_to_csv( $array, $separator = ',', $delimiter = '"' ) {
		// No rows provided, bail
		if ( ! isset( $array[0] ) || ! $array[0] ) {
			return false;
		}

		$csv = '';

		$csv .= self::get_csv_line( array_keys( $array[0] ), $separator, $delimiter );

		foreach ( $array as $row ) {
			$csv .= self::get_csv_line( $row, $separator, $delimiter );
		}

		$csv = rtrim( $csv, "\n" );

		return $csv;    }

	/**
	 * Not only does PHP require you to create a file pointer to convert an Array to CSV, but you have to do it line by line. Strangely limiting.
	 *
	 * @param array  $line_array  Array of CSV line values
	 * @param string $separator   CSV separator
	 * @param string $delimiter   String delimiter
	 *
	 * @access protected
	 * @since 2.0.0
	 * @return string               CSV Line
	 */
	public static function get_csv_line( $line_array, $separator = ',', $delimiter = '"' ) {
		$file_pointer = fopen( 'php://temp', 'r+b' );

		fputcsv( $file_pointer, $line_array, $separator, $delimiter );

		rewind( $file_pointer );

		$csv_line = stream_get_contents( $file_pointer );

		fclose( $file_pointer );

		return $csv_line;   }

	/**
	 * Check if this User can view the requested Gradebook Data.
	 *
	 * @param object $request  WP_REST_Request object
	 *
	 * @access public
	 * @since 2.0.0
	 * @return boolean           Whether the User has access to this endpoint or not
	 */
	public static function permission_callback_can_view_gradebook( $request ) {
		if ( ! current_user_can( 'view_gradebook' ) ) {
			return false;
		}

		// If we're checking against a specific Group and they are not an Admin or a Group Leader for that Group, bail
		if ( ( $request->get_param( 'group_id' ) && $request->get_param( 'group_id' ) != '0' ) &&
			! ld_gb_is_super_admin() &&
			! learndash_gradebook_is_user_group_leader_of_group( $request->get_param( 'group_id' ) ) ) {
			return false;
		}

		// If we're not checking against a specific Group and they are not an Admin, bail
		if ( ( ! $request->get_param( 'group_id' ) || $request->get_param( 'group_id' ) == '0' ) &&
			! ld_gb_is_super_admin() ) {
			return false;
		}

		return apply_filters( 'learndash_gradebook_api_permission_callback_can_view_gradebook', true, $request );   }
}
