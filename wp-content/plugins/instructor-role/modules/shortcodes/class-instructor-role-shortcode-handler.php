<?php
/**
 * Shortcode Handler Module
 *
 * @since 3.5.5
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Shortcodes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Shortcode_Handler' ) ) {
	/**
	 * Class Instructor Role Shortcode Handler
	 */
	class Instructor_Role_Shortcode_Handler {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 3.3.0
		 */
		protected static $instance = null;

		/**
		 * Plugin Slug
		 *
		 * @var string  $plugin_slug
		 *
		 * @since 3.3.0
		 */
		protected $plugin_slug = '';

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->plugin_slug = INSTRUCTOR_ROLE_TXT_DOMAIN;
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 3.5.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function initialize_shortcodes() {
			$shortcodes = [
				'all_instructors' => __CLASS__ . '::all_instructors',
			];

			foreach ( $shortcodes as $shortcode => $function ) {
				add_shortcode( apply_filters( "ir_filter_{$shortcode}_shortcode_tag", $shortcode ), $function );
			}
		}

		/**
		 * Display list of all instructors
		 *
		 * @since 3.5.5
		 *
		 * @param array $atts   Attributes.
		 * @return string
		 */
		public static function all_instructors( $atts ) {
			global $post;

			$atts = array_merge(
				[
					'display_img'    => true,
					'display_name'   => true,
					'visible'        => 2,
					'size'           => 32,
					'exclude_author' => false,
				],
				(array) $atts
			);

			// Get current course data.
			$course = $post;

			// Check whether to get some other course data.
			if ( array_key_exists( 'course_id', $atts ) && ! empty( $atts['course_id'] ) ) {
				$course_id = intval( $atts['course_id'] );
				$course    = get_post( $course_id );
			}

			// Get list of co-instructors.
			$co_instructor_list = get_post_meta( $course->ID, 'ir_shared_instructor_ids', 1 );

			$all_instructor_ids = array_filter( explode( ',', $co_instructor_list ) );

			// Include course author.
			if ( ! $atts['exclude_author'] ) {
				array_unshift( $all_instructor_ids, $course->post_author );
			}

			// Remove any duplicates.
			$all_instructor_ids = array_filter( array_unique( $all_instructor_ids ) );

			// Handle visible instructors.
			$hidden_instructors = [];
			if ( $atts['visible'] ) {
				$hidden_instructors = array_splice( $all_instructor_ids, $atts['visible'] );
			}

			return ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/shortcodes/ir-all-instructors-shortcode.template.php',
				[
					'atts'                => $atts,
					'course'              => $course,
					'visible_instructors' => $all_instructor_ids,
					'hidden_instructors'  => $hidden_instructors,
				],
				true
			);
		}

		/**
		 * Enqueue shortcode styles
		 *
		 * @since 3.5.5
		 */
		public function enqueue_scripts() {
			wp_enqueue_style(
				'ir-shortcode-styles',
				plugins_url( 'css/ir-shortcode-styles.css', __DIR__ )
			);
		}
	}
}
