<?php
/**
 * Subscriptions Module
 *
 * @since 4.0
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/modules/classes
 */

namespace LdGroupRegistration\Modules\Classes; // @phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound -- Legacy namespace.

use WP_Query;
use WP_Post;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Ld_Group_Registration_Subscriptions' ) ) {
	/**
	 * Class LD Group Registration Subscriptions
	 */
	class Ld_Group_Registration_Subscriptions {
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
		 * Handle variation settings fields for groups
		 *
		 * @param mixed   $loop           Loop.
		 * @param array   $variation_data Variation data.
		 * @param WP_Post $variation      Variation post object.
		 */
		public function handle_variation_settings_fields( $loop, $variation_data, $variation ) {
			$loop           = $loop;
			$variation_data = $variation_data;

			wp_nonce_field( 'learndash_seats_plus_wc_variation_settings', 'learndash_seats_plus_nonce' );

			if (
				// The constant is available in LearnDash LMS - WooCommerce Integration v1.5.0 and above.
				! defined( 'LEARNDASH_WOOCOMMERCE_VERSION' )
				|| version_compare( LEARNDASH_WOOCOMMERCE_VERSION, '1.5.0', '<' ) // @phpstan-ignore-line -- False positive.
			) {
				$courses_options = [ 0 => __( 'No Related Courses', 'wdm_ld_group' ) ];
				$courses         = $this->list_courses();

				if ( ( is_array( $courses ) ) && ( ! empty( $courses ) ) ) {
					$courses_options = $courses_options + $courses;
				}

				$values = get_post_meta( $variation->ID, '_related_course', true );

				woocommerce_wp_select(
					[
						'id'          => '_related_course[' . $variation->ID . '][]',
						'label'       => __( 'Related courses', 'wdm_ld_group' ),
						'multiple'    => true,
						'desc_tip'    => true,
						'description' => __( 'You can select multiple courses to sell together holding the SHIFT key when clicking.', 'wdm_ld_group' ),
						'value'       => get_post_meta( $variation->ID, '_related_course', true ),
						'options'     => $courses_options,
					]
				);

				echo '<script>wdm_ldRelatedCourses = ' . wp_json_encode( $values ) . '</script>';
				echo '<script>variation_id = ' . esc_js( (string) $variation->ID ) . '</script>';
				?>
				<script>
				jQuery(function($){
					$(document.getElementById("_related_course["+ variation_id + "][]"))
						.attr('multiple', true)
						.val(wdm_ldRelatedCourses);
				});
				</script>
				<?php
			}

			$parent_product_id   = $variation->post_parent;
			$parent_product_type = ldgr_get_woo_product_type( $parent_product_id );

			if ( 'variable' === $parent_product_type ) {
				// Add checkbox for the package quantity.
				woocommerce_wp_checkbox(
					[
						'id'          => 'wdm_gr_package_' . $variation->ID,
						'label'       => __( 'Available as Package', 'wdm_ld_group' ),
						'desc_tip'    => true,
						'description' => __( 'Enable this option if you want to provide fix package to your customers for Group Purchase.', 'wdm_ld_group' ),
						'value'       => get_post_meta( $variation->ID, 'wdm_gr_package_' . $variation->ID, true ),
						'style'       => 'float:none;',
					]
				);

				woocommerce_wp_text_input(
					[
						'id'                => 'wdm_gr_package_seat_' . $variation->ID,
						'label'             => __( 'No. of Group Member', 'wdm_ld_group' ),
						'placeholder'       => '0',
						'desc_tip'          => 'true',
						'description'       => __( 'Enter the maximum Group Members allowed for the package.', 'wdm_ld_group' ),
						'type'              => 'number',
						'custom_attributes' => [
							'step' => 'any',
							'min'  => '1',
						],
						'value'             => get_post_meta( $variation->ID, 'wdm_gr_package_seat_' . $variation->ID, true ),
						'style'             => 'width:initial;',
					]
				);

				echo '<script>variation_id = ' . esc_js( (string) $variation->ID ) . '</script>';
				?>
				<script type="text/javascript">
					function update_wdm_gr_package_seat_field() {
						if (jQuery("#wdm_gr_package_"+variation_id).is(":checked")) {
						jQuery(".wdm_gr_package_seat_"+variation_id+"_field").show();
						} else {
							jQuery(".wdm_gr_package_seat_"+variation_id+"_field").hide();
						}
					}
					update_wdm_gr_package_seat_field();
					jQuery("body").on("change","#wdm_gr_package_"+variation_id,function() {
						jQuery(this).parent().next().toggle();
					});
				</script>
				<?php
			}
		}

		/**
		 * Gets the list of the LearnDash courses.
		 *
		 * @since 4.0.0
		 *
		 * @return array<int, string>
		 */
		public function list_courses() {
			$args    = [
				'post_type'      => 'sfwd-courses',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			];
			$query   = new WP_Query( $args );
			$courses = [];

			/**
			 * Our courses are stored in the $query->posts property.
			 *
			 * @var WP_Post[] $posts The list of courses.
			 */
			$posts = $query->get_posts();
			foreach ( $posts as $course_post ) {
				$courses[ $course_post->ID ] = $course_post->post_title;
			}

			return $courses;
		}

		/**
		 * Save variation settings fields for groups
		 *
		 * @param int $post_id  ID of the post.
		 */
		public function save_variation_settings_fields( $post_id ) {
			if (
				! isset( $_POST['learndash_seats_plus_nonce'] )
				|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['learndash_seats_plus_nonce'] ) ), 'learndash_seats_plus_wc_variation_settings' )
			) {
				return;
			}

			if (
				isset( $_POST['_related_course'][ $post_id ] )
				&& (
					// The constant is available in LearnDash LMS - WooCommerce Integration v1.5.0 and above.
					! defined( 'LEARNDASH_WOOCOMMERCE_VERSION' )
					|| version_compare( LEARNDASH_WOOCOMMERCE_VERSION, '1.5.0', '<' ) // @phpstan-ignore-line -- False positive.
				)
			) {
				$related_courses = array_map( 'intval', wp_unslash( $_POST['_related_course'][ $post_id ] ) );
				update_post_meta( $post_id, '_related_course', $related_courses );
			}

			if ( isset( $_POST[ 'wdm_gr_package_' . $post_id ] ) ) {
				update_post_meta( $post_id, 'wdm_gr_package_' . $post_id, sanitize_text_field( wp_unslash( $_POST[ 'wdm_gr_package_' . $post_id ] ) ) );

				if ( isset( $_POST[ 'wdm_gr_package_seat_' . $post_id ] ) && ! empty( $_POST[ 'wdm_gr_package_seat_' . $post_id ] ) ) {
					update_post_meta( $post_id, 'wdm_gr_package_seat_' . $post_id, sanitize_text_field( wp_unslash( $_POST[ 'wdm_gr_package_seat_' . $post_id ] ) ) );
				} else {
					delete_post_meta( $post_id, 'wdm_gr_package_seat_' . $post_id );
				}
			} else {
				delete_post_meta( $post_id, 'wdm_gr_package_seat_' . $post_id );
				delete_post_meta( $post_id, 'wdm_gr_package_' . $post_id );
			}
		}
	}
}
