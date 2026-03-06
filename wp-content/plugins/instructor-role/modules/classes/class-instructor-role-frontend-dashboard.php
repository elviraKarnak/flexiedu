<?php
/**
 * Frontend Dashboard Module
 *
 * @since 4.4.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Classes;

use LearnDash\Instructor_Role\Utilities\Translation;
use LearnDash_Custom_Label;
use LDLMS_Post_Types;
use LearnDash\Core\Utilities\Cast;
use WP_Query;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Frontend_Dashboard' ) ) {
	/**
	 * Class Instructor Role Frontend Dashboard Module
	 */
	class Instructor_Role_Frontend_Dashboard {
		/**
		 * Singleton instance of this class
		 *
		 * @since 4.4.0
		 *
		 * @var ?self
		 */
		protected static $instance = null;

		/**
		 * Plugin Slug
		 *
		 * @var string  $plugin_slug
		 *
		 * @since 4.4.0
		 */
		protected $plugin_slug = '';

		/**
		 * Base URL Slug
		 *
		 * @var string  $base_slug
		 *
		 * @since 4.4.0
		 */
		protected $base_slug = '';

		/**
		 * Base URL Quiz Slug
		 *
		 * @var string  $base_slug
		 *
		 * @since 4.4.0
		 */
		protected $base_quiz_slug = '';

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->plugin_slug    = INSTRUCTOR_ROLE_TXT_DOMAIN;
			$this->base_slug      = 'course-builder';
			$this->base_quiz_slug = 'quiz-builder';
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @since 4.4.0
		 *
		 * @return self
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Enqueue styles and scripts necessary for the frontend dashboard.
		 *
		 * @since 4.4.0
		 */
		public function enqueue_scripts() {
			if ( current_user_can( 'manage_options' ) ) {
				$is_admin = true;
			} else {
				$is_admin = false;
			}

			$is_elementor_active   = false;
			$elementor_cpt_support = get_option( 'elementor_cpt_support' );
			$is_elementor_course   = false;
			$is_elementor_lesson   = false;
			$is_elementor_topic    = false;
			$is_elementor_quiz     = false;
			include_once ABSPATH . 'wp-admin/includes/plugin.php';

			if ( is_plugin_active( 'elementor/elementor.php' ) ) {
				$is_elementor_active = true;

				if ( is_array( $elementor_cpt_support ) && in_array( 'sfwd-courses', $elementor_cpt_support ) ) {
					$is_elementor_course = true;
				}
				if ( is_array( $elementor_cpt_support ) && in_array( 'sfwd-lessons', $elementor_cpt_support ) ) {
					$is_elementor_lesson = true;
				}
				if ( is_array( $elementor_cpt_support ) && in_array( 'sfwd-topic', $elementor_cpt_support ) ) {
					$is_elementor_topic = true;
				}
				if ( is_array( $elementor_cpt_support ) && in_array( 'sfwd-quiz', $elementor_cpt_support ) ) {
					$is_elementor_quiz = true;
				}
			}

			$fonts = [
				''           => 'Theme (Default)',
				'Open Sans'  => 'Open Sans',
				'Roboto'     => 'Roboto',
				'Montserrat' => 'Montserrat',
				'Lato'       => 'Lato',
				'Poppins'    => 'Poppins',
				'Inter'      => 'Inter',
			];

			if ( get_query_var( 'course_builder', false ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_dequeue_style( 'jquery-ui' );
				wp_enqueue_media();
				wp_enqueue_editor();

				wp_enqueue_style(
					'ir_course_builder_styles',
					plugins_url( 'css/course-builder/ir-course-builder-styles.css', __DIR__ ),
					[],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/css/course-builder/ir-course-builder-styles.css' )
				);

				// Dequeue elumine scripts.
				wp_dequeue_script( 'elumine-critical-js' );
				wp_dequeue_script( 'elumine-base-js' );
				wp_dequeue_style( 'elumine-critical-css' );
				wp_dequeue_style( 'elumine-base-css' );

				$course_id   = get_query_var( 'ir_course', false );
				$ld_category = [];

				$ld_category['course']     = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' );
				$ld_category['course_tag'] = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'ld_course_tag' );

				$ld_category['lesson']     = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_category' );
				$ld_category['lesson_tag'] = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_tag' );

				$ld_category['topic']     = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Topics_Taxonomies', 'ld_topic_category' );
				$ld_category['topic_tag'] = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Topics_Taxonomies', 'ld_topic_tag' );

				wp_localize_script(
					'instructor-role-course-builder-view-script',
					'ir_fcb_loc',
					[
						'course_label'                   => LearnDash_Custom_Label::get_label( 'course' ),
						'group_label'                    => LearnDash_Custom_Label::get_label( 'group' ),
						'groups_label'                   => LearnDash_Custom_Label::get_label( 'groups' ),
						'lesson_label'                   => LearnDash_Custom_Label::get_label( 'lesson' ),
						'topic_label'                    => LearnDash_Custom_Label::get_label( 'topic' ),
						'quiz_label'                     => LearnDash_Custom_Label::get_label( 'quiz' ),
						'courses_label'                  => LearnDash_Custom_Label::get_label( 'courses' ),
						'lessons_label'                  => LearnDash_Custom_Label::get_label( 'lessons' ),
						'topics_label'                   => LearnDash_Custom_Label::get_label( 'topics' ),
						'quizzes_label'                  => LearnDash_Custom_Label::get_label( 'quizzes' ),
						'lower_course_label'             => LearnDash_Custom_Label::label_to_lower( 'course' ),
						'lower_lesson_label'             => LearnDash_Custom_Label::label_to_lower( 'lesson' ),
						'lower_topic_label'              => LearnDash_Custom_Label::label_to_lower( 'topic' ),
						'lower_quiz_label'               => LearnDash_Custom_Label::label_to_lower( 'quiz' ),
						'lower_group_label'              => LearnDash_Custom_Label::label_to_lower( 'group' ),
						'all_quizzes_url'                => self::get_back_to_dashboard_link( 'quiz' ),
						'course_preview_link'            => get_preview_post_link( $course_id ),
						'nonce'                          => wp_create_nonce( 'ir-create-new-quiz' ),
						'ajax_url'                       => admin_url( 'admin-ajax.php' ),
						'elementor_url'                  => admin_url( 'post.php' ),
						'quiz_builder_url'               => site_url() . '/quiz-builder',
						'is_admin'                       => $is_admin,
						'course_info'                    => get_post( $course_id ),
						'instructor_settings'            => get_option( '_wdmir_admin_settings', false ),
						'ld_cat_tag'                     => $ld_category,
						'is_elementor_active'            => $is_elementor_active,
						'is_elementor_course'            => $is_elementor_course,
						'is_elementor_lesson'            => $is_elementor_lesson,
						'is_elementor_topic'             => $is_elementor_topic,
						'is_elementor_quiz'              => $is_elementor_quiz,
						'back_to_wordpress_editor_nonce' => wp_create_nonce( 'ir-back-to-wordpress-editor' ),
						'ir_frontend_course_creator_color_scheme' => ir_get_settings( 'ir_frontend_course_creator_color_scheme' ),
						'ir_frontend_course_creator_custom_primary' => ir_get_settings( 'ir_frontend_course_creator_custom_primary' ),
						'ir_frontend_course_creator_custom_accent' => ir_get_settings( 'ir_frontend_course_creator_custom_accent' ),
						'ir_frontend_course_creator_custom_background' => ir_get_settings( 'ir_frontend_course_creator_custom_background' ),
						'ir_frontend_course_creator_custom_headings' => ir_get_settings( 'ir_frontend_course_creator_custom_headings' ),
						'ir_frontend_course_creator_custom_text' => ir_get_settings( 'ir_frontend_course_creator_custom_text' ),
						'ir_frontend_course_creator_custom_border' => ir_get_settings( 'ir_frontend_course_creator_custom_border' ),
						'ir_frontend_course_creator_custom_text_light' => ir_get_settings( 'ir_frontend_course_creator_custom_text_light' ),
						'ir_frontend_course_creator_custom_text_ex_light' => ir_get_settings( 'ir_frontend_course_creator_custom_text_ex_light' ),
						'ir_frontend_course_creator_custom_text_primary_btn' => ir_get_settings( 'ir_frontend_course_creator_custom_text_primary_btn' ),
						'fonts'                          => $fonts,
						'ir_frontend_course_creator_font_family' => ir_get_settings( 'ir_frontend_course_creator_font_family' ),
						'ir_frontend_course_creator_font_size' => ir_get_settings( 'ir_frontend_course_creator_font_size' ),
						'dayjs_locale'                   => Translation::get_dayjs_locale(),
					]
				);

				wp_set_script_translations( 'instructor-role-course-builder-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			}

			if ( get_query_var( 'quiz_builder', false ) ) {
				wp_enqueue_media();
				wp_enqueue_editor();
				wp_dequeue_style( 'jquery-ui' );
				wp_dequeue_script( 'elumine-critical-js' );
				wp_dequeue_script( 'elumine-base-js' );
				wp_dequeue_style( 'elumine-critical-css' );
				wp_dequeue_style( 'elumine-base-css' );
				wp_dequeue_style( 'learndash-front' );
				wp_dequeue_style( 'learndash_quiz_front_css' );
				wp_dequeue_style( 'learndash-quiz-front' );
				wp_enqueue_style(
					'ir-course-builder-style',
					plugin_dir_url( plugin_dir_path( __DIR__ ) ) . '/modules/css/course-builder/ir-course-builder-styles.css',
					'1.0.0',
					true
				);
				$ld_category['quiz']     = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Taxonomies', 'ld_quiz_category' );
				$ld_category['quiz_tag'] = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Taxonomies', 'ld_quiz_tag' );
				wp_localize_script(
					'instructor-role-quiz-builder-view-script',
					'ir_fqb_loc',
					[
						'course_label'                   => LearnDash_Custom_Label::get_label( 'course' ),
						'group_label'                    => LearnDash_Custom_Label::get_label( 'group' ),
						'groups_label'                   => LearnDash_Custom_Label::get_label( 'groups' ),
						'lesson_label'                   => LearnDash_Custom_Label::get_label( 'lesson' ),
						'topic_label'                    => LearnDash_Custom_Label::get_label( 'topic' ),
						'quiz_label'                     => LearnDash_Custom_Label::get_label( 'quiz' ),
						'courses_label'                  => LearnDash_Custom_Label::get_label( 'courses' ),
						'lessons_label'                  => LearnDash_Custom_Label::get_label( 'lessons' ),
						'topics_label'                   => LearnDash_Custom_Label::get_label( 'topics' ),
						'quizzes_label'                  => LearnDash_Custom_Label::get_label( 'quizzes' ),
						'question_label'                 => LearnDash_Custom_Label::get_label( 'question' ),
						'questions_label'                => LearnDash_Custom_Label::get_label( 'questions' ),
						'lower_question_label'           => LearnDash_Custom_Label::label_to_lower( 'question' ),
						'lower_course_label'             => LearnDash_Custom_Label::label_to_lower( 'course' ),
						'lower_lesson_label'             => LearnDash_Custom_Label::label_to_lower( 'lesson' ),
						'lower_topic_label'              => LearnDash_Custom_Label::label_to_lower( 'topic' ),
						'lower_quiz_label'               => LearnDash_Custom_Label::label_to_lower( 'quiz' ),
						'all_quizzes_url'                => self::get_back_to_dashboard_link( 'quiz' ),
						'reset_lock_nonce'               => wp_create_nonce( 'learndash-wpproquiz-reset-lock' ),
						'ajax_url'                       => admin_url( 'admin-ajax.php' ),
						'elementor_url'                  => admin_url( 'post.php' ),
						'course_builder_url'             => site_url() . '/course-builder',
						'is_admin'                       => $is_admin,
						'instructor_settings'            => get_option( '_wdmir_admin_settings', false ),
						'template_load_nonce'            => wp_create_nonce( 'ir-fqb-load-quiz-template' ),
						'is_captcha_installed'           => class_exists( 'ReallySimpleCaptcha' ),
						'ld_cat_tag'                     => $ld_category,
						'is_elementor_active'            => $is_elementor_active,
						'is_elementor_course'            => $is_elementor_course,
						'is_elementor_lesson'            => $is_elementor_lesson,
						'is_elementor_topic'             => $is_elementor_topic,
						'is_elementor_quiz'              => $is_elementor_quiz,
						'back_to_wordpress_editor_nonce' => wp_create_nonce( 'ir-back-to-wordpress-editor' ),
						'ir_frontend_course_creator_color_scheme' => ir_get_settings( 'ir_frontend_course_creator_color_scheme' ),
						'ir_frontend_course_creator_custom_primary' => ir_get_settings( 'ir_frontend_course_creator_custom_primary' ),
						'ir_frontend_course_creator_custom_accent' => ir_get_settings( 'ir_frontend_course_creator_custom_accent' ),
						'ir_frontend_course_creator_custom_background' => ir_get_settings( 'ir_frontend_course_creator_custom_background' ),
						'ir_frontend_course_creator_custom_headings' => ir_get_settings( 'ir_frontend_course_creator_custom_headings' ),
						'ir_frontend_course_creator_custom_text' => ir_get_settings( 'ir_frontend_course_creator_custom_text' ),
						'ir_frontend_course_creator_custom_border' => ir_get_settings( 'ir_frontend_course_creator_custom_border' ),
						'ir_frontend_course_creator_custom_text_light' => ir_get_settings( 'ir_frontend_course_creator_custom_text_light' ),
						'ir_frontend_course_creator_custom_text_ex_light' => ir_get_settings( 'ir_frontend_course_creator_custom_text_ex_light' ),
						'ir_frontend_course_creator_custom_text_primary_btn' => ir_get_settings( 'ir_frontend_course_creator_custom_text_primary_btn' ),
						'fonts'                          => $fonts,
						'ir_frontend_course_creator_font_family' => ir_get_settings( 'ir_frontend_course_creator_font_family' ),
						'ir_frontend_course_creator_font_size' => ir_get_settings( 'ir_frontend_course_creator_font_size' ),
						'dayjs_locale'                   => Translation::get_dayjs_locale(),
					]
				);
				wp_set_script_translations( 'instructor-role-quiz-builder-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			}
		}

		/**
		 * Adds create buttons in the admin header.
		 *
		 * @since 5.9.6
		 *
		 * @param array<int,array{class: string, text: string}> $buttons List of buttons to render.
		 * @return array<int,array{class: string, text: string}> Array of button data.
		 */
		public function add_header_buttons( $buttons = [] ) {
			if (
				$this->should_show_course_button()
				&& wdm_is_instructor()
				&& ir_get_settings( 'ir_disable_ld_links' ) === 'on'
			) {
				$buttons[] = [
					'class' => 'ir-fcb-create-new',
					'text'  => __( 'Add New', 'wdm_instructor_role' ),
				];
			} elseif ( $this->should_show_course_button() ) {
				$buttons[] = [
					'class' => 'ir-fcb-create-new',
					'text'  => sprintf(
						/* translators: Course Label */
						__( 'Add New via Frontend %s Creator', 'wdm_instructor_role' ),
						LearnDash_Custom_Label::get_label( 'course' )
					),
				];
			}

			if ( $this->should_show_quiz_button() ) {
				$buttons[] = [
					'class' => 'ir-fqb-create-new-quiz',
					'text'  => sprintf(
						/* translators: Quiz Label */
						__( 'Add New via Frontend %s Creator', 'wdm_instructor_role' ),
						LearnDash_Custom_Label::get_label( 'quiz' )
					),
				];
			}

			return $buttons;
		}

		/**
		 * Enqueue admin styles and scripts for the frontend dashboard.
		 *
		 * @since 4.4.0
		 */
		public function enqueue_admin_scripts() {
			if ( ! current_user_can( 'manage_options' ) && ! wdm_is_instructor() ) {
				return;
			}

			// Check if LD is active.
			if ( ! function_exists( 'learndash_get_post_type_slug' ) ) {
				return;
			}

			$screen = get_current_screen();
			if ( $this->should_show_course_button() ) {
				wp_enqueue_script(
					'ir_course_builder_admin_scripts',
					plugins_url( 'js/course-builder/ir-course-builder-admin.js', __DIR__ ),
					[ 'jquery' ],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/course-builder/ir-course-builder-admin.js' ),
					true
				);

				wp_enqueue_style(
					'ir_course_builder_admin_styles',
					plugins_url( 'css/course-builder/ir-course-builder-admin.css', __DIR__ ),
					[],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/css/course-builder/ir-course-builder-admin.css' )
				);

				wp_localize_script(
					'ir_course_builder_admin_scripts',
					'ir_fcb_loc',
					[
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => wp_create_nonce( 'ir-create-new-course' ),
					]
				);
			}

			wp_enqueue_script(
				'ir_webfont',
				'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js'
			);

			if ( $this->should_show_quiz_button() ) {
				wp_enqueue_script(
					'ir_quiz_builder_admin_scripts',
					plugins_url( 'js/quiz-builder/ir-quiz-builder-admin.js', __DIR__ ),
					[ 'jquery' ],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/quiz-builder/ir-quiz-builder-admin.js' ),
					true
				);

				wp_enqueue_style(
					'ir_quiz_builder_admin_styles',
					plugins_url( 'css/quiz-builder/ir-quiz-builder-admin.css', __DIR__ ),
					[],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/css/quiz-builder/ir-quiz-builder-admin.css' )
				);

				wp_localize_script(
					'ir_quiz_builder_admin_scripts',
					'ir_fqb_loc',
					[
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => wp_create_nonce( 'ir-create-new-quiz' ),
					]
				);
			}
		}

		/**
		 * Register Gutenberg Blocks.
		 *
		 * @since 4.4.0
		 */
		public function register_blocks() {
			// Frontend Course Creator.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build' );
			// Frontend Quiz Creator.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/quiz-builder' );
		}

		/**
		 * Add rewrite rule for the frontend instructor dashboard.
		 *
		 * @since 4.4.0
		 */
		public function add_dashboard_rewrite_rule() {
			add_rewrite_rule(
				'^' . $this->base_slug . '/([\d]+)/?$',
				'index.php?course_builder=true&ir_course=$matches[1]',
				'top'
			);
		}

		/**
		 * Add rewrite tags for the frontend course builder.
		 *
		 * @since 4.4.0
		 */
		public function add_dashboard_rewrite_tag() {
			add_rewrite_tag( '%course_builder%', '([^&]+)' );
		}

		/**
		 * Add rewrite rule for the frontend instructor quiz dashboard.
		 *
		 * @since 4.4.0
		 */
		public function add_quiz_dashboard_rewrite_rule() {
			add_rewrite_rule(
				'^' . $this->base_quiz_slug . '/([\d]+)/?$',
				'index.php?quiz_builder=true&ir_quiz=$matches[1]',
				'top'
			);
		}

		/**
		 * Add rewrite tags for the frontend quiz builder.
		 *
		 * @since 4.4.0
		 */
		public function add_quiz_dashboard_rewrite_tag() {
			add_rewrite_tag( '%quiz_builder%', '([^&]+)' );
		}

		/**
		 * Add instructor frontend dashboard template
		 *
		 * @since 4.4.0
		 *
		 * @param string $template  Template path.
		 * @return string           Updated template path
		 */
		public function add_course_builder_template( $template ) {
			if ( get_query_var( 'course_builder', false ) ) {
				$current_user_id      = get_current_user_id();
				$is_instructor_course = false;
				if ( wdm_is_instructor() ) {
					// Check if instructor course.
					$course_id              = absint( get_query_var( 'ir_course', false ) );
					$instructor_course_list = ir_get_instructor_complete_course_list( $current_user_id, 1 );
					if ( in_array( $course_id, $instructor_course_list ) ) {
						$is_instructor_course = true;
					}
				}
				if ( current_user_can( 'manage_options' ) || $is_instructor_course ) {
					$template = INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-frontend-dashboard.template.php';
				}
			}
			return $template;
		}

		/**
		 * Add instructor frontend quiz dashboard template
		 *
		 * @since 4.4.0
		 *
		 * @param string $template  Template path.
		 * @return string           Updated template path
		 */
		public function add_quiz_builder_template( $template ) {
			if ( get_query_var( 'quiz_builder', false ) ) {
				$template = INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-frontend-quiz-dashboard.template.php';
			}
			return $template;
		}

		/**
		 * Add query variables for the frontend course builder
		 *
		 * @param array $query_vars     Array of WP query variables.
		 * @return array                Updated array of WP query variables.
		 */
		public function add_course_builder_query_vars( $query_vars ) {
			if ( ! in_array( 'ir_course', $query_vars ) ) {
				$query_vars[] = 'ir_course';
			}
			return $query_vars;
		}

		/**
		 * Add query variables for the frontend quiz builder
		 *
		 * @param array $query_vars     Array of WP query variables.
		 * @return array                Updated array of WP query variables.
		 */
		public function add_quiz_builder_query_vars( $query_vars ) {
			if ( ! in_array( 'ir_quiz', $query_vars ) ) {
				$query_vars[] = 'ir_quiz';
			}
			return $query_vars;
		}

		/**
		 * Prevents WordPress from treating course_builder and quiz_builder queries as the home page.
		 * This is a workaround for Elementor Pro's incorrect detection of these pages as Archive pages.
		 *
		 * @since 5.9.12
		 *
		 * @param WP_Query $query The WP_Query instance.
		 *
		 * @return void
		 */
		public function prevent_detection_as_home_page( $query ): void {
			if (
				is_admin()
				|| wp_doing_ajax()
			) {
				return;
			}

			/**
			 * Check if this is a course_builder or quiz_builder query.
			 * Check both query_vars array and get() method to ensure we catch it.
			 */
			$course_builder = isset( $query->query_vars['course_builder'] )
				? $query->query_vars['course_builder'] : $query->get( 'course_builder' );
			$quiz_builder   = isset( $query->query_vars['quiz_builder'] )
				? $query->query_vars['quiz_builder'] : $query->get( 'quiz_builder' );

			if (
				! $course_builder
				&& ! $quiz_builder
			) {
				return;
			}

			/**
			 * Elementor Pro checks is_home directly when determining if a page should use the archive template.
			 * See ElementorPro\Modules\ThemeBuilder\Conditions\Archive::check() .
			 *
			 * Since the Rewrite Rule uses index.php, WordPress may set is_home to true causing Elementor Pro
			 * to incorrectly treat these builder pages as archive pages. Setting is_home to false prevents this.
			 */
			$query->is_home = false;

			// Set the other checked query vars, just in case.
			$query->is_archive = false;
			$query->is_search  = false;
		}

		/**
		 * Add course builder option to the row actions.
		 *
		 * @since 4.4.0
		 *
		 * @param array   $actions  The listing actions.
		 * @param WP_Post $post     The post object.
		 *
		 * @return array            Updated listing actions.
		 */
		public function add_course_builder_option( $actions, $post ) {
			if ( learndash_get_post_type_slug( 'course' ) === $post->post_type ) {
				$disable_ld_links = ir_get_settings( 'ir_disable_ld_links' );
				$action_url       = site_url( '/' . $this->base_slug . '/' . $post->ID );

				// Check if edit action exists.
				if ( ! array_key_exists( 'edit', $actions ) ) {
					return $actions;
				}

				if ( 'on' === $disable_ld_links && wdm_is_instructor() ) {
					$actions['edit'] = '<a class="ir-fcc-action-links" href="' . $action_url . '">' . esc_html__( 'Edit', 'wdm_instructor_role' ) . '</a>';
					unset( $actions['ld-course-builder'] );
				} else {
					$new_actions = [
						'edit'                  => $actions['edit'],
						'ir_course_builder_url' => '<a class="ir-fcc-action-links" href="' . $action_url . '">' . sprintf( /* translators: Course Label */ esc_html__( 'Edit via Frontend %s Creator', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) ) . '</a>',
					];
					foreach ( $actions as $key => $action ) {
						if ( 'edit' === $key ) {
							continue;
						}
						$new_actions[ $key ] = $action;
					}
					$actions = $new_actions;
				}
			}
			return $actions;
		}

		/**
		 * Add quiz builder option to the row actions.
		 *
		 * @since 4.4.0
		 *
		 * @param array   $actions  The listing actions.
		 * @param WP_Post $post     The post object.
		 *
		 * @return array            Updated listing actions.
		 */
		public function add_quiz_builder_option( $actions, $post ) {
			// Check if edit action exists.
			if ( ! array_key_exists( 'edit', $actions ) ) {
				return $actions;
			}

			if ( learndash_get_post_type_slug( 'quiz' ) === $post->post_type ) {
				$action_url                     = site_url( '/' . $this->base_quiz_slug . '/' . $post->ID );
				$actions['ir_quiz_builder_url'] = '<a class="ir-fqb-action-links" href="' . $action_url . '">' . sprintf( /* translators: Quiz Label */esc_html__( 'Edit via Frontend %s Creator', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'quiz' ) ) . '</a>';
			}
			return $actions;
		}

		/**
		 * Add the create new course button from frontend dashboard to the course list table
		 *
		 * @since 4.4.0
		 */
		public function ajax_create_new_course() {
			$response = [
				'status'  => 'error',
				'message' => __( 'Some error occurred. Please refresh and try again', 'wdm_instructor_role' ),
			];

			if ( current_user_can( 'manage_options' ) || wdm_is_instructor( get_current_user_id() ) ) {
				// Set Request Type.
				$request_type = 'post';
				if ( empty( $_POST ) && ! empty( $_GET ) ) {
					$request_type = 'get';
				}

				// Create post object.
				$new_course = [
					'post_title'  => sprintf( /* translators: Course label */__( '%s Title', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) ),
					'post_status' => 'draft',
					'post_author' => get_current_user_id(),
					'post_type'   => learndash_get_post_type_slug( 'course' ),
				];

				// Insert the post into the database.
				$post_id = wp_insert_post( $new_course );

				if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
					$response = [
						'status'  => 'error',
						'message' => $post_id->get_error_message(),
					];
				} else {
					$response = [
						'status'     => 'success',
						'post_id'    => $post_id,
						'course_url' => get_site_url() . '/course-builder/' . $post_id,
					];
				}

				if ( 'get' === $request_type ) {
					if ( 'success' === $response['status'] ) {
						wp_safe_redirect( $response['course_url'] );
						exit();
					}
				} else {
					echo wp_json_encode( $response );
				}
			} elseif ( array_key_exists( 'HTTP_REFERER', $_SERVER ) ) {
					wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
			} else {
				wp_safe_redirect( site_url() );
			}

			wp_die();
		}

		/**
		 * Ajax create new quiz from frontend.
		 *
		 * @since 4.4.0
		 */
		public function ajax_create_new_quiz() {
			$response = [
				'status'  => 'error',
				'message' => __( 'Some error occurred. Please refresh and try again', 'wdm_instructor_role' ),
			];

			// Verify nonce.
			if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'ir-create-new-quiz' ) ) {
				echo wp_json_encode( $response );
				wp_die();
			}

			$post_title = sprintf( /* translators: Quiz Label */ __( '%s Title', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'quiz' ) );
			if ( array_key_exists( 'post_title', $_POST ) ) {
				$post_title = filter_input( INPUT_POST, 'post_title' );
			}

			// Create post object.
			$new_quiz = [
				'post_title'  => Cast::to_string( $post_title ),
				'post_status' => 'draft',
				'post_author' => get_current_user_id(),
				'post_type'   => learndash_get_post_type_slug( 'quiz' ),
			];

			// Insert the post into the database.
			$post_id = wp_insert_post( $new_quiz );

			// Check if course associated.
			if ( array_key_exists( 'course_id', $_POST ) ) {
				$course_id = filter_input( INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT );
				update_post_meta( $post_id, 'course_id', $course_id );
				learndash_update_setting( $post_id, 'course', $course_id );
			}

			// Check if lesson associated.
			if ( array_key_exists( 'lesson_id', $_POST ) ) {
				$lesson_id = filter_input( INPUT_POST, 'lesson_id', FILTER_SANITIZE_NUMBER_INT );
				update_post_meta( $post_id, 'lesson_id', $lesson_id );
				learndash_update_setting( $post_id, 'lesson', $lesson_id );
			}

			// Check if topic associated.
			if ( array_key_exists( 'topic_id', $_POST ) ) {
				$topic_id = filter_input( INPUT_POST, 'topic_id', FILTER_SANITIZE_NUMBER_INT );
				update_post_meta( $post_id, 'topic_id', $topic_id );
				learndash_update_setting( $post_id, 'topic', $topic_id );
			}

			$quiz_model  = new \WpProQuiz_Model_Quiz();
			$quiz_mapper = new \WpProQuiz_Model_QuizMapper();
			$quiz_model->setName( $post_title );
			$quiz_model->setPostId( $post_id );
			$quiz_model = $quiz_mapper->save( $quiz_model );
			$quiz_id    = $quiz_model->getId();
			learndash_update_setting( $post_id, 'quiz_pro', $quiz_id );

			if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
				$response = [
					'status'  => 'error',
					'message' => $post_id->get_error_message(),
				];
			} else {
				$response = [
					'status'   => 'success',
					'post_id'  => $post_id,
					'post'     => get_post( $post_id ),
					'quiz_url' => get_site_url() . '/quiz-builder/' . $post_id,
				];
			}

			echo wp_json_encode( $response );

			wp_die();
		}

		/**
		 * Update course title link for instructors.
		 *
		 * @since 4.4.0
		 *
		 * @param string $link      The edit link.
		 * @param int    $post_id      The ID of the post.
		 * @param string $context   The link context. If set to 'display' then ampersands
		 *                          are encoded.
		 *
		 * @return string           Updated edit link for the course.
		 */
		public function update_course_title_link( $link, $post_id, $context ) {
			if ( ! is_admin() || ! function_exists( 'get_current_screen' ) || ! function_exists( 'learndash_get_post_type_slug' ) ) {
				return $link;
			}

			$screen = get_current_screen();

			// Check if course listing page for instructors.
			if ( ! empty( $screen ) && 'edit-' . learndash_get_post_type_slug( 'course' ) === $screen->id ) {
				$disable_ld_links = ir_get_settings( 'ir_disable_ld_links' );
				if ( 'on' === $disable_ld_links && wdm_is_instructor() ) {
					$link = site_url( '/' . $this->base_slug . '/' . $post_id );
				}
			}
			return $link;
		}

		/**
		 * Hide LD lessons and topic menus if FCC enabled and links disabled.
		 *
		 * @since 4.4.0
		 */
		public function hide_ld_sections_for_fcc() {
			$disable_ld_links = ir_get_settings( 'ir_disable_ld_links' );
			if ( 'on' === $disable_ld_links ) {
				if ( wdm_is_instructor() ) {
					$custom_styles = 'li.submenu-ldlms-lessons, li.submenu-ldlms-topics{ display: none; }';
					wp_add_inline_style( 'ir-instructor-styles', $custom_styles );
				}
			}
		}

		/**
		 * Adds edit button to the quiz and course post edit page.
		 *
		 * @since 4.4.1
		 * @since 5.9.7 The Frontend Editor button is not shown for Quizzes anymore on LearnDash 4.22.1 and above. This is handled elsewhere.
		 *
		 * @return void
		 */
		public function edit_with_fcc_fqb_button() {
			$current_screen = get_current_screen();

			// If LearnDash is using the updated Quiz Edit Page, then we will filter this in a different way.
			if (
				! $current_screen
				|| (
					learndash_get_post_type_slug( LDLMS_Post_Types::QUIZ ) === $current_screen->id
					&& version_compare( constant( 'LEARNDASH_VERSION' ), '4.22.1', '>=' ) // @phpstan-ignore-line -- This constant can change.
				)
			) {
				return;
			}

			if (
				(
					'sfwd-courses' === $current_screen->id
					|| 'sfwd-quiz' === $current_screen->id
				) && isset( $_GET['post'] )
				&& ir_get_settings( 'ir_enable_frontend_dashboard' )
			) {
				global $post_type;
				if ( 'sfwd-courses' === $post_type || 'sfwd-quiz' === $post_type ) {
					// Prepare URL this will also work for multisite.
					$site_url  = home_url( 1 );
					$namespace = 'sfwd-courses' == $post_type ? 'course-builder' : 'quiz-builder';
					$post_id   = get_the_ID();
					$site_url  = $site_url . '/' . $namespace . '/' . $post_id;

					// Prepare text for button.
					$text = 'sfwd-courses' == $post_type ? sprintf(
						/* translators: activate link */
						esc_html__( 'Edit via Frontend %s Creator', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( 'course' )
					) : sprintf(
						/* translators: activate link */
						esc_html__( 'Edit via Frontend %s Creator', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( 'quiz' )
					);
					?>
					<script>
						jQuery(document).ready(function($) {
							function checkPostHeader () {
								if(document.querySelector(".edit-post-header__settings")){
									var button = document.createElement("button");
									button.innerText = "<?php esc_html_e( $text ); ?>";
									button.setAttribute("id", "ir-edit-with-fcc-fqb");
									button.addEventListener('click', function() {
										window.open(' <?php esc_html_e( $site_url ); ?> ', '_blank');
									});
									var editPostHeaderSettings = document.querySelector(".ld-tab-buttons");
									editPostHeaderSettings.appendChild(button);
								} else {
										setTimeout(checkPostHeader, 50);
								}
							}
							$(document).ready(checkPostHeader);
						});
					</script>
					<style>
						#sfwd-header .ld-global-header .ld-tab-buttons button#ir-edit-with-fcc-fqb {
							background: #2067FA;
							padding: 4px 8px;
							font-size: 14px;
							border-radius: 2px;
							color: #fff !important;
							font-weight: 600;
							text-decoration: none;
							border: none;
							cursor: pointer;
						}
					</style>
					<?php
				}
			}
		}

		/**
		 * Remove admin bar for frontend course creation
		 *
		 * @since 4.5.0
		 */
		public function remove_admin_bar() {
			if ( wdm_is_instructor() && function_exists( 'show_admin_bar' ) && get_query_var( 'course_builder', false ) ) {
				show_admin_bar( false );
			}
		}

		/**
		 * Ajax load selected quiz template on frontend quiz builder
		 *
		 * @since 4.5.3
		 */
		public function ajax_load_quiz_template() {
			$response = [
				'status'  => 'error',
				'message' => __( 'Some error occurred. Please refresh and try again', 'wdm_instructor_role' ),
			];

			// Verify nonce.
			if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'ir-fqb-load-quiz-template' ) ) {
				echo wp_json_encode( $response );
				wp_die();
			}

			$quiz_id     = filter_input( INPUT_POST, 'quiz_id', FILTER_SANITIZE_NUMBER_INT );
			$template_id = filter_input( INPUT_POST, 'template_id', FILTER_SANITIZE_NUMBER_INT );
			$name        = filter_input( INPUT_POST, 'name', FILTER_DEFAULT );

			$quiz            = new \WpProQuiz_Model_Quiz();
			$template_mapper = new \WpProQuiz_Model_TemplateMapper();
			$quiz_mapper     = new \WpProQuiz_Model_QuizMapper();
			$form_mapper     = new \WpProQuiz_Model_FormMapper();

			$pro_quiz_id = (int) learndash_get_setting( $quiz_id, 'quiz_pro' );
			$template    = $template_mapper->fetchById( (int) $template_id );

			if ( ( $template ) && is_a( $template, 'WpProQuiz_Model_Template' ) ) {
				$data = $template->getData();

				if ( null !== $data ) {
					$quiz = $data['quiz'];
					$quiz->setId( $pro_quiz_id );
					$quiz->setName( $name );
					$quiz->setText( 'AAZZAAZZ' ); // cspell:disable-line .
					$quiz_mapper->save( $quiz );

					if ( array_key_exists( '_' . learndash_get_post_type_slug( 'quiz' ), $data ) ) {
						$skip_keys = [
							'quiz_pro',
							'course',
							'lesson',
						];
						foreach ( $data[ '_' . learndash_get_post_type_slug( 'quiz' ) ] as $meta_key => $value ) {
							if ( ! empty( $meta_key ) && ! in_array( $meta_key, $skip_keys ) ) {
								learndash_update_setting( $quiz_id, $meta_key, $value );
							}
						}
					}

					$existing_fields = $form_mapper->fetch( $pro_quiz_id );
					// First remove any existing custom fields in the quiz.
					if ( ! empty( $existing_fields ) ) {
						$delete_ids = [];
						foreach ( $existing_fields as $delete_field ) {
							$delete_ids[] = $delete_field->getFormId();
						}

						$form_mapper->deleteForm( $delete_ids, $pro_quiz_id );
					}

					// Check if custom fields in template.
					if ( array_key_exists( 'forms', $data ) && ! empty( $data['forms'] ) ) {
						// Then add custom fields from the template.
						$form_fields = [];
						$sort        = 0;
						foreach ( $data['forms'] as $field ) {
							if ( empty( $field->getFieldname() ) ) {
								continue;
							}

							$form_fields[] = new \WpProQuiz_Model_Form(
								[
									'fieldname' => $field->getFieldname(),
									'formId'    => ( ! empty( $field->getFormId() ) ) ? $field->getFormId() : 0,
									'sort'      => $sort++,
									'quizId'    => $pro_quiz_id,
									'type'      => $field->getType(),
									'required'  => $field->isRequired(),
									'data'      => $field->getData(),
								]
							);
						}
						$form_mapper->update( $form_fields );
					}
				}
			}

			echo wp_json_encode(
				[
					'status'  => 'success',
					'message' => __( 'Template loaded successfully !!', 'wdm_instructor_role' ),
				]
			);

			wp_die();
		}

		/**
		 * Remove protected and private from post titles.
		 *
		 * @param string  $prepend Text displayed before the post title.
		 * @param WP_Post $post   Current post object.
		 *
		 * @since 4.5.3
		 */
		public function remove_private_protected_from_titles( $prepend, $post ) {
			if ( apply_filters( 'ir_filter_remove_private_protected_from_titles', false, $prepend, $post ) ) {
				return $prepend;
			}

			$post_types = [
				learndash_get_post_type_slug( 'course' ),
				learndash_get_post_type_slug( 'lesson' ),
				learndash_get_post_type_slug( 'topic' ),
				learndash_get_post_type_slug( 'quiz' ),
			];

			if ( in_array( $post->post_type, $post_types ) ) {
				return '%s';
			}
		}

		/**
		 * Get back to dashboard link.
		 *
		 * @since 5.0.0
		 *
		 * @param string $type  Type of dashboard. One of 'course' or 'quiz'.
		 */
		public static function get_back_to_dashboard_link( $type ) {
			// Get default backend dashboard links.
			if ( 'course' === $type ) {
				$dashboard_url = add_query_arg(
					[ 'post_type' => learndash_get_post_type_slug( 'course' ) ],
					admin_url( 'edit.php' )
				);
			} else {
				$dashboard_url = add_query_arg(
					[ 'post_type' => learndash_get_post_type_slug( 'quiz' ) ],
					admin_url( 'edit.php' )
				);
			}

			// Check if backend dashboard disabled.
			$is_backend_disabled = ir_get_settings( 'ir_disable_backend_dashboard' );
			if ( $is_backend_disabled ) {
				// Get frontend dashboard link if set.
				$dashboard_page_id = get_option( 'ir_frontend_dashboard_page' );
				if ( false !== $dashboard_page_id ) {
					$dashboard_url = get_permalink( $dashboard_page_id );
				}
			}

			return $dashboard_url;
		}

		/**
		 * Ajax request to handle Back to WordPress Editor button.
		 *
		 * @since 5.3.0
		 */
		public static function ajax_back_to_wordpress_editor() {
			$response = [
				'message' => __( 'Some error occurred. Please refresh the page and try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir-back-to-wordpress-editor', 'nonce', false ) ) {
				wp_send_json_error( $response );
			}

			$post_id      = ir_filter_input( 'post_id', INPUT_GET );
			$post_content = get_the_content( null, false, $post_id );
			delete_post_meta( $post_id, '_elementor_edit_mode', 'builder' );

			wp_send_json_success( $post_content );
		}

		/**
		 * Checks whether the elementor editor is active or not for the course,lesson,topic or quiz
		 *
		 * @since 5.3.0
		 *
		 * @param object $response Response object to be attached.
		 * @param object $post     $post object to fetch the id.
		 * @param object $request  $request object.
		 */
		public static function is_elementor_editor_enabled( $response, $post, $request ) {
			$elementor_edit_mode = get_post_meta( $post->ID, '_elementor_edit_mode', true );

			if ( ! empty( $elementor_edit_mode ) ) {
					$response->data['elementor_edit_mode'] = true;
			} else {
					$response->data['elementor_edit_mode'] = false;
			}

			return $response;
		}

		/**
		 * Function to override title for FCC and FQB.
		 *
		 * @since 5.3.0
		 *
		 * @param string $title Title to be overridden.
		 */
		public static function override_fcc_title( $title ) {
			global $wp;
			$current_slug = $wp->request;

			preg_match( '/\d+/', $current_slug, $matches );
			if ( isset( $matches[0] ) && ! empty( $matches[0] ) ) {
				$post_title = get_the_title( $matches[0] );
			} else {
				$post_title = get_the_title();
			}
			$site_title = get_bloginfo( 'name' );

			if ( strpos( $current_slug, 'course-builder/' ) !== false ) {
				// If "course-builder/" is found in the slug, set a custom title.
				$title = sprintf(
					/* translators: Course Label */
					__( 'Frontend %s Creator', 'wdm_instructor_role' ),
					\LearnDash_Custom_Label::get_label( 'course' )
				);
				return $title . ' "' . $post_title . '" < ' . $site_title;
			} elseif ( strpos( $current_slug, 'quiz-builder/' ) !== false ) {
				// If "quiz-builder/" is found in the slug, set a custom title.
				$title = sprintf(
					/* translators: Quiz Label */
					__( 'Frontend %s Creator', 'wdm_instructor_role' ),
					\LearnDash_Custom_Label::get_label( 'quiz' )
				);
				return $title . ' "' . $post_title . '" < ' . $site_title;
			}
		}

		/**
		 * Determines whether the quiz create button should render or not.
		 *
		 * @since 5.9.6
		 *
		 * @return bool
		 */
		private function should_show_quiz_button(): bool {
			$screen = get_current_screen();
			if (
				current_user_can( 'manage_options' )
				&& function_exists( 'learndash_get_post_type_slug' )
				&& is_object( $screen )
				&& 'edit-' . learndash_get_post_type_slug( LDLMS_Post_Types::QUIZ ) === $screen->id
			) {
				return true;
			}

			return false;
		}

		/**
		 * Determines whether the create buttons should render or not.
		 *
		 * @since 5.9.6
		 *
		 * @return bool
		 */
		private function should_show_course_button(): bool {
			$screen = get_current_screen();
			if (
				is_object( $screen )
				&& current_user_can( 'manage_options' )
				&& function_exists( 'learndash_get_post_type_slug' )
				&& 'edit-' . learndash_get_post_type_slug( LDLMS_Post_Types::COURSE ) === $screen->id
			) {
				return true;
			}

			return false;
		}
	}
}
