<?php
/**
 * Adds telemetry data to the WP Site Health screen
 *
 * @since 4.3.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_Health_Check
 *
 * Adds telemetry data to the WP Site Health screen
 *
 * @since 4.3.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */
class LD_GB_Health_Check extends Learndash_Site_Health {
	private const SITE_HEALTH_KEY = 'learndash_gradebook';

	/**
	 * LD_GB_Health_Check constructor.
	 *
	 * @since 4.3.0
	 */
	public function __construct() {
		add_filter( 'debug_information', [ $this, 'add_site_health_info' ] );   }

	/**
	 * Add Telemetry info to Site Health.
	 *
	 * @param array $debug_info Info.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return array Debug info.
	 */
	public function add_site_health_info( array $debug_info ): array {
		$debug_info[ self::SITE_HEALTH_KEY ] = [
			'label'  => __( 'Gradebook by LearnDash', 'learndash-gradebook' ),
			'fields' => $this->get_fields(),
		];

		return $debug_info; }

	/**
	 * Maps the telemetry data to the Site Health fields.
	 *
	 * @access private
	 * @since 4.3.0
	 * @return array
	 */
	private function get_fields(): array {
		if ( ! empty( $this->fields ) ) {
			return $this->fields;
		}

		$this->fields = array_merge(
			$this->map_general_fields(),
			$this->map_post_count_fields(),
			$this->map_settings_fields(),
			$this->map_statistics_fields(),
		);

		return $this->fields;   }

	/**
	 * Maps general fields.
	 *
	 * @access private
	 * @since 4.3.0
	 * @return array
	 */
	private function map_general_fields(): array {
		$last_upgrade  = get_option( 'ld_gb_last_upgrade', 0 );
		$last_upgraded = get_option( 'ld_gb_last_upgraded', 0 );

		$license_data = get_option( 'learndash_gradebook_license' );

		return [
			'version'           => [
				'label' => __( 'Version', 'learndash-gradebook' ),
				'value' => LEARNDASH_GRADEBOOK_VERSION,
			],
			'last_upgraded'     => [
				'label' => __( 'Last database upgrade ran', 'learndash-gradebook' ),
				'value' => $last_upgraded > 0 ? learndash_adjust_date_time_display( $last_upgraded ) : __( 'Never', 'learndash-gradebook' ),
				'debug' => $last_upgraded,
			],
			'last_upgrade'      => [
				'label' => __( 'Last database upgrade version', 'learndash-gradebook' ),
				'value' => ( $last_upgrade ) ? $last_upgrade : __( 'N/A', 'learndash-gradebook' ),
				'debug' => $last_upgrade,
			],
			'license_validated' => [
				'label' => __( 'License validated', 'learndash-gradebook' ),
				'value' => $this->bool_to_yes_no_string( ! empty( $license_data ) ),
				'debug' => ( ! empty( $license_data ) ) ? $license_data : [],
			],
		];  }

	/**
	 * Maps fields with post counts.
	 *
	 * @access private
	 * @since 4.3.0
	 * @return array
	 */
	private function map_post_count_fields(): array {
		return [
			'gradebook_count' => [
				'label' => __( 'Number of Gradebooks created', 'learndash-gradebook' ),
				'value' => wp_count_posts( 'gradebook' )->publish,
			],
		];  }

	/**
	 * Maps settings fields.
	 *
	 * @since 4.3.0
	 *
	 * @return array
	 */
	private function map_settings_fields(): array {
		$settings_sections = LearnDash_Gradebook()->settings_page->get_settings();

		$settings_sections = $this->validate_fields( $settings_sections );

		$settings_fields = [];

		foreach ( $settings_sections as $section ) {
			foreach ( $section['fields'] as $setting ) {
				$field_id = $setting['id'];

				$label = $setting['label'];

				$default = ( isset( $setting['args']['default'] ) && $setting['args']['default'] ) ? $setting['args']['default'] : '';

				$raw_value = ld_gb_get_option_field( $field_id, $default );

				if ( isset( $setting['args']['role_selector'] ) && $setting['args']['role_selector'] && isset( $setting['args']['value'] ) && $setting['args']['value'] ) {
					$raw_value = $setting['args']['value'];
				}

				if ( is_array( $raw_value ) || is_object( $raw_value ) ) {
					$raw_value = $raw_value;
				}

				if ( $setting['callback'] === 'ld_gb_do_field_toggle' ) {
					$value = $this->bool_to_yes_no_string( $raw_value );
				} elseif ( $setting['callback'] === 'ld_gb_do_field_radio' ) {
					$value = $this->get_radio_value( $raw_value, $setting );
				} elseif ( in_array(
					$setting['callback'],
					[
						'ld_gb_do_field_checkbox',
						'ld_gb_do_field_select',
					],
					true
				) ) {
					$value = $this->get_select_or_checkbox_values( $raw_value, $setting );
				} elseif ( $setting['callback'] === 'ld_gb_do_field_repeater' ) {
					$setting['args'] = wp_parse_args(
						$setting['args'],
						[
							'fields' => [],
						]
					);

					$value = $raw_value;

					if ( count( $setting['args']['fields'] ) === 1 ) {
						$value = array_map(
							function ( $row ) use ( $setting ) {
								$key = false;

								foreach ( $setting['args']['fields'] as $key => $field ) {
									break;
								}

								if ( ! $key ) {
									return $row;
								}

								if ( in_array(
									$setting['args']['fields'][ $key ]['type'],
									[
										'select',
										'checkbox',
									],
									true
								) ) {
									return $this->get_select_or_checkbox_values( $row[ $key ], $setting['args']['fields'][ $key ] );
								} elseif ( $setting['args']['fields'][ $key ]['type'] === 'radio' ) {
									return $this->get_radio_value( $row[ $key ], $setting['args']['fields'][ $key ] );
								}

								return $row[ $key ];
							},
							$value
						);
					} else {
						foreach ( $setting['args']['fields'] as $key => $field ) {
							$field['args'] = wp_parse_args(
								$field['args'],
								[
									'label' => '',
								]
							);

							$setting['args']['fields'][ $key ]['label'] = $field['args']['label'];
						}

						$value = array_map(
							function ( $row ) use ( $setting ) {
								$values = [];

								foreach ( $row as $key => $value ) {
									if ( in_array(
										$setting['args']['fields'][ $key ]['type'],
										[
											'select',
											'checkbox',
										],
										true
									) ) {
										$values[] = $this->get_select_or_checkbox_values( $value, $setting['args']['fields'][ $key ], true );
									} elseif ( $setting['args']['fields'][ $key ]['type'] === 'radio' ) {
										$values[] = $this->get_radio_value( $value, $setting['args']['fields'][ $key ], true );
									} else {
										$values[] = "{$setting['args']['fields'][ $key ]['label']}: {$value}";
									}
								}

								return implode( ', ', $values );
							},
							$value
						);
					}
				} else {
					$value = $raw_value;
				}

				$settings_fields[ $field_id ] = [
					'label' => $label,
					'value' => $value,
					'debug' => $raw_value,
				];
			}
		}

		return $settings_fields;
	}

	/**
	 * Maps statistics fields.
	 *
	 * @since 4.3.0
	 *
	 * @return array
	 */
	private function map_statistics_fields(): array {
		$fields = [
			'gradebook_largest_user_count'   => [
				'label' => __( 'Largest number of users recorded to be shown for a Gradebook', 'learndash-gradebook' ),
				'value' => ld_gb_get_largest_total_users(),
			],
			'restart_quickstart_guide_count' => [
				'label' => __( 'Number of times the Quickstart Guide has been restarted', 'learndash-gradebook' ),
				'value' => ld_gb_get_quickstart_guide_reset_counter(),
			],
		];

		$unserialized_post_meta = LearnDash_Gradebook()->posttypes->gradebook->get_settings_metabox_fields(); // @cspell:disable-line.

		$select_checkbox_radio_post_meta = array_filter(
			$unserialized_post_meta,
			function ( $field ) {
				$field = wp_parse_args(
					$field,
					[
						'callback' => '',
					]
				);

				return in_array(
					$field['callback'],
					[
						'ld_gb_do_field_select',
						'ld_gb_do_field_checkbox',
						'ld_gb_do_field_radio',
					]
				);
			}
		);

		if ( ! empty( $select_checkbox_radio_post_meta ) ) {
			$fields_with_value_labels = [];

			foreach ( $select_checkbox_radio_post_meta as $setting_key => $setting ) {
				$setting = wp_parse_args(
					$setting,
					[
						'label'   => '',
						'options' => [],
					]
				);

				foreach ( $setting['options'] as $option_key => $option_value ) {
					// RBM Field Helpers technically expects an Array to build out option values for select fields, so we account for that here.
					if ( is_array( $option_value ) ) {
						$option_value = wp_parse_args(
							$option_value,
							[
								'value' => '',
								'text'  => '',
							]
						);

						$option_key   = $option_value['value'];
						$option_value = $option_value['text'];
					}

					$fields_with_value_labels[ "{$setting_key}_{$option_key}" ] = [
						'label' => sprintf(
							// translators: %1$s: Option Value, %2$s: Setting Label.
							__( 'Number of Gradebooks with "%1$s" chosen for %2$s', 'learndash-gradebook' ),
							$option_value,
							$setting['label']
						),
						'value' => $this->count_by_meta_value_within_meta_key( 'gradebook', "ld_gb_{$setting['name']}", $option_key ),
					];
				}
			}

			$fields = array_merge( $fields, $fields_with_value_labels );
		}

		$fields = array_merge(
			$fields,
			[
				'include_all_users'          => [
					'label' => __( 'Number of Gradebooks with "Include All Users In Gradebook" enabled', 'learndash-gradebook' ),
					'value' => $this->count_by_meta_value_within_meta_key( 'gradebook', 'ld_gb_include_all_users', '1' ),
				],
				'gradebook_weighting_enable' => [
					'label' => __( 'Number of Gradebooks with "Enable Gradebook Weighting" turned on', 'learndash-gradebook' ),
					'value' => $this->count_by_meta_value_within_meta_key( 'gradebook', 'ld_gb_gradebook_weighting_enable', '1' ),
				],
				'lessons_all'                => [
					// translators: %s: Lessons.
					'label' => sprintf( __( 'Number of Gradebooks with "All %s" enabled for at least one Component', 'learndash-gradebook' ), learndash_get_custom_label( 'lessons' ) ),
					'value' => $this->count_by_serialized_setting_within_meta_key( 'gradebook', 'ld_gb_components', 'lessons_all', '1' ),
				],
				'topics_all'                 => [
					// translators: %s: Topics.
					'label' => sprintf( __( 'Number of Gradebooks with "All %s" enabled for at least one Component', 'learndash-gradebook' ), learndash_get_custom_label( 'topics' ) ),
					'value' => $this->count_by_serialized_setting_within_meta_key( 'gradebook', 'ld_gb_components', 'topics_all', '1' ),
				],
				'quizzes_all'                => [
					// translators: %s: Quizzes.
					'label' => sprintf( __( 'Number of Gradebooks with "All %s" enabled for at least one Component', 'learndash-gradebook' ), learndash_get_custom_label( 'quizzes' ) ),
					'value' => $this->count_by_serialized_setting_within_meta_key( 'gradebook', 'ld_gb_components', 'quizzes_all', '1' ),
				],
				'assignments_all'            => [
					// translators: %s: Assignments.
					'label' => sprintf( __( 'Number of Gradebooks with "All %s" enabled for at least one Component', 'learndash-gradebook' ), learndash_get_custom_label( 'assignments' ) ),
					'value' => $this->count_by_serialized_setting_within_meta_key( 'gradebook', 'ld_gb_components', 'assignments_all', '1' ),
				],
				'lessons'                    => [
					// translators: %s: Lessons.
					'label' => sprintf( __( 'Number of Gradebooks with specific %s configured for at least one Component', 'learndash-gradebook' ), learndash_get_custom_label( 'lessons' ) ),
					'value' => $this->count_by_serialized_setting_within_meta_key( 'gradebook', 'ld_gb_components', 'lessons', ';.[^;}]*' ),
				],
				'topics'                     => [
					// translators: %s: Topics.
					'label' => sprintf( __( 'Number of Gradebooks with specific %s configured for at least one Component', 'learndash-gradebook' ), learndash_get_custom_label( 'topics' ) ),
					'value' => $this->count_by_serialized_setting_within_meta_key( 'gradebook', 'ld_gb_components', 'topics', ';.[^;}]*' ),
				],
				'quizzes'                    => [
					// translators: %s: Quizzes.
					'label' => sprintf( __( 'Number of Gradebooks with specific %s configured for at least one Component', 'learndash-gradebook' ), learndash_get_custom_label( 'quizzes' ) ),
					'value' => $this->count_by_serialized_setting_within_meta_key( 'gradebook', 'ld_gb_components', 'quizzes', ';.[^;}]*' ),
				],
				'assignment_lessons'         => [
					'label' => sprintf(
						// translators: %1$s: Assignments, %2$s: Lessons.
						__( 'Number of Gradebooks with specific %1$s from %2$s configured for at least one Component', 'learndash-gradebook' ),
						learndash_get_custom_label( 'assignments' ),
						learndash_get_custom_label( 'lessons' )
					),
					'value' => $this->count_by_serialized_setting_within_meta_key( 'gradebook', 'ld_gb_components', 'assignment_lessons', ';.[^;}]*' ),
				],
				'assignment_topics'          => [
					'label' => sprintf(
						// translators: %1$s: Assignments, %2$s: Topics.
						__( 'Number of Gradebooks with specific %1$s from %2$s configured for at least one Component', 'learndash-gradebook' ),
						learndash_get_custom_label( 'assignments' ),
						learndash_get_custom_label( 'topics' )
					),
					'value' => $this->count_by_serialized_setting_within_meta_key( 'gradebook', 'ld_gb_components', 'assignment_topics', ';.[^;}]*' ),
				],
			]
		);

		return $fields;
	}

	/**
	 * Returns a number of published posts filtered by a post type and a setting.
	 *
	 * This differs from Learndash_Site_Health's count_by_post_type_and_setting() by allowing any Post Type and by allowing an explicit Meta Key to be set. Unfortunately due to the necessity of an additional param, we couldn't just
	 * overload it.
	 *
	 * @since 4.3.0
	 *
	 * @param string $post_type     Post type key.
	 * @param string $meta_key      Meta key to look in.
	 * @param string $setting_key   Setting key to filter by.
	 * @param string $setting_value Optional. Setting value to filter by. Can be a regular expression. Default empty string.
	 *
	 * @return int Number of found Posts.
	 */
	protected function count_by_serialized_setting_within_meta_key( string $post_type, string $meta_key, string $setting_key, string $setting_value = '' ): int {
		$query_args = [
			'post_type'    => $post_type,
			'post_status'  => 'publish',
			'fields'       => 'ids',
			'nopaging'     => true,
			'meta_key'     => $meta_key,
			'meta_value'   => $this->map_meta_value_from_setting( $setting_key, $setting_value ),
			'meta_compare' => 'RLIKE',
		];

		$query = new WP_Query( $query_args );

		return $query->found_posts;
	}

	/**
	 * Returns a number of published posts filtered by Post Type and Meta Key matching a value.
	 *
	 * This couldn't use Learndash_Site_Health::count_by_post_type_and_setting() for similar reasons as LD_GB_Health_Check::count_by_serialized_setting_within_meta_key(), but with the added reason that this doesn't check for serialized
	 * values.
	 *
	 * @since 4.3.0
	 *
	 * @param string $post_type  Post type key.
	 * @param string $meta_key   Meta key to look in.
	 * @param string $meta_value Meta value to check against. Default empty string.
	 * @param string $compare    Comparison to use. Default LIKE.
	 *
	 * @return int of found Posts.
	 */
	protected function count_by_meta_value_within_meta_key( string $post_type, string $meta_key, $meta_value = '', string $compare = 'LIKE' ): int {
		$query_args = [
			'post_type'    => $post_type,
			'post_status'  => 'publish',
			'fields'       => 'ids',
			'nopaging'     => true,
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'meta_compare' => $compare,
		];

		$query = new WP_Query( $query_args );

		return $query->found_posts;
	}

	/**
	 * Ensures the Fields are set up as needed for the Health Check screen to show user-friendly values.
	 *
	 * @since 4.3.0
	 *
	 * @param array $settings_sections Array of Setting Section data.
	 *
	 * @return array Array of Setting Section data.
	 */
	private function validate_fields( $settings_sections ): array {
		foreach ( $settings_sections as &$section ) {
			foreach ( $section['fields'] as $setting_key => &$setting ) {
				$setting = wp_parse_args(
					$setting,
					[
						'label' => '',
						'args'  => [],
					]
				);

				$setting['args'] = wp_parse_args(
					$setting['args'],
					[
						'option_field' => true,
					]
				);

				if ( ! $setting['args']['option_field'] ) {
					unset( $section['fields'][ $setting_key ] );
					continue;
				}

				$field_id = $setting['id'];

				$label = ( isset( $setting['label'] ) && $setting['label'] ) ? $setting['label'] : $field_id;

				$label = trim( preg_replace( '/\<div\s*?class="fieldhelpers-field-description fieldhelpers-field-tip">[\s|\S]*?\<\/div\>/sim', '', $label ) );

				$setting['label'] = $label;

				if ( in_array(
					$field_id,
					[
						'gradebook_roles',
						'edit_gradebook_roles',
						'quickstart_roles',
					]
				) ) {
					$setting['args'] = wp_parse_args(
						$setting['args'],
						[
							'options' => [],
						]
					);

					global $wp_roles;

					// Ensure implied Administrator and Group Leader access are shown to the user.

					if ( ! isset( $setting['args']['options']['administrator'] ) ) {
						$setting['args']['options']['administrator'] = $wp_roles->get_names()['administrator'];
					}

					if ( ! isset( $setting['args']['options']['group_leader'] ) ) {
						$setting['args']['options']['group_leader'] = $wp_roles->get_names()['group_leader'];
					}
				}

				if ( in_array(
					$field_id,
					[
						'letter_grade_scale',
						'grade_color_scale',
					],
					true
				) ) {
					$setting['args'] = wp_parse_args(
						$setting['args'],
						[
							'fields' => [],
						]
					);

					foreach ( $setting['args']['fields'] as $key => $field ) {
						$field = wp_parse_args(
							$field,
							[
								'args' => [],
							]
						);

						$field['args'] = wp_parse_args(
							$field['args'],
							[
								'label' => '',
							]
						);

						if ( ! empty( $field['args']['label'] ) ) {
							continue;
						}

						// This fields don't have a label normally, so we need to define them so that the output to the user make sense.

						if ( $key === 'grade' ) {
							$setting['args']['fields'][ $key ]['args']['label'] = __( 'Grade', 'learndash-gradebook' );
						} elseif ( $key === 'color' ) {
							$setting['args']['fields'][ $key ]['args']['label'] = __( 'Color', 'learndash-gradebook' );
						} elseif ( $key === 'letter' ) {
							$setting['args']['fields'][ $key ]['args']['label'] = __( 'Letter', 'learndash-gradebook' );
						}
					}
				}

				if ( $setting['callback'] === 'ld_gb_do_field_select' ) {
					$setting['args'] = wp_parse_args(
						$setting['args'],
						[
							'options' => [],
						]
					);

					$new_options = [];

					foreach ( $setting['args']['options'] as $key => $value ) {
						if ( is_array( $value ) ) {
							$value = wp_parse_args(
								$value,
								[
									'text'  => '',
									'value' => '',
								]
							);

							if ( empty( $value['value'] ) ) {
								continue;
							}

							$new_options[ $value['value'] ] = $value['text'];
						}
					}

					if ( ! empty( $new_options ) ) {
						$setting['args']['options'] = $new_options;
					}
				}

				if ( $setting['callback'] === 'ld_gb_do_field_repeater' ) {
					$setting['args'] = wp_parse_args(
						$setting['args'],
						[
							'fields' => [],
						]
					);

					foreach ( $setting['args']['fields'] as $field_key => $field ) {
						$field = wp_parse_args(
							$field,
							[
								'type' => '',
							]
						);

						if ( $field['type'] !== 'select' ) {
							continue;
						}

						$field['args'] = wp_parse_args(
							$field['args'],
							[
								'options' => [],
							]
						);

						$new_options = [];

						foreach ( $field['args']['options'] as $option_key => $option_value ) {
							if ( ! is_array( $option_value ) ) {
								continue;
							}

							$option_value = wp_parse_args(
								$option_value,
								[
									'text'  => '',
									'value' => '',
								]
							);

							if ( empty( $option_value['value'] ) ) {
								continue;
							}

							$new_options[ $option_value['value'] ] = $option_value['text'];
						}

						if ( ! empty( $new_options ) ) {
							$setting['args']['fields'][ $field_key ]['args']['options'] = $new_options;
						}
					}
				}
			}
		}

		return $settings_sections;  }

	/**
	 * When provided the saved values for a Select or Checkbox field, grab corresponding Labels from its Options to display to the user
	 *
	 * @param array|string $value             Saved value(s).
	 * @param array        $field             Field Settings.
	 * @param boolean      $with_field_label  Whether to show the Field Label first. Defaults to false.
	 *
	 * @access private
	 * @since 4.3.0
	 * @return string                           Values as their Labels, comma separated.
	 */
	private function get_select_or_checkbox_values( $value, $field, $with_field_label = false ): string {
		$field = wp_parse_args(
			$field,
			[
				'label' => '',
				'args'  => [],
			]
		);

		$field['args'] = wp_parse_args(
			$field['args'],
			[
				'options' => [],
			]
		);

		if ( ! is_array( $value ) ) {
			$value = [ $value ];
		}

		$value_labels = [];

		foreach ( $value as $item ) {
			if ( ! isset( $field['args']['options'][ $item ] ) ) {
				continue;
			}

			if ( $with_field_label && ! empty( $field['label'] ) ) {
				$value_labels[] = "{$field['label']}: {$field['args']['options'][ $item ]}";
			} else {
				$value_labels[] = $field['args']['options'][ $item ];
			}
		}

		$value = implode( ', ', $value_labels );

		return $value;  }

	/**
	 * When provided the saved values for a Radio field, grab corresponding Labels from its Options to display to the user
	 *
	 * @param string  $value             Saved value.
	 * @param array   $field             Field Settings.
	 * @param boolean $with_field_label  Whether to show the Field Label first. Defaults to false.
	 *
	 * @access private
	 * @since 4.3.0
	 * @return string                      Value as its Label.
	 */
	private function get_radio_value( $value, $field, $with_field_label = false ): string {
		$field = wp_parse_args(
			$field,
			[
				'label' => '',
				'args'  => [],
			]
		);

		$field['args'] = wp_parse_args(
			$field['args'],
			[
				'options' => [],
			]
		);

		if ( isset( $field['args']['options'][ $value ] ) ) {
			if ( $with_field_label && ! empty( $field['label'] ) ) {
				$value = "{$field['label']}: {$field['args']['options'][ $value ]}";
			} else {
				$value = $field['args']['options'][ $value ];
			}
		}

		return $value;  }
}
