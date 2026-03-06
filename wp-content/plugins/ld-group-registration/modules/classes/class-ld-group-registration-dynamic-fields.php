<?php
/**
 * Dynamic courses Module
 *
 * @since 4.3.4
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/modules/classes
 */

namespace LdGroupRegistration\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Ld_Group_Registration_Dynamic_Fields' ) ) {
	/**
	 * Class LD Group Registration Dynamic Group
	 */
	class Ld_Group_Registration_Dynamic_Fields {
		/**
		 * Class Instance
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 4.3.3
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Add Fields Setting Tab
		 *
		 * @param array $setting_tabs    tabs already available in settings.
		 *
		 * @return array
		 * @since 4.3.4
		 */
		public function add_fields_setting_tab_header( $setting_tabs ) {
			if ( ! array_key_exists( 'dynamic-fields', $setting_tabs ) ) {
				$setting_tabs['dynamic-fields'] = __( 'Custom Fields', 'wdm_ld_group' );
			}

			return $setting_tabs;
		}

		/**
		 * Display tab contents for dynamic fields settings.
		 *
		 * @param string $current_tab   Tab which is selected currently.
		 * @since 4.3.4
		 */
		public function add_dynamic_fields_setting_tab_contents( $current_tab ) {
			// Check if dynamic fields tab.
			if ( 'dynamic-fields' !== $current_tab ) {
				return;
			}

			// Enqueue styles.
			wp_enqueue_style(
				'wdm-admin_css',
				plugins_url(
					'css/wdm-admin.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);

			$style = '<style>#wpfooter{position:relative;margin-left:0px;}</style>';
			wp_add_inline_style( 'wdm-admin_css', $style );

			// Enqueue Scripts.
			wp_enqueue_script(
				'wdm-ldgr-addel-js',
				plugins_url(
					'js/addel.jquery.min.js',
					__DIR__
				),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION,
				true
			);

			// Fetch data.
			$ldgr_dynamic_fields_setting = get_option( 'ldgr_dynamic_fields', [] );

			return ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/modules/templates/dynamic-fields/ldgr-dynamic-fields-settings.template.php',
				[
					'ldgr_dynamic_fields_setting' => $ldgr_dynamic_fields_setting,
				]
			);
		}

		/**
		 * Save group purchase settings on product publish
		 *
		 * @param int $post_id  ID of the post.
		 * @since 4.3.4
		 */
		public function save_dynamic_fields_options( $post_id ) {
			$post_data = wp_unslash( $_POST );
			if ( isset( $post_data['ldgr_nonce'] ) && wp_verify_nonce( $post_data['ldgr_nonce'], 'ldgr_save_dynamic_fields_settings' ) ) {
				if ( ! isset( $post_data['ldgr_dynamic_field']['key'] ) || ! isset( $post_data['ldgr_dynamic_field'] ) ) {
					delete_option( 'ldgr_dynamic_fields' );
				} else {
					$dynamic_fields_data = (array) $post_data['ldgr_dynamic_field'];
					if ( 1 === count( $dynamic_fields_data['key'] ) && '' === $dynamic_fields_data['key'][0] ) {
						delete_option( 'ldgr_dynamic_fields' );
					} else {
						$all_key         = array_key_exists( 'key', $dynamic_fields_data ) ? array_filter( $dynamic_fields_data['key'] ) : '';
						$all_name        = array_key_exists( 'name', $dynamic_fields_data ) ? array_filter( $dynamic_fields_data['name'] ) : '';
						$all_types       = array_key_exists( 'field_type', $dynamic_fields_data ) ? array_filter( $dynamic_fields_data['field_type'] ) : '';
						$all_required    = array_key_exists( 'required', $dynamic_fields_data ) ? array_filter( $dynamic_fields_data['required'] ) : '';
						$all_override    = array_key_exists( 'override', $dynamic_fields_data ) ? array_filter( $dynamic_fields_data['override'] ) : '';
						$all_options     = array_key_exists( 'options', $dynamic_fields_data ) ? array_filter( json_decode( $dynamic_fields_data['options'], true ) ) : '';
						$final_array     = [];
						$key_check_array = [];
						foreach ( $all_key as $index => $single_key ) {
							if ( ! in_array( $single_key, $key_check_array, true ) ) {
								array_push(
									$final_array,
									[
										'name'       => $all_name[ $index ],
										'field_type' => $all_types[ $index ],
										'required'   => empty( $all_required[ $index ] ) ? '' : $all_required[ $index ],
										'override'   => empty( $all_override[ $index ] ) ? '' : $all_override[ $index ],
										'options'    => $all_options[ $index ],
										'key'        => $single_key,
									]
								);
								array_push( $key_check_array, $single_key );
							}
						}

						update_option( 'ldgr_dynamic_fields', $final_array );
					}
				}
			}
		}

		/**
		 * This function is used to display dynamic fields on user profile
		 *
		 * @param object $user    WP User object.
		 * @return void
		 * @since 4.3.4
		 */
		public function display_dynamic_user_fields( $user ) {
			$ldgr_dynamic_fields_setting = get_option( 'ldgr_dynamic_fields', [] );

			?>
			<table class="form-table">
			<?php
			foreach ( $ldgr_dynamic_fields_setting as $key => $value ) {
				?>
				<tr>
					<th>
						<label for="<?php echo esc_attr( $value['key'] ); ?>"><?php echo esc_attr( $value['name'] ); ?></label>
					</th>
					<td>
						<?php
						switch ( $value['field_type'] ) {
							case 'text':
								?>
								<input type="text" name="<?php echo esc_attr( $value['key'] ); ?>" id="<?php echo esc_attr( $value['key'] ); ?>" value="<?php echo esc_attr( get_the_author_meta( $value['key'], $user->ID ) ); ?>" class="regular-text" />
								<?php
								break;

							case 'select':
								?>
								<select name="<?php echo esc_attr( $value['key'] ); ?>">
									<option value=""><?php esc_html_e( 'Select Option', 'wdm_ld_group' ); ?></option>
									<?php
									foreach ( $value['options']['key'] as $option_key => $option_value ) {
										?>
										<option value="<?php echo esc_attr( $value['options']['value'][ $option_key ] ); ?>" <?php echo ( get_the_author_meta( $value['key'], $user->ID ) === $value['options']['value'][ $option_key ] ) ? 'selected' : ''; ?> ><?php echo esc_attr( $option_value ); ?></option>
										<?php
									}
									?>
								</select>
								<?php
								break;

							case 'number':
								?>
								<input type="number" name="<?php echo esc_attr( $value['key'] ); ?>" id="<?php echo esc_attr( $value['key'] ); ?>" value="<?php echo esc_attr( get_the_author_meta( $value['key'], $user->ID ) ); ?>" class="regular-text" />
								<?php
								break;

							case 'radio':
								foreach ( $value['options']['key'] as $option_key => $option_value ) {
									?>
									<input type="radio" name="<?php echo esc_attr( $value['key'] ); ?>"  value="<?php echo esc_attr( $value['options']['value'][ $option_key ] ); ?>" <?php echo ( get_the_author_meta( $value['key'], $user->ID ) === $value['options']['value'][ $option_key ] ) ? 'checked' : ''; ?>>
									<label for="<?php echo esc_attr( $value['key'] ); ?>"><?php echo esc_attr( $option_value ); ?></label><br>
									<?php
								}
								break;

							case 'checkbox':
								?>
								<input type="checkbox" name="<?php echo esc_attr( $value['key'] ); ?>" <?php echo ( get_the_author_meta( $value['key'], $user->ID ) === 'on' ) ? 'checked' : ''; ?>/>
								<label><?php echo esc_attr( $value['name'] ); ?></label>
								<?php
								break;

							case 'textarea':
								?>
								<textarea name="<?php echo esc_attr( $value['key'] ); ?>" id="<?php echo esc_attr( $value['key'] ); ?>"><?php echo esc_attr( get_the_author_meta( $value['key'], $user->ID ) ); ?></textarea>
								<?php
								break;
						}
						?>
					</td>
				</tr>
				<?php
			}
			?>
			</table>
			<?php
		}

		/**
		 * This function is used to update dynamic user fields
		 *
		 * @param int $user_id   user id.
		 * @return void
		 * @since 4.3.4
		 */
		public function update_dynamic_user_fields( $user_id ) {
			$ldgr_dynamic_fields_setting = get_option( 'ldgr_dynamic_fields', [] );
			$post_data                   = wp_unslash( $_POST );

			foreach ( $ldgr_dynamic_fields_setting as $key => $value ) {
				if ( current_user_can( 'edit_user', $user_id ) && isset( $post_data[ $value['key'] ] ) ) {
					update_user_meta( $user_id, $value['key'], $post_data[ $value['key'] ] );
				}
			}
		}

		/**
		 * This function is used to create dynamic fields using values set in settings.
		 *
		 * @param array $data   Array of settings set.
		 * @return string    Dynamic fields HTML.
		 * @since 4.3.4
		 */
		public function create_dynamic_field( $data, $parent_class = '', $field_class = '' ) {
			$html = '';
			$html = '<div class="ldgr-field ' . $parent_class . '">';

			$required = 'yes' === $data['required'] ? 'required' : '';

			switch ( $data['field_type'] ) {
				case 'text':
					$html .= '<label>' . $data['name'] . '</label>';
					$html .= '<input type="text" class="ldgr-dynamic-textbox ' . $field_class . '" name="wdm_dynamic[' . $data['key'] . '][]" ' . $required . ' />';
					break;

				case 'select':
					$html .= '<label>' . $data['name'] . '</label>';
					$html .= '<select class="ldgr-select ldgr-dynamic-select ' . $field_class . '" name="wdm_dynamic[' . $data['key'] . '][]" ' . $required . ' >';
					$html .= '<option value="">Select Option</option>';
					foreach ( $data['options']['key'] as $key => $value ) {
						$html .= '<option value="' . $data['options']['value'][ $key ] . '">' . $value . '</option>';
					}
					$html .= '</select>';
					break;

				case 'number':
					$html .= '<label>' . $data['name'] . '</label>';
					$html .= '<input type="number" class="ldgr-number ldgr-dynamic-number ' . $field_class . '" name="wdm_dynamic[' . $data['key'] . '][]" ' . $required . '  />';
					break;

				case 'radio':
					$html .= '<label>' . $data['name'] . '</label>';
					foreach ( $data['options']['key'] as $key => $value ) {
						$html .= '<input class="ldgr-radio ldgr-dynamic-radio ' . $field_class . '" type="radio" name="wdm_dynamic[' . $data['key'] . '][]"  value="' . $data['options']['value'][ $key ] . '" id="' . $data['options']['value'][ $key ] . '"' . $required . ' >';
						$html .= '<label for="' . $data['options']['value'][ $key ] . '">' . $value . '</label><br>';
					}
					break;

				case 'checkbox':
					$html .= '<label>' . $data['name'] . '</label>';
					$html .= '<input type="checkbox" class="ldgr-checkbox ldgr-dynamic-checkbox ' . $required . ' ' . $field_class . '" name="wdm_dynamic[' . $data['key'] . '][]" ' . $required . '  />';
					break;

				case 'textarea':
					$html .= '<label>' . $data['name'] . '</label>';
					$html .= '<textarea class="ldgr-textarea ldgr-dynamic-textarea ' . $field_class . '" name="wdm_dynamic[' . $data['key'] . '][]" ' . $required . ' ></textarea>';
					break;
			}
			$html .= '<span class="ldgr-field-error"></span>';
			$html .= '</div>';

			/**
			 * Filter to alter html during creation of dynamic html fields.
			 *
			 * @since 4.3.4
			 */
			return apply_filters( 'ldgr_filter_dynamic_fields_html', $html );       }

		/**
		 * This function is used to display fields on group code page
		 *
		 * @return void
		 * @since 4.3.4
		 */
		public function add_dynamic_fields_group_code() {
			$dynamic_fields = get_option( 'ldgr_dynamic_fields', [] );

			if ( ! empty( $dynamic_fields ) && is_array( $dynamic_fields ) ) {
				$dynamic_field_class = new \LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Dynamic_Fields();
				foreach ( $dynamic_fields as $key => $value ) {
					$html = $dynamic_field_class->create_dynamic_field( $value, 'ldgr-d-inline-flex ldgr-mr-40', 'ldgr-w-300' );
					echo $html;
				}
			}
		}

		/**
		 * This function is used to save dynamic field form data in user meta
		 *
		 * @param int    $member_user_id   User id of registered user.
		 * @param string $group_code    Group code used to register.
		 * @param array  $form_data      Form post data.
		 * @return void
		 * @since 4.3.4
		 */
		public function save_dynamic_field_form_data( $member_user_id, $group_code, $form_data ) {
			$dynamic_fields = $form_data['wdm_dynamic'];
			$this->add_user_meta( $member_user_id, $dynamic_fields, 0 );
		}

		/**
		 * This function is used to add user meta for dynamic fields.
		 *
		 * @param int    $member_user_id   User id of registered user.
		 * @param string $fields_data    Form post data.
		 * @param array  $index      index for data.
		 * @return void
		 * @since 4.3.4
		 */
		public function add_user_meta( $member_user_id, $fields_data, $index ) {
			$ldgr_dynamic_fields_setting = get_option( 'ldgr_dynamic_fields', [] );

			foreach ( $ldgr_dynamic_fields_setting as $key => $value ) {
				if ( 'yes' === $value['override'] && ! empty( get_user_meta( $member_user_id, $value['key'], true ) ) ) {
					continue;
				} else {
					if ( ! isset( $fields_data[ $value['key'] ][ $index ] ) ) {
						$fields_data[ $value['key'] ][ $index ] = '';
					}
					update_user_meta( $member_user_id, $value['key'], $fields_data[ $value['key'] ][ $index ] );
				}
			}       }

		/**
		 * This function is used to add dynamic fields to the array structure which is used to map with CSV.
		 *
		 * @param array  $csv_data_list   User id of registered user.
		 * @param string $data    Form post data.
		 * @return array
		 * @since 4.3.4
		 */
		function alter_csv_data( $csv_data_list, $data, $group_id ) {
			$dynamic_fields = get_option( 'ldgr_dynamic_fields', [] );
			// The dynamic_fields_count defaults to three because of the default three required WordPress fields ( First name , Last name and Email ) .
			$dynamic_fields_count = 3;
			foreach ( $dynamic_fields as $key => $value ) {
				if ( $value['required'] != '' && $value['required'] != 'no' && ! empty( $value['required'] ) ) {
					$csv_data_list[ $value['key'] ][] = trim( $data[ $dynamic_fields_count ] );
				} elseif ( $value['required'] == 'yes' && empty( $value['required'] ) ) {
					$csv_data_list[ $value['key'] ][] = '';
				} else {
					$csv_data_list[ $value['key'] ][] = trim( $data[ $dynamic_fields_count ] );
				}
				++$dynamic_fields_count;
			}
			return $csv_data_list;
		}

		/**
		 * This function is used to add the dynamic fields CSV data to the final mapped CSV array.
		 *
		 * @param array $final_csv_data   User id of registered user.
		 * @param array $csv_data_list    Form post data.
		 * @return array
		 * @since 4.3.4
		 */
		function alter_upload_data( $final_csv_data, $group_id, $csv_data_list ) {
			$dynamic_fields = get_option( 'ldgr_dynamic_fields', [] );

			foreach ( $dynamic_fields as $key => $value ) {
				if ( isset( $csv_data_list[ $value['key'] ] ) ) {
					$final_csv_data[ $value['key'] ] = $csv_data_list[ $value['key'] ];
				} else {
					$final_csv_data[ $value['key'] ] = '';
				}
			}

			/**
			 * Filter to alter dynamic fields final CSV data.
			 *
			 * @since 4.3.4
			 */
			return apply_filters( 'ldgr_filter_dynamic_final_csv_data', $final_csv_data );
		}

		/**
		 * This function is used to add user meta for dynamic fields.
		 *
		 * @param array $data   User id of registered user.
		 * @return array
		 * @since 4.3.4
		 */
		function alter_csv_data_list( $data ) {
			$dynamic_fields = get_option( 'ldgr_dynamic_fields', [] );

			if ( ! empty( $dynamic_fields ) && is_array( $dynamic_fields ) ) {
				foreach ( $dynamic_fields as $key => $value ) {
					$dynamic_fields_meta[ $value['key'] ] = [];
				}
				$data = array_merge( $data, $dynamic_fields_meta );
			}

			/**
			 * Filter to alter user meta for dynamic fields.
			 *
			 * @since 4.3.4
			 */
			return apply_filters( 'ldgr_filter_dynamic_user_meta', $data );
		}

		/**
		 * This function is used to alter dynamic field column count based on required not required
		 *
		 * @param int    $column_count   Allowed column count.
		 * @param string $file_name    File name.
		 * @param string $group_id    group id.
		 * @return int
		 * @since 4.3.4
		 */
		function alter_required_columns( $column_count, $file_name, $group_id ) {
			$dynamic_fields = get_option( 'ldgr_dynamic_fields', [] );
			$column_count   = 3;
			foreach ( $dynamic_fields as $key => $value ) {
				if ( 'yes' === $value['required'] ) {
					++$column_count;
				}
			}
			return $column_count;
		}

		/**
		 * This function is used to validate dynamic csv data based on required and not required fields.
		 *
		 * @param bool  $is_valid   validate cav dynamic data boolean.
		 * @param array $csv_data   Actual csv data to validate.
		 * @param int   $allowed_columns   Allowed fields.
		 * @param int   $required_columns   Required fields.
		 * @return bool
		 * @since 4.3.4
		 */
		function validate_dynamic_csv_fields( $is_valid, $csv_data, $allowed_columns, $required_columns ) {
			$is_valid = true;

			// check if csv data count
			if ( count( $csv_data ) > $allowed_columns ) {
				return false;
			}

			// WordPress required data check
			$column = 0;
			while ( $column < 3 ) {
				if ( ! isset( $csv_data[ $column ] ) || empty( $csv_data[ $column ] ) ) {
					$is_valid = false;
					break;
				}
				++$column;
			}
			// Dynamic data validation

			$dynamic_fields = get_option( 'ldgr_dynamic_fields', [] );
			foreach ( $dynamic_fields as $key => $value ) {
				if ( 'yes' === $value['required'] ) {
					if ( ! isset( $csv_data[ $column ] ) || empty( $csv_data[ $column ] ) ) {
						$is_valid = false;
						break;
					}
				}
				// check if value is of numeric and validate it against csv_data value
				if ( $value['field_type'] == 'number' && is_numeric( $csv_data[ $column ] ) != 1 ) {
					if ( 'yes' == $value['required'] && empty( $csv_data[ $column ] ) ) {
						$is_valid = false;
					} elseif ( empty( $csv_data[ $column ] ) ) {
						$is_valid = true;
					} else {
						$is_valid = false;
					}
					break;
				}

				// check if value is of checkbox and validate it against csv_data value
				if ( $value['field_type'] == 'checkbox' && ! in_array( strtolower( $csv_data[ $column ] ), [ 'on', '' ] ) ) {
					if ( 'yes' == $value['required'] && empty( $csv_data[ $column ] ) ) {
						$is_valid = false;
					} elseif ( empty( $csv_data[ $column ] ) ) {
						$is_valid = true;
					} else {
						$is_valid = false;
					}
					break;
				}
				++$column;
			}

			return $is_valid;
		}
	}
}
