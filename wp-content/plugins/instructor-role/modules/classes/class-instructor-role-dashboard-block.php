<?php
/**
 * Instructor Dashboard Block
 *
 * @since 5.0.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Modules\Classes\Instructor_Role_Dashboard;
use LearnDash\Core\Utilities\Cast;
use LearnDash\Instructor_Role\Utilities\Translation;
use LearnDash_Custom_Label;
use LearnDash\Instructor_Role\StellarWP\Arrays\Arr;
use WP_Post;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Dashboard_Block' ) ) {
	/**
	 * Class Instructor Role Dashboard Block Module
	 */
	class Instructor_Role_Dashboard_Block {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 5.0.0
		 */
		protected static $instance = null;

		/**
		 * Plugin Slug
		 *
		 * @var string  $plugin_slug
		 *
		 * @since 5.0.0
		 */
		protected $plugin_slug = '';

		/**
		 * Custom Page Templates
		 *
		 * @var array
		 *
		 * @since 5.0.0
		 */
		protected $templates;

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->plugin_slug = INSTRUCTOR_ROLE_TXT_DOMAIN;
			$this->templates   = [
				'ir-wisdm-dashboard.template.php' => __( 'Instructor Dashboard', 'wdm_instructor_role' ),
			];
		}

		/**
		 * Force enqueue block assets for instructor-role blocks.
		 *
		 * @since 5.9.9
		 *
		 * @param bool   $enqueue    Whether to enqueue the block assets.
		 * @param string $block_name The block name.
		 *
		 * @return bool Whether to enqueue the block assets.
		 */
		public function force_enqueue_block_assets( bool $enqueue, string $block_name ): bool {
			if ( strpos( $block_name, 'instructor-role/' ) === 0 ) {
				return true;
			}

			return $enqueue;
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 5.0.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Add our new page template to the page templates dropdown list.
		 *
		 * @since 5.0.0
		 *
		 * @param array $page_templates    Array of page templates. Keys are filenames, values are translated names.
		 */
		public function add_frontend_dashboard_template( $page_templates ) {
			$page_templates = array_merge( $page_templates, $this->templates );
			return $page_templates;
		}

		/**
		 * Register the frontend dashboard template
		 *
		 * @since 5.0.0
		 *
		 * @param array $atts       Array of Attributes.
		 * @return array
		 */
		public function register_frontend_dashboard_templates( $atts ) {
			// Create the key used for the themes cache.
			$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

			// Retrieve the cache list.
			// If it doesn't exist, or it's empty prepare an array.
			$templates = wp_get_theme()->get_page_templates();
			if ( empty( $templates ) ) {
				$templates = [];
			}

			// New cache, therefore remove the old one.
			wp_cache_delete( $cache_key, 'themes' );

			// Now add our template to the list of templates by merging our templates.
			// with the existing templates array from the cache.
			$templates = array_merge( $templates, $this->templates );

			// Add the modified cache to allow WordPress to pick it up for listing.
			// available templates.
			wp_cache_add( $cache_key, $templates, 'themes', 1800 );

			return $atts;
		}

		/**
		 * Checks if the template is assigned to the page
		 *
		 * @since 5.0.0
		 *
		 * @param string $template      Current page template file path.
		 */
		public function view_frontend_dashboard_template( $template ) {
			// Return the search template if we're searching (instead of the template for the first result).
			if ( is_search() ) {
				return $template;
			}

			// Get global post.
			global $post;

			// Return template if post is empty.
			if ( ! $post ) {
				return $template;
			}

			// Return default template if we don't have a custom one defined.
			if ( ! isset( $this->templates[ get_post_meta( $post->ID, '_wp_page_template', true ) ] ) ) {
				return $template;
			}

			$filepath = INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/';

			$file = $filepath . get_post_meta( $post->ID, '_wp_page_template', true );

			// Just to be safe, we check if the file exist first.
			if ( file_exists( $file ) ) {
				return $file;
			} else {
				error_log( 'Invalid File Path : ' . print_r( $file, 1 ) );
			}

			// Return template.
			return $template;
		}

		/**
		 * Register Gutenberg Blocks.
		 *
		 * @since 5.0.0
		 */
		public function register_blocks() {
			// Tabs.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/wisdm-tabs' );
			// Frontend Overview Page.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/dashboard-overview' );
			// Settings Page.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/settings' );
			// All Courses Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/all-courses' );
			// All Quizzes Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/all-quizzes' );
			// Commissions Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/commissions' );
			// Products Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/products' );
			// Assignments Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/assignments' );
			// Submitted Essays Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/submitted-essays' );
			// Quiz Attempts Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/quiz-attempts' );
			// Comments Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/comments' );
			// Course Reports Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/course-reports' );
			// Groups Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/groups' );
			// Certificates Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/certificates' );
			// Manage Instructor Block.
			register_block_type( INSTRUCTOR_ROLE_ABSPATH . '/blocks/build/manage-instructor' );
		}

		/**
		 * Add dashboard display state on page list table.
		 *
		 * @since 5.0.0
		 *
		 * @param string[] $post_states An array of post display states.
		 * @param WP_Post  $post        The current post object.
		 * @return string[]             Updated array of post display states.
		 */
		public function add_dashboard_display_state( $post_states, $post ) {
			$is_dashboard_page = get_post_meta( $post->ID, '_wp_page_template', true );
			$template_path     = key( $this->templates );
			if ( $template_path === $is_dashboard_page ) {
				$post_states['ir_dashboard_page'] = _x( 'Instructor Dashboard Page', 'page label' );
			}

			return $post_states;
		}

		/**
		 * Enqueue block scripts and styles on frontend.
		 *
		 * @since 5.0.0
		 */
		public function enqueue_scripts() {
			if ( current_user_can( 'manage_options' ) ) {
				$is_admin = true;
			} else {
				$is_admin = false;
			}

			$is_learndash_certificate_builder_active = false;

			if ( class_exists( 'LearnDash_Certificate_Builder\Bootstrap' ) ) {
				$is_learndash_certificate_builder_active = true;
			}

			/**
			 * Get overview block attributes and localize them for the frontend dashboard javascript.
			 *
			 * The overview block components visibility can be controlled by the block attributes from the Gutenberg editor.
			 * We need this data to set the block visibility attributes for the frontend dashboard.
			 */

			$overview_block_attributes    = [];
			$instructor_dashboard_post_id = Cast::to_int( get_option( 'ir_frontend_dashboard_page' ) );

			if ( ! empty( $instructor_dashboard_post_id ) ) {
				$instructor_dashboard_post = get_post( $instructor_dashboard_post_id );

				if ( $instructor_dashboard_post instanceof WP_Post ) {
					$blocks = parse_blocks( $instructor_dashboard_post->post_content );

					// The overview block is a fixed block with fixed inner blocks structure, so we can expect the nested structure here.
					$overview_block_attributes = Arr::get( $blocks, '0.innerBlocks.0.innerBlocks.0.attrs', [] );
				}
			}

			$user_id           = get_current_user_id();
			$nonce             = wp_create_nonce( 'ir-update-pass-' . $user_id );
			$localization_data = [
				'ajax_url'                                => admin_url( 'admin-ajax.php' ),
				'logout_sessions_nonce'                   => wp_create_nonce( 'update-user_' . $user_id ),
				'user_id'                                 => $user_id,
				'update_pass_nonce'                       => $nonce,
				'create_new_course_nonce'                 => wp_create_nonce( 'ir-create-new-course' ),
				'create_new_quiz_nonce'                   => wp_create_nonce( 'ir-create-new-quiz' ),
				'export_order_details_nonce'              => wp_create_nonce( 'ir-export-order-details' ),
				'export_manual_commission_log_nonce'      => wp_create_nonce( 'ir-export-manual-commission-log' ),
				'update_commission_log_nonce'             => wp_create_nonce( 'ir_update_commission_log' ),
				'delete_manual_commission_log_nonce'      => wp_create_nonce( 'ir_commission_log_actions' ),
				'ir_commission_paypal_payout_nonce'       => wp_create_nonce( 'ir_commission_paypal_payout_payment' ),
				'replyto-comment'                         => wp_create_nonce( 'replyto-comment' ),
				'unfiltered-html-comment'                 => wp_create_nonce( 'unfiltered-html-comment' ),
				'course_label'                            => \LearnDash_Custom_Label::get_label( 'course' ),
				'group_label'                             => \LearnDash_Custom_Label::get_label( 'group' ),
				'groups_label'                            => \LearnDash_Custom_Label::get_label( 'groups' ),
				'lesson_label'                            => \LearnDash_Custom_Label::get_label( 'lesson' ),
				'topic_label'                             => \LearnDash_Custom_Label::get_label( 'topic' ),
				'quiz_label'                              => \LearnDash_Custom_Label::get_label( 'quiz' ),
				'question_label'                          => \LearnDash_Custom_Label::get_label( 'question' ),
				'courses_label'                           => \LearnDash_Custom_Label::get_label( 'courses' ),
				'lessons_label'                           => \LearnDash_Custom_Label::get_label( 'lessons' ),
				'topics_label'                            => \LearnDash_Custom_Label::get_label( 'topics' ),
				'quizzes_label'                           => \LearnDash_Custom_Label::get_label( 'quizzes' ),
				'questions_label'                         => \LearnDash_Custom_Label::get_label( 'questions' ),
				'lower_course_label'                      => \LearnDash_Custom_Label::label_to_lower( 'course' ),
				'lower_courses_label'                     => \LearnDash_Custom_Label::label_to_lower( 'courses' ),
				'lower_lesson_label'                      => \LearnDash_Custom_Label::label_to_lower( 'lesson' ),
				'lower_topic_label'                       => \LearnDash_Custom_Label::label_to_lower( 'topic' ),
				'lower_quiz_label'                        => \LearnDash_Custom_Label::label_to_lower( 'quiz' ),
				'lower_quizzes_label'                     => \LearnDash_Custom_Label::label_to_lower( 'quizzes' ),
				'lower_group_label'                       => \LearnDash_Custom_Label::label_to_lower( 'group' ),
				'lower_questions_label'                   => \LearnDash_Custom_Label::label_to_lower( 'questions' ),
				'dashboard_colors'                        => $this->get_dashboard_colors(),
				'active_tab'                              => $this->get_active_dashboard_tab(),
				'create_new_course_url'                   => add_query_arg(
					[
						'action' => 'ir_fcb_new_course',
					],
					admin_url( 'admin-ajax.php' ),
				),
				'is_fcc_enabled'                          => ir_get_settings( 'ir_enable_frontend_dashboard' ),
				'empty_overview_msg'                      => ir_get_settings( 'ir_frontend_overview_empty_message' ),
				'is_admin'                                => $is_admin,
				'product_review_enabled'                  => defined( 'WDMIR_REVIEW_PRODUCT' ) ? WDMIR_REVIEW_PRODUCT : false,
				'is_shared_steps'                         => learndash_is_course_shared_steps_enabled(),
				'ld_currency'                             => learndash_get_currency_symbol(),
				'woo_currency'                            => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '',
				'currency_symbol'                         => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : learndash_get_currency_symbol(),
				'woo_activated'                           => ( class_exists( 'WooCommerce' ) && class_exists( 'Learndash_WooCommerce' ) ) ? true : false,
				'is_shared_steps_questions'               => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Builder', 'shared_questions' ) : '',
				'assignments_comments_enabled'            => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'comment_status' ) : false,
				'assignments_comments_queryable'          => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'publicly_queryable' ) : false,
				'threadComments'                          => get_option( 'thread_comments_depth' ),
				'course_reports_email_nonce'              => wp_create_nonce( 'ir_send_course_email_notifications' ),
				'use_certificate_builder'                 => wp_create_nonce( 'use_certificate_builder' ),
				'is_learndash_certificate_builder_active' => $is_learndash_certificate_builder_active,
				'create_dashboard_status'                 => ir_get_settings( 'create_dashboard_onboarding' ),
				'dayjs_locale'                            => Translation::get_dayjs_locale(),
				'apex_charts_locale'                      => Translation::get_apex_charts_locale(),
				'overview_block_attributes'               => $overview_block_attributes,
			];

			/**
			 * Filter localized data for frontend dashboard blocks.
			 *
			 * @since 5.0.0
			 *
			 * @param array $localized_data     Array of localized variables.
			 */
			$localization_data = apply_filters( 'ir_filter_dashboard_localized_data', $localization_data );
			$script_loaded     = false;

			// Tabs.
			if ( has_block( 'instructor-role/wisdm-tabs' ) ) {
				if ( ! $script_loaded ) {
					wp_localize_script(
						'instructor-role-wisdm-tabs-view-script',
						'ir_fd_loc',
						$localization_data
					);
					$script_loaded = true;
				}

				wp_enqueue_style(
					'ir-dashboard-block-styles',
					plugins_url( 'css/frontend-dashboard/ir-dashboard-block-styles.css', __DIR__ ),
					[],
					filemtime( plugin_dir_path( __DIR__ ) . '/css/frontend-dashboard/ir-dashboard-block-styles.css' ),
				);

				wp_add_inline_style( 'ir-dashboard-block-styles', $this->get_dynamic_css() );
				wp_enqueue_media();
				wp_enqueue_editor();
			}

			// Settings script.
			if ( has_block( 'instructor-role/dashboard-settings' ) && ! $script_loaded ) {
				wp_localize_script(
					'instructor-role-dashboard-settings-view-script',
					'ir_fd_loc',
					$localization_data
				);
				$script_loaded = true;
			}

			// All Courses script.
			if ( has_block( 'instructor-role/wisdm-all-courses' ) && ! $script_loaded ) {
				wp_localize_script(
					'instructor-role-wisdm-all-courses-view-script',
					'ir_fd_loc',
					$localization_data
				);
				$script_loaded = true;
			}

			// All Quiz script.
			if ( has_block( 'instructor-role/wisdm-all-quizzes' ) && ! $script_loaded ) {
				wp_localize_script(
					'instructor-role-wisdm-all-quizzes-view-script',
					'ir_fd_loc',
					$localization_data
				);
				$script_loaded = true;
			}

			// Overview script.
			if ( has_block( 'instructor-role/overview-page' ) ) {
				if ( ! $script_loaded ) {
					wp_localize_script(
						'instructor-role-overview-page-view-script',
						'ir_fd_loc',
						$localization_data
					);
					$script_loaded = true;
				}

				wp_enqueue_style(
					'ir-dashboard-block-styles',
					plugins_url( 'css/frontend-dashboard/ir-dashboard-block-styles.css', __DIR__ ),
					[],
					filemtime( plugin_dir_path( __DIR__ ) . '/css/frontend-dashboard/ir-dashboard-block-styles.css' ),
				);
			}

			// Commissions Block.
			if ( has_block( 'instructor-role/wisdm-instructor-commissions' ) && ! $script_loaded ) {
				wp_localize_script(
					'instructor-role-wisdm-instructor-commissions-view-script',
					'ir_fd_loc',
					$localization_data
				);
				$script_loaded = true;
			}

			// Products Block.
			if ( has_block( 'instructor-role/wisdm-instructor-products' ) && ! $script_loaded ) {
				wp_localize_script(
					'instructor-role-wisdm-instructor-products-view-script',
					'ir_fd_loc',
					$localization_data
				);
				$script_loaded = true;
			}

			// Assignments Block.
			if ( has_block( 'instructor-role/ir-assignments' ) && ! $script_loaded ) {
				wp_localize_script(
					'instructor-role-ir-assignments-view-script',
					'ir_fd_loc',
					$localization_data
				);
				$script_loaded = true;
			}

			// Essays Block.
			if ( has_block( 'instructor-role/submitted-essays' ) && ! $script_loaded ) {
				wp_localize_script(
					'instructor-role-submitted-essays-view-script',
					'ir_fd_loc',
					$localization_data
				);
				$script_loaded = true;
			}

			// Comments Block.
			if ( has_block( 'instructor-role/wisdm-instructor-comments' ) && ! $script_loaded ) {
				wp_localize_script(
					'instructor-role-wisdm-instructor-comments-view-script',
					'ir_fd_loc',
					$localization_data
				);
				$script_loaded = true;
			}

			// Quiz Attempts Block.
			if ( has_block( 'instructor-role/wisdm-quiz-attempts' ) && ! $script_loaded ) {
				wp_localize_script(
					'instructor-role-wisdm-quiz-attempts-view-script',
					'ir_fd_loc',
					$localization_data
				);
				$script_loaded = true;
			}

			// Course Reports Block.
			if ( has_block( 'instructor-role/wisdm-course-reports' ) && ! $script_loaded ) {
				wp_localize_script(
					'instructor-role-wisdm-course-reports-view-script',
					'ir_fd_loc',
					$localization_data
				);
				$script_loaded = true;
			}

			// Groups Block.
			if ( has_block( 'instructor-role/wisdm-groups' ) && ! $script_loaded ) {
				wp_localize_script(
					'instructor-role-wisdm-groups-view-script',
					'ir_fd_loc',
					$localization_data
				);
				$script_loaded = true;
			}

			// Certificates Block.
			if ( has_block( 'instructor-role/wisdm-certificates' ) ) {
				if ( ! $script_loaded ) {
					wp_localize_script(
						'instructor-role-wisdm-certificates-view-script',
						'ir_fd_loc',
						$localization_data
					);
					$script_loaded = true;
				}

				wp_add_inline_script(
					'instructor-role-wisdm-certificates-view-script',
					"if ( typeof ajaxurl === 'undefined' ) { var ajaxurl = '" . $localization_data['ajax_url'] . "';}"
				);
			}

			wp_set_script_translations( 'instructor-role-wisdm-tabs-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-wisdm-tabs-editor-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-dashboard-settings-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-wisdm-all-courses-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-wisdm-all-quizzes-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-overview-page-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-wisdm-instructor-commissions-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-wisdm-instructor-products-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-assignments-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-submitted-essays-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-wisdm-instructor-comments-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-wisdm-quiz-attempts-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-wisdm-course-reports-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_set_script_translations( 'instructor-role-wisdm-groups-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );

			// RTL CSS Enqueue.
			if ( is_rtl() ) {
				if ( has_block( 'instructor-role/wisdm-tabs' ) ) {
					wp_enqueue_style(
						'ir-wisdm-tabs-rtl-styles',
						plugins_url( 'css/rtl/wisdmTabs-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/wisdmTabs-rtl.css' ),
					);
				}

				if ( has_block( 'instructor-role/overview-page' ) ) {
					wp_enqueue_style(
						'ir-overview-rtl-styles',
						plugins_url( 'css/rtl/overview-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/overview-rtl.css' ),
					);
				}

				if ( has_block( 'instructor-role/wisdm-all-courses' ) ) {
					wp_enqueue_style(
						'ir-all-courses-rtl-styles',
						plugins_url( 'css/rtl/allCourses-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/allCourses-rtl.css' ),
					);
				}

				if ( has_block( 'instructor-role/wisdm-all-quizzes' ) ) {
					wp_enqueue_style(
						'ir-all-quizzes-rtl-styles',
						plugins_url( 'css/rtl/allQuizzes-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/allQuizzes-rtl.css' ),
					);
				}

				if ( has_block( 'instructor-role/dashboard-settings' ) ) {
					wp_enqueue_style(
						'ir-settings-rtl-styles',
						plugins_url( 'css/rtl/settings-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/settings-rtl.css' ),
					);
				}

				if ( has_block( 'instructor-role/wisdm-instructor-commissions' ) ) {
					wp_enqueue_style(
						'ir-commissions-rtl-styles',
						plugins_url( 'css/rtl/commissions-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/commissions-rtl.css' ),
					);
				}
				if ( has_block( 'instructor-role/wisdm-instructor-products' ) ) {
					wp_enqueue_style(
						'ir-products-rtl-styles',
						plugins_url( 'css/rtl/products-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/products-rtl.css' ),
					);
				}
				if ( has_block( 'instructor-role/ir-assignments' ) ) {
					wp_enqueue_style(
						'ir-assignments-rtl-styles',
						plugins_url( 'css/rtl/assignments-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/assignments-rtl.css' ),
					);
				}
				if ( has_block( 'instructor-role/submitted-essays' ) ) {
					wp_enqueue_style(
						'ir-submitted-essays-rtl-styles',
						plugins_url( 'css/rtl/submitted-essays-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/submitted-essays-rtl.css' ),
					);
				}

				if ( has_block( 'instructor-role/wisdm-instructor-comments' ) ) {
					wp_enqueue_style(
						'ir-comments-rtl-styles',
						plugins_url( 'css/rtl/comments-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/comments-rtl.css' ),
					);
				}

				if ( has_block( 'instructor-role/wisdm-course-reports' ) ) {
					wp_enqueue_style(
						'ir-course-reports-rtl-styles',
						plugins_url( 'css/rtl/courseReports-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/courseReports-rtl.css' ),
					);
				}

				if ( has_block( 'instructor-role/wisdm-quiz-attempts' ) ) {
					wp_enqueue_style(
						'ir-quiz-attempts-rtl-styles',
						plugins_url( 'css/rtl/quizAttempts-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/quizAttempts-rtl.css' ),
					);
				}

				if ( has_block( 'instructor-role/wisdm-groups' ) ) {
					wp_enqueue_style(
						'ir-groups-rtl-styles',
						plugins_url( 'css/rtl/groups-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/groups-rtl.css' ),
					);
				}

				if ( get_query_var( 'quiz_builder' ) ) {
					wp_enqueue_style(
						'ir-quiz-builder-rtl-styles',
						plugins_url( 'css/rtl/quizBuilder-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/quizBuilder-rtl.css' ),
					);
					wp_enqueue_style(
						'ir-course-builder-main-rtl-styles',
						plugins_url( 'css/rtl/ir-course-builder-style-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/ir-course-builder-style-rtl.css' ),
					);
				}

				if ( get_query_var( 'course_builder' ) ) {
					wp_enqueue_style(
						'ir-course-builder-rtl-styles',
						plugins_url( 'css/rtl/courseBuilder-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/courseBuilder-rtl.css' ),
					);
					wp_enqueue_style(
						'ir-course-builder-main-rtl-styles',
						plugins_url( 'css/rtl/ir-course-builder-style-rtl.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/rtl/ir-course-builder-style-rtl.css' ),
					);
				}
			}
		}

		/**
		 * Add Dashboard Block Settings tab in Instructor Settings
		 *
		 * @since 5.0.0
		 *
		 * @param array  $tabs          Array of tabs.
		 * @param string $current_tab   Current selected instructor tab.
		 */
		public function add_dashboard_block_settings_tab( $tabs, $current_tab ) {
			// Check if admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return $tabs;
			}

			// Check if dashboard block tab already exists.
			if ( ! array_key_exists( 'ir-dashboard-settings', $tabs ) ) {
				$tabs['ir-dashboard-settings'] = [
					'title'  => __( 'Frontend Dashboard', 'wdm_instructor_role' ),
					'access' => [ 'admin' ],
				];
			}
			return $tabs;
		}

		/**
		 * Display frontend dashboard global settings for configuring frontend dashboard settings.
		 *
		 * @since 5.0.0
		 *
		 * @param string $current_tab   Slug of the selected tab in instructor settings.
		 */
		public function add_dashboard_block_settings_tab_content( $current_tab ) {
			// Check if admin and dashboard tab.
			if ( ! current_user_can( 'manage_options' ) || 'ir-dashboard-settings' !== $current_tab ) {
				return;
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

			$active_menu = 'view';
			if ( array_key_exists( 'fdb_manual_edit', $_GET ) && $_GET['fdb_manual_edit'] ) {
				$active_menu = 'menu';
			}

			// Render Template.
			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/settings/ir-dashboard-block-settings.template.php',
				[
					// Menu Settings.
					'ir_frontend_overview_block'           => ir_get_settings( 'ir_frontend_overview_block' ),
					'ir_frontend_courses_block'            => ir_get_settings( 'ir_frontend_courses_block' ),
					'ir_frontend_quizzes_block'            => ir_get_settings( 'ir_frontend_quizzes_block' ),
					'ir_frontend_settings_block'           => ir_get_settings( 'ir_frontend_settings_block' ),
					'ir_frontend_products_block'           => ir_get_settings( 'ir_frontend_products_block' ),
					'ir_frontend_commissions_block'        => ir_get_settings( 'ir_frontend_commissions_block' ),
					'ir_frontend_assignments_block'        => ir_get_settings( 'ir_frontend_assignments_block' ),
					'ir_frontend_essays_block'             => ir_get_settings( 'ir_frontend_essays_block' ),
					'ir_frontend_quiz_attempts_block'      => ir_get_settings( 'ir_frontend_quiz_attempts_block' ),
					'ir_frontend_comments_block'           => ir_get_settings( 'ir_frontend_comments_block' ),
					'ir_frontend_course_reports_block'     => ir_get_settings( 'ir_frontend_course_reports_block' ),
					'ir_frontend_groups_block'             => ir_get_settings( 'ir_frontend_groups_block' ),
					'ir_frontend_certificates_block'       => ir_get_settings( 'ir_frontend_certificates_block' ),
					// Overview Settings.
					'ir_frontend_overview_course_tile_block' => ir_get_settings( 'ir_frontend_overview_course_tile_block' ),
					'ir_frontend_overview_student_tile_block' => ir_get_settings( 'ir_frontend_overview_student_tile_block' ),
					'ir_frontend_overview_submissions_tile_block' => ir_get_settings( 'ir_frontend_overview_submissions_tile_block' ),
					'ir_frontend_overview_quiz_attempts_tile_block' => ir_get_settings( 'ir_frontend_overview_quiz_attempts_tile_block' ),
					'ir_frontend_overview_course_progress_block' => ir_get_settings( 'ir_frontend_overview_course_progress_block' ),
					'ir_frontend_overview_top_courses_block' => ir_get_settings( 'ir_frontend_overview_top_courses_block' ),
					'ir_frontend_overview_earnings_block'  => ir_get_settings( 'ir_frontend_overview_earnings_block' ),
					'ir_frontend_overview_submissions_block' => ir_get_settings( 'ir_frontend_overview_submissions_block' ),
					'ir_frontend_overview_empty_message'   => ir_get_settings( 'ir_frontend_overview_empty_message' ),
					// Appearance Settings.
					'ir_frontend_appearance_color_scheme'  => ir_get_settings( 'ir_frontend_appearance_color_scheme' ),
					'ir_frontend_appearance_custom_primary' => ir_get_settings( 'ir_frontend_appearance_custom_primary' ),
					'ir_frontend_appearance_custom_accent' => ir_get_settings( 'ir_frontend_appearance_custom_accent' ),
					'ir_frontend_appearance_custom_background' => ir_get_settings( 'ir_frontend_appearance_custom_background' ),
					'ir_frontend_appearance_custom_headings' => ir_get_settings( 'ir_frontend_appearance_custom_headings' ),
					'ir_frontend_appearance_custom_text'   => ir_get_settings( 'ir_frontend_appearance_custom_text' ),
					'ir_frontend_appearance_custom_border' => ir_get_settings( 'ir_frontend_appearance_custom_border' ),
					'ir_frontend_appearance_custom_side_bg' => ir_get_settings( 'ir_frontend_appearance_custom_side_bg' ),
					'ir_frontend_appearance_custom_side_mt' => ir_get_settings( 'ir_frontend_appearance_custom_side_mt' ),
					'ir_frontend_appearance_custom_text_light' => ir_get_settings( 'ir_frontend_appearance_custom_text_light' ),
					'ir_frontend_appearance_custom_text_ex_light' => ir_get_settings( 'ir_frontend_appearance_custom_text_ex_light' ),
					'ir_frontend_appearance_custom_text_primary_btn' => ir_get_settings( 'ir_frontend_appearance_custom_text_primary_btn' ),
					'fonts'                                => $fonts,
					'ir_frontend_appearance_font_family'   => ir_get_settings( 'ir_frontend_appearance_font_family' ),
					'ir_frontend_appearance_font_size'     => ir_get_settings( 'ir_frontend_appearance_font_size' ),
					// Dashboard Launch.
					'is_dashboard_launched'                => get_option( 'ir_frontend_dashboard_launched', false ),
					// Dashboard Page.
					'dashboard_page_id'                    => get_option( 'ir_frontend_dashboard_page', false ),
					'ir_enable_frontend_dashboard'         => ir_get_settings( 'ir_enable_frontend_dashboard' ),
					// Miscellaneous.
					'active_menu'                          => $active_menu,
					'banner_img'                           => plugins_url( '/images/frontend-db-intro.png', __DIR__ ),
					'create_frontend_dashboard_link'       => add_query_arg(
						[
							'action' => 'ir_create_new_dashboard_page',
							'nonce'  => wp_create_nonce( 'ir-create-dashboard-page' ),
						],
						admin_url( 'admin-ajax.php' )
					),
					'help_tooltip_img'                     => plugins_url( '/images/help-tooltip.svg', __DIR__ ),
					'ir_is_gutenberg_enabled'              => ir_is_gutenberg_enabled(),
				]
			);
		}

		/**
		 * Save global frontend dashboard settings
		 *
		 * @since 5.0.0
		 */
		public function save_dashboard_block_settings() {
			// If not admin then return.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_nonce', $_POST ) ) {
				return;
			}

			// Fetch and save dashboard settings.
			// Menu.
			if ( wp_verify_nonce( filter_input( INPUT_POST, 'ir_nonce' ), 'frontend_dashboard_menu_settings_nonce' ) ) {
				ir_set_settings( 'ir_frontend_overview_block', ir_filter_input( 'ir_frontend_overview_block' ) );
				ir_set_settings( 'ir_frontend_courses_block', ir_filter_input( 'ir_frontend_courses_block' ) );
				ir_set_settings( 'ir_frontend_quizzes_block', ir_filter_input( 'ir_frontend_quizzes_block' ) );
				ir_set_settings( 'ir_frontend_settings_block', ir_filter_input( 'ir_frontend_settings_block' ) );
				ir_set_settings( 'ir_frontend_products_block', ir_filter_input( 'ir_frontend_products_block' ) );
				ir_set_settings( 'ir_frontend_commissions_block', ir_filter_input( 'ir_frontend_commissions_block' ) );
				ir_set_settings( 'ir_frontend_assignments_block', ir_filter_input( 'ir_frontend_assignments_block' ) );
				ir_set_settings( 'ir_frontend_essays_block', ir_filter_input( 'ir_frontend_essays_block' ) );
				ir_set_settings( 'ir_frontend_quiz_attempts_block', ir_filter_input( 'ir_frontend_quiz_attempts_block' ) );
				ir_set_settings( 'ir_frontend_comments_block', ir_filter_input( 'ir_frontend_comments_block' ) );
				ir_set_settings( 'ir_frontend_course_reports_block', ir_filter_input( 'ir_frontend_course_reports_block' ) );
				ir_set_settings( 'ir_frontend_groups_block', ir_filter_input( 'ir_frontend_groups_block' ) );
				ir_set_settings( 'ir_frontend_certificates_block', ir_filter_input( 'ir_frontend_certificates_block' ) );

				$this->update_dashboard_page_content();
			}

			// Overview.
			if ( wp_verify_nonce( filter_input( INPUT_POST, 'ir_nonce' ), 'frontend_dashboard_overview_settings_nonce' ) ) {
				ir_set_settings( 'ir_frontend_overview_course_tile_block', ir_filter_input( 'ir_frontend_overview_course_tile_block' ) );
				ir_set_settings( 'ir_frontend_overview_student_tile_block', ir_filter_input( 'ir_frontend_overview_student_tile_block' ) );
				ir_set_settings( 'ir_frontend_overview_submissions_tile_block', ir_filter_input( 'ir_frontend_overview_submissions_tile_block' ) );
				ir_set_settings( 'ir_frontend_overview_quiz_attempts_tile_block', ir_filter_input( 'ir_frontend_overview_quiz_attempts_tile_block' ) );
				ir_set_settings( 'ir_frontend_overview_course_progress_block', ir_filter_input( 'ir_frontend_overview_course_progress_block' ) );
				ir_set_settings( 'ir_frontend_overview_top_courses_block', ir_filter_input( 'ir_frontend_overview_top_courses_block' ) );
				ir_set_settings( 'ir_frontend_overview_earnings_block', ir_filter_input( 'ir_frontend_overview_earnings_block' ) );
				ir_set_settings( 'ir_frontend_overview_submissions_block', ir_filter_input( 'ir_frontend_overview_submissions_block' ) );
				ir_set_settings( 'ir_frontend_overview_empty_message', ir_filter_input( 'ir_frontend_overview_empty_message' ) );

				$this->update_dashboard_page_content();
			}

			// Appearance.
			if ( wp_verify_nonce( ir_filter_input( 'ir_nonce' ), 'frontend_dashboard_appearance_settings_nonce' ) ) {
				ir_set_settings( 'ir_frontend_appearance_color_scheme', ir_filter_input( 'ir_frontend_appearance_color_scheme' ) );
				ir_set_settings( 'ir_frontend_appearance_custom_primary', ir_filter_input( 'ir_frontend_appearance_custom_primary' ) );
				ir_set_settings( 'ir_frontend_appearance_custom_accent', ir_filter_input( 'ir_frontend_appearance_custom_accent' ) );
				ir_set_settings( 'ir_frontend_appearance_custom_background', ir_filter_input( 'ir_frontend_appearance_custom_background' ) );
				ir_set_settings( 'ir_frontend_appearance_custom_headings', ir_filter_input( 'ir_frontend_appearance_custom_headings' ) );
				ir_set_settings( 'ir_frontend_appearance_custom_text', ir_filter_input( 'ir_frontend_appearance_custom_text' ) );
				ir_set_settings( 'ir_frontend_appearance_custom_border', ir_filter_input( 'ir_frontend_appearance_custom_border' ) );
				ir_set_settings( 'ir_frontend_appearance_custom_side_bg', ir_filter_input( 'ir_frontend_appearance_custom_side_bg' ) );
				ir_set_settings( 'ir_frontend_appearance_custom_side_mt', ir_filter_input( 'ir_frontend_appearance_custom_side_mt' ) );
				ir_set_settings( 'ir_frontend_appearance_custom_text_light', ir_filter_input( 'ir_frontend_appearance_custom_text_light' ) );
				ir_set_settings( 'ir_frontend_appearance_custom_text_ex_light', ir_filter_input( 'ir_frontend_appearance_custom_text_ex_light' ) );
				ir_set_settings( 'ir_frontend_appearance_custom_text_primary_btn', ir_filter_input( 'ir_frontend_appearance_custom_text_primary_btn' ) );
				ir_set_settings( 'ir_frontend_appearance_font_family', ir_filter_input( 'ir_frontend_appearance_font_family' ) );
				ir_set_settings( 'ir_frontend_appearance_font_size', ir_filter_input( 'ir_frontend_appearance_font_size' ) );

				$this->update_dashboard_page_content();
			}

			// View.
			if ( wp_verify_nonce( ir_filter_input( 'ir_nonce' ), 'frontend_dashboard_view_settings_nonce' ) ) {
				update_option( 'ir_frontend_dashboard_page', ir_filter_input( 'ir_frontend_dashboard_page' ) );
			}
		}

		/**
		 * Enqueue settings pages scripts and styles.
		 *
		 * @since 5.0.0
		 */
		public function enqueue_settings_scripts() {
			global $current_screen;

			// Instructor settings scripts.
			$page_slug = sanitize_title( __( 'LearnDash LMS', 'learndash' ) ) . '_page_instuctor';
			if ( $page_slug === $current_screen->id && ! empty( $_GET ) && array_key_exists( 'page', $_GET ) && 'instuctor' === $_GET['page'] && array_key_exists( 'tab', $_GET ) && 'dashboard_settings' === $_GET['tab'] ) {
				wp_enqueue_script(
					'ir-frontend-dashboard-settings-script',
					plugins_url( 'js/settings/ir-frontend-dashboard-settings.js', __DIR__ ),
					[ 'jquery' ],
					filemtime( plugin_dir_path( __DIR__ ) . '/js/settings/ir-frontend-dashboard-settings.js' ),
					true
				);
				wp_enqueue_style(
					'ir-dashboard-settings-styles',
					plugins_url( 'css/dashboard/ir-dashboard-settings-styles.css', __DIR__ ),
					[],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/css/dashboard/ir-dashboard-settings-styles.css' )
				);
				wp_localize_script(
					'ir-frontend-dashboard-settings-script',
					'ir_fd_loc',
					[
						'ajax_url'         => admin_url( 'admin-ajax.php' ),
						'dashboard_colors' => $this->get_dashboard_colors(),
					]
				);
			}

			// Enqueue Edit page block scripts.
			if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
				if ( wp_script_is( 'instructor-role-wisdm-tabs-editor-script' ) ) {
					if ( current_user_can( 'manage_options' ) ) {
						$is_admin = true;
					} else {
						$is_admin = false;
					}

					wp_localize_script(
						'instructor-role-wisdm-tabs-editor-script',
						'ir_fd_loc',
						[
							'export_order_details_nonce'   => wp_create_nonce( 'ir-export-order-details' ),
							'export_manual_commission_log_nonce' => wp_create_nonce( 'ir-export-manual-commission-log' ),
							'update_commission_log_nonce'  => wp_create_nonce( 'ir_update_commission_log' ),
							'delete_manual_commission_log_nonce' => wp_create_nonce( 'ir_commission_log_actions' ),
							'ir_commission_paypal_payout_nonce' => wp_create_nonce( 'ir_commission_paypal_payout_payment' ),
							'course_label'                 => \LearnDash_Custom_Label::get_label( 'course' ),
							'group_label'                  => \LearnDash_Custom_Label::get_label( 'group' ),
							'groups_label'                 => \LearnDash_Custom_Label::get_label( 'groups' ),
							'lesson_label'                 => \LearnDash_Custom_Label::get_label( 'lesson' ),
							'topic_label'                  => \LearnDash_Custom_Label::get_label( 'topic' ),
							'quiz_label'                   => \LearnDash_Custom_Label::get_label( 'quiz' ),
							'courses_label'                => \LearnDash_Custom_Label::get_label( 'courses' ),
							'lessons_label'                => \LearnDash_Custom_Label::get_label( 'lessons' ),
							'topics_label'                 => \LearnDash_Custom_Label::get_label( 'topics' ),
							'quizzes_label'                => \LearnDash_Custom_Label::get_label( 'quizzes' ),
							'lower_course_label'           => \LearnDash_Custom_Label::label_to_lower( 'course' ),
							'lower_courses_label'          => \LearnDash_Custom_Label::label_to_lower( 'courses' ),
							'lower_lesson_label'           => \LearnDash_Custom_Label::label_to_lower( 'lesson' ),
							'lower_topic_label'            => \LearnDash_Custom_Label::label_to_lower( 'topic' ),
							'lower_quiz_label'             => \LearnDash_Custom_Label::label_to_lower( 'quiz' ),
							'lower_quizzes_label'          => \LearnDash_Custom_Label::label_to_lower( 'quizzes' ),
							'lower_group_label'            => \LearnDash_Custom_Label::label_to_lower( 'group' ),
							'dashboard_colors'             => $this->get_dashboard_colors(),
							'active_tab'                   => $this->get_active_dashboard_tab(),
							'create_new_course_url'        => add_query_arg(
								[
									'action' => 'ir_fcb_new_course',
								],
								admin_url( 'admin-ajax.php' )
							),
							'is_fcc_enabled'               => ir_get_settings( 'ir_enable_frontend_dashboard' ),
							'empty_overview_msg'           => ir_get_settings( 'ir_frontend_overview_empty_message' ),
							'is_admin'                     => $is_admin,
							'product_review_enabled'       => defined( 'WDMIR_REVIEW_PRODUCT' ) ? WDMIR_REVIEW_PRODUCT : false,
							'is_shared_steps'              => learndash_is_course_shared_steps_enabled(),
							'ld_currency'                  => learndash_get_currency_symbol(),
							'woo_currency'                 => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '',
							'currency_symbol'              => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : learndash_get_currency_symbol(),
							'woo_activated'                => ( class_exists( 'WooCommerce' ) && class_exists( 'Learndash_WooCommerce' ) ) ? true : false,
							'is_shared_steps_questions'    => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Builder', 'shared_questions' ) : '',
							'assignments_comments_enabled' => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'comment_status' ) : false,
							'assignments_comments_queryable' => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'publicly_queryable' ) : false,
							'threadComments'               => get_option( 'thread_comments_depth' ),
							'course_reports_email_nonce'   => wp_create_nonce( 'ir_send_course_email_notifications' ),
							'apex_charts_locale'           => Translation::get_apex_charts_locale(),
						]
					);

					// Get Dashboard.
					$is_dashboard_page = ir_filter_input( 'ir_action', INPUT_GET );

					if ( $is_dashboard_page && 'setup_ir_dashboard' === $is_dashboard_page ) {
						wp_enqueue_script(
							'ir-frontend-dashboard-setup-script',
							plugins_url( 'js/frontend-dashboard/ir-frontend-dashboard-setup-script.js', __DIR__ ),
							[ 'jquery' ],
							filemtime( plugin_dir_path( __DIR__ ) . '/js/frontend-dashboard/ir-frontend-dashboard-setup-script.js' ),
							true
						);

						wp_localize_script(
							'ir-frontend-dashboard-setup-script',
							'ir_setup_data',
							[
								'launch_popup_html'  => ir_get_template(
									INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-dashboard-setup-notice.template.php',
									[],
									1,
								),
								'is_launch_complete' => get_option( 'ir_frontend_dashboard_launched' ),
							]
						);
					}
				}
			}
		}

		/**
		 * Setup frontend dashboard.
		 *
		 * @since 5.0.0
		 */
		public function setup_frontend_dashboard() {
			$response = [
				'status'  => 'error',
				'message' => __( 'Some error occurred. Please refresh and try again', 'wdm_instructor_role' ),
			];

			if ( ! current_user_can( 'manage_options' ) ) {
				echo wp_json_encode(
					[
						'status'  => 'error',
						'message' => __( 'Sorry you do not have sufficient privileges to perform this action. Please contact admin.', 'wdm_instructor_role' ),
					]
				);
				wp_die();
			}

			// Verify nonce.
			if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'ir_nonce' ), 'frontend_dashboard_view_settings_nonce' ) ) {
				echo wp_json_encode( $response );
				wp_die();
			}

			// Update other settings.
			$enable_fcc      = ir_filter_input( 'enable_fcc', INPUT_POST, 'bool' );
			$add_dash_link   = ir_filter_input( 'add_dash_link', INPUT_POST, 'bool' );
			$login_redirect  = ir_filter_input( 'login_redirect', INPUT_POST, 'bool' );
			$disable_backend = ir_filter_input( 'disable_backend', INPUT_POST, 'bool' );

			// Enable frontend dashboard.
			if ( $enable_fcc ) {
				ir_set_settings( 'ir_enable_frontend_dashboard', 'on' );
			}

			// Add dashboard link on frontend.
			if ( $add_dash_link ) {
				ir_set_settings( 'wdm_id_ir_dash_pri_menu', 'on' );
			}

			// Add login redirect for instructors.
			if ( $login_redirect ) {
				ir_set_settings( 'wdm_login_redirect', 'on' );
			}

			// Disable backend(WP) dashboard.
			if ( $disable_backend ) {
				ir_set_settings( 'ir_disable_backend_dashboard', 1 );
			}

			// Check if page already exists.
			$frontend_dashboard_page = get_option( 'ir_frontend_dashboard_page' );

			if ( false !== $frontend_dashboard_page ) {
				// Set login redirect to frontend dashboard.
				if ( $login_redirect ) {
					ir_set_settings( 'wdm_login_redirect_page', $frontend_dashboard_page );
				}

				// If gutenberg disabled, visit the actual page instead of edit page.
				$edit_url = add_query_arg(
					[
						'post'   => $frontend_dashboard_page,
						'action' => 'edit',
					],
					admin_url( 'post.php' )
				);

				if ( ! ir_is_gutenberg_enabled() ) {
					$edit_url = get_permalink( $frontend_dashboard_page );
				}

				echo wp_json_encode(
					[
						'status'   => 'error',
						'message'  => __( 'Dashboard already setup, no need to setup twice. Please contact admin.', 'wdm_instructor_role' ),
						'view_url' => get_permalink( $frontend_dashboard_page ),
						'edit_url' => $edit_url,
					]
				);
				wp_die();
			}

			// Setup Vanilla Dashboard Settings.
			$this->configure_vanilla_frontend_dashboard_settings();

			// Create new dashboard page.
			$new_page = [
				'post_title'   => __( 'Instructor Dashboard', 'wdm_instructor_role' ),
				'post_status'  => 'draft',
				'post_author'  => get_current_user_id(),
				'post_type'    => 'page',
				'post_content' => ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-static-dashboard-block.template.php',
					[],
					true
				),
				'meta_input'   => [
					'_wp_page_template' => 'ir-wisdm-dashboard.template.php',
				],
			];

			// If gutenberg disabled, publish page instead of drafting.
			if ( ! ir_is_gutenberg_enabled() ) {
				$new_page['post_status'] = 'publish';
			}

			// Set backend layouts to  new layouts.
			ir_set_settings( 'ir_dashboard_layout', 'layout-2' );

			// Insert the post into the database.
			$page_id = wp_insert_post( $new_page );

			if ( empty( $page_id ) || is_wp_error( $page_id ) ) {
				$response = [
					'status'  => 'error',
					'message' => $page_id->get_error_message(),
				];
			} else {
				$edit_url = add_query_arg(
					[
						'post'      => $page_id,
						'action'    => 'edit',
						'ir_action' => 'setup_ir_dashboard',
					],
					admin_url( 'post.php' )
				);

				// If gutenberg disabled, visit the actual page instead of edit page.
				if ( ! ir_is_gutenberg_enabled() ) {
					$edit_url = get_permalink( $page_id );
				}

				$response = [
					'status'   => 'success',
					'page'     => get_post( $page_id ),
					'edit_url' => $edit_url,
				];

				// Save page ID in options.
				update_option( 'ir_frontend_dashboard_page', $page_id );
				ir_set_settings( 'create_dashboard_onboarding', 'step_2' );

				// Set login redirect to frontend dashboard.
				if ( $login_redirect ) {
					ir_set_settings( 'wdm_login_redirect_page', $page_id );
				}
			}

			echo wp_json_encode( $response );

			wp_die();
		}

		/**
		 * Handle the modal shown on frontend dashboard launch.
		 *
		 * @since 5.0.0
		 *
		 * @param string $content   The post content.
		 * @return string           Updated post content.
		 */
		public function handle_dashboard_launch_modal( $content ) {
			global $post;

			$auto_generated_page = get_option( 'ir_frontend_dashboard_page', 0 );
			$visited_dashboard   = get_option( 'ir_frontend_dashboard_launched', false );
			$fd_page_id          = get_option( 'ir_frontend_dashboard_page', false );

			if ( is_admin() || ( intval( $auto_generated_page ) !== $post->ID ) || defined( 'REST_REQUEST' ) || ! current_user_can( 'manage_options' ) ) {
				return $content;
			}

			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				if ( false === $visited_dashboard ) {
					wp_enqueue_style(
						'ir_frontend_dashboard_launch_style',
						plugins_url( 'css/frontend-dashboard/ir-frontend-dashboard-launch.css', __DIR__ ),
						[],
						filemtime( plugin_dir_path( __DIR__ ) . '/css/frontend-dashboard/ir-frontend-dashboard-launch.css' ),
					);

					wp_enqueue_script(
						'ir_frontend_dashboard_launch_script',
						plugins_url( 'js/frontend-dashboard/ir-frontend-dashboard-launch.js', __DIR__ ),
						[ 'jquery' ],
						filemtime( plugin_dir_path( __DIR__ ) . '/js/frontend-dashboard/ir-frontend-dashboard-launch.js' ),
						true
					);

					wp_localize_script(
						'ir_frontend_dashboard_launch_script',
						'ir_fd_data',
						[
							'ajax_url'                     => admin_url( 'admin-ajax.php' ),
							'nonce'                        => wp_create_nonce( 'ir_complete_dashboard_launch' ),
							'frontend_dashboard_edit_link' => get_edit_post_link( $fd_page_id, 'edit' ),
						]
					);

					return $content . ir_get_template(
						INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-frontend-dashboard-launch-modal.template.php',
						[
							'background_img' => plugins_url( '/css/images/new_modal_bg.png', __DIR__ ),
							'center_img'     => plugins_url( '/images/dashboard-created.png', __DIR__ ),
						],
						true
					);
				}
			}
			return $content;
		}

		/**
		 * Complete the dashboard launch process.
		 *
		 * @since 5.0.0
		 */
		public function complete_dashboard_launch() {
			$response = [
				'status'  => 'error',
				'message' => __( 'Some error occurred. Please refresh and try again', 'wdm_instructor_role' ),
			];

			if ( ! wp_verify_nonce( ir_filter_input( 'ir_nonce' ), 'ir_complete_dashboard_launch' ) ) {
				echo wp_json_encode( $response );
				wp_die();
			}

			update_option( 'ir_frontend_dashboard_launched', 1 );
			ir_set_settings( 'create_dashboard_onboarding', 'step_3' );

			echo wp_json_encode(
				[
					'status'       => 'success',
					'message'      => __( 'Launch Process completed successfully', 'wdm_instructor_role' ),
					'resume_setup' => add_query_arg(
						[
							'page'       => 'instuctor',
							'tab'        => 'dashboard_settings',
							'onboarding' => 'step_3',
						],
						admin_url( 'admin.php' )
					),
				]
			);
			wp_die();
		}

		/**
		 * Handle login redirects for guest users landing on frontend dashboard.
		 *
		 * @since 5.0.0
		 */
		public function handle_guest_dashboard_template() {
			// Check if access enabled for all users.
			$enable_tabs_access = ir_get_settings( 'enable_tabs_access' );
			if ( $enable_tabs_access ) {
				return;
			}

			// Check if current page has dashboard blocks included.
			if ( has_block( 'instructor-role/wisdm-tabs' ) ) {
				if ( current_user_can( 'manage_options' ) || wdm_is_instructor() ) {
					return;
				}

				/**
				 * Filter the redirect URL for guest users landing on frontend dashboard page.
				 *
				 * @since 5.0.0
				 *
				 * @param string $url   URL to redirect to. Default set to home url.
				 */
				$redirect_url = apply_filters( 'ir_filter_guest_frontend_dashboard_redirect', home_url() );

				// Prevent a home page redirect loop.
				if ( home_url() === $redirect_url ) {
					global $wp;

					// Return since already on home page.
					if ( empty( $wp->request ) ) {
						return;
					}
				}
				wp_safe_redirect( $redirect_url );
			}
		}

		/**
		 * Get the active dashboard tab.
		 *
		 * @since 5.0.0
		 *
		 * @return int  Index of the active dashboard tab.
		 */
		public function get_active_dashboard_tab() {
			$active_index             = 0;
			$enable_overview_block    = ir_get_settings( 'ir_frontend_overview_block' );
			$enable_courses_block     = ir_get_settings( 'ir_frontend_courses_block' );
			$enable_quizzes_block     = ir_get_settings( 'ir_frontend_quizzes_block' );
			$enable_products_block    = ir_get_settings( 'ir_frontend_products_block' );
			$enable_commissions_block = ir_get_settings( 'ir_frontend_commissions_block' );
			$enable_assignments_block = ir_get_settings( 'ir_frontend_assignments_block' );
			$enable_essays_block      = ir_get_settings( 'ir_frontend_essays_block' );

			// If overview enabled, return 1st tab as active.
			if ( $enable_overview_block ) {
				return $active_index;
			}

			// If course list enabled, return 2nd tab as active.
			if ( $enable_courses_block ) {
				return ( $active_index + 1 );
			}

			// If quiz list enabled, return 3rd tab as active.
			if ( $enable_quizzes_block ) {
				return ( $active_index + 2 );
			}

			// If products list enabled, return 4th tab as active.
			if ( $enable_products_block ) {
				return ( $active_index + 3 );
			}

			// If commissions list enabled, return 5th tab as active.
			if ( $enable_commissions_block ) {
				return ( $active_index + 4 );
			}

			// If assignments list enabled, return 6th tab as active.
			if ( $enable_assignments_block ) {
				return ( $active_index + 5 );
			}

			// If essays list enabled, return 7th tab as active.
			if ( $enable_essays_block ) {
				return ( $active_index + 6 );
			}

			// Return last (8th) tab as active.
			return ( $active_index + 7 );
		}

		/**
		 * Update dashboard page on settings update
		 *
		 * @since 5.0.0
		 */
		public function update_dashboard_page_content() {
			$dashboard_page = get_option( 'ir_frontend_dashboard_page' );

			if ( false !== $dashboard_page ) {
				$post_content = ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-dynamic-dashboard-block.template.php',
					[
						'active_tab'                   => $this->get_active_dashboard_tab(),
						'tab_settings_json'            => wp_json_encode( $this->get_global_tab_settings() ),
						'custom_styles'                => $this->get_dynamic_css(),
						'overview_block_setting'       => $this->get_global_overview_settings(),
						'is_overview_tab_active'       => ir_get_settings( 'ir_frontend_overview_block' ),
						'is_courses_tab_active'        => ir_get_settings( 'ir_frontend_courses_block' ),
						'is_quizzes_tab_active'        => ir_get_settings( 'ir_frontend_quizzes_block' ),
						'is_settings_tab_active'       => ir_get_settings( 'ir_frontend_settings_block' ),
						'is_products_tab_active'       => ir_get_settings( 'ir_frontend_products_block' ),
						'is_commissions_tab_active'    => ir_get_settings( 'ir_frontend_commissions_block' ),
						'is_assignments_tab_active'    => ir_get_settings( 'ir_frontend_assignments_block' ),
						'is_essays_tab_active'         => ir_get_settings( 'ir_frontend_essays_block' ),
						'is_quiz_attempts_tab_active'  => ir_get_settings( 'ir_frontend_quiz_attempts_block' ),
						'is_comments_tab_active'       => ir_get_settings( 'ir_frontend_comments_block' ),
						'is_course_reports_tab_active' => ir_get_settings( 'ir_frontend_course_reports_block' ),
						'is_groups_tab_active'         => ir_get_settings( 'ir_frontend_groups_block' ),
						'is_certificates_tab_active'   => ir_get_settings( 'ir_frontend_certificates_block' ),
						'custom_font'                  => ir_get_settings( 'ir_frontend_appearance_font_family' ),
						'tab_index'                    => 0,
					],
					true
				);

				wp_update_post(
					[
						'ID'           => $dashboard_page,
						'post_content' => $post_content,
					]
				);
			}
		}

		/**
		 * Configure vanilla frontend dashboard global settings.
		 *
		 * @since 5.0.0
		 */
		public static function configure_vanilla_frontend_dashboard_settings() {
			$dashboard_default_settings = [
				// Menu.
				'ir_frontend_overview_block'               => 'on',
				'ir_frontend_courses_block'                => 'on',
				'ir_frontend_quizzes_block'                => 'on',
				'ir_frontend_settings_block'               => 'on',
				'ir_frontend_products_block'               => 'on',
				'ir_frontend_commissions_block'            => 'on',
				'ir_frontend_assignments_block'            => 'on',
				'ir_frontend_essays_block'                 => 'on',
				'ir_frontend_quiz_attempts_block'          => 'on',
				'ir_frontend_comments_block'               => 'on',
				'ir_frontend_course_reports_block'         => 'on',
				'ir_frontend_groups_block'                 => 'on',
				'ir_frontend_certificates_block'           => 'on',
				// Overview.
				'ir_frontend_overview_course_tile_block'   => 'on',
				'ir_frontend_overview_student_tile_block'  => 'on',
				'ir_frontend_overview_submissions_tile_block' => 'on',
				'ir_frontend_overview_quiz_attempts_tile_block' => 'on',
				'ir_frontend_overview_course_progress_block' => 'on',
				'ir_frontend_overview_top_courses_block'   => 'on',
				'ir_frontend_overview_earnings_block'      => 'on',
				'ir_frontend_overview_submissions_block'   => 'on',
				'ir_frontend_overview_empty_message'       => '',
				// Appearance.
				'ir_frontend_appearance_color_scheme'      => 'calm_ocean',
				'ir_frontend_appearance_font_size'         => '16px',
				'ir_frontend_appearance_font_family'       => '',
				'ir_frontend_appearance_custom_primary'    => '#2067FA',
				'ir_frontend_appearance_custom_accent'     => '#F3F9FB',
				'ir_frontend_appearance_custom_background' => '#FBFCFF',
				'ir_frontend_appearance_custom_headings'   => '#2E353C',
				'ir_frontend_appearance_custom_text'       => '#666666',
				'ir_frontend_appearance_custom_border'     => '#D6D8E7',
				'ir_frontend_appearance_custom_side_bg'    => '#FFFFFF',
				'ir_frontend_appearance_custom_side_mt'    => '#666666',
				'ir_frontend_appearance_custom_text_light' => '#868E96',
				'ir_frontend_appearance_custom_text_ex_light' => '#ADB5BD',
				'ir_frontend_appearance_custom_text_primary_btn' => '#F5F3EC',
				// FCC Appearance.
				'ir_enable_frontend_dashboard'             => 'on',
				'ir_enable_sync'                           => 'on',
				'ir_frontend_course_creator_color_scheme'  => 'calm_ocean',
				'ir_frontend_course_creator_font_size'     => '16px',
				'ir_frontend_course_creator_font_family'   => '',
				'ir_frontend_course_creator_custom_primary' => '#2067FA',
				'ir_frontend_course_creator_custom_accent' => '#F3F9FB',
				'ir_frontend_course_creator_custom_background' => '#FBFCFF',
				'ir_frontend_course_creator_custom_headings' => '#2E353C',
				'ir_frontend_course_creator_custom_text'   => '#666666',
				'ir_frontend_course_creator_custom_border' => '#D6D8E7',
				'ir_frontend_course_creator_custom_text_light' => '#868E96',
				'ir_frontend_course_creator_custom_text_ex_light' => '#ADB5BD',
				'ir_frontend_course_creator_custom_text_primary_btn' => '#F5F3EC',
			];

			foreach ( $dashboard_default_settings as $key => $value ) {
				ir_set_settings( $key, $value );
			}
		}

		/**
		 * Register default frontend dashboard pattern.
		 *
		 * @since 5.0.0
		 */
		public function register_default_dashboard_pattern() {
			register_block_pattern_category(
				'instructor-role',
				[
					'label' => __( 'Instructor Role', 'wdm_instructor_role' ),
				]
			);

			register_block_pattern(
				'instructor-role/frontend-dashboard-pattern',
				[
					'title'         => __( 'Instructor Frontend Dashboard', 'wdm_instructor_role' ),
					'description'   => sprintf( /* translators: 1.Courses label 2.Quizzes label */ __( 'Empower Instructors to effortlessly create and manage their %1$s and %2$s from Frontend Dashboard', 'wdm_instructor_role' ), ir_get_learndash_label( 'label_to_lower', 'courses' ), ir_get_learndash_label( 'label_to_lower', 'quizzes' ) ),
					'content'       => ir_get_template(
						INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-static-dashboard-block.template.php',
						[],
						true
					),
					'categories'    => [ 'instructor-role' ],
					'keywords'      => [ 'cta', 'dashboard', 'frontend' ],
					'viewportWidth' => 400,
				]
			);
		}

		/**
		 * Get dashboard colors
		 *
		 * @since 5.0.0
		 *
		 * @return object   List of Dashboard colors.
		 */
		public function get_dashboard_colors() {
			$dashboard_default_colors = json_decode(
				ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/dashboard-colors.json',
					[],
					1
				)
			);

			$custom_colors = [
				'name'             => 'Custom',
				'primary'          => ir_get_settings( 'ir_frontend_appearance_custom_primary' ),
				'accent'           => ir_get_settings( 'ir_frontend_appearance_custom_accent' ),
				'background'       => ir_get_settings( 'ir_frontend_appearance_custom_background' ),
				'headings'         => ir_get_settings( 'ir_frontend_appearance_custom_headings' ),
				'text'             => ir_get_settings( 'ir_frontend_appearance_custom_text' ),
				'border'           => ir_get_settings( 'ir_frontend_appearance_custom_border' ),
				'side_bg'          => ir_get_settings( 'ir_frontend_appearance_custom_side_bg' ),
				'side_mt'          => ir_get_settings( 'ir_frontend_appearance_custom_side_mt' ),
				'text_light'       => ir_get_settings( 'ir_frontend_appearance_custom_text_light' ),
				'text_ex_light'    => ir_get_settings( 'ir_frontend_appearance_custom_text_ex_light' ),
				'text_primary_btn' => ir_get_settings( 'ir_frontend_appearance_custom_text_primary_btn' ),
			];

			$dashboard_default_colors->custom = json_decode( json_encode( $custom_colors ), false );

			return $dashboard_default_colors;
		}

		/**
		 * Add dynamic CSS styling for the frontend dashboard.
		 *
		 * @since 5.0.0
		 *
		 * @return string   Custom generated CSS styles.
		 */
		public function get_dynamic_css() {
			$styling_options = [];
			$color_scheme    = ir_get_settings( 'ir_frontend_appearance_color_scheme' );

			switch ( $color_scheme ) {
				case 'wise_pink':
					$styling_options['primary']          = '#E339D8';
					$styling_options['accent']           = '#FFEAFE';
					$styling_options['background']       = '#FFF8FF';
					$styling_options['headings']         = '#3C2E3B';
					$styling_options['text']             = '#696769';
					$styling_options['border']           = '#E5D5E4';
					$styling_options['side_bg']          = '#FFFFFF';
					$styling_options['side_mt']          = '#696769';
					$styling_options['text_light']       = '#938392';
					$styling_options['text_ex_light']    = '#BCADBB';
					$styling_options['text_primary_btn'] = '#ffffff';
					break;

				case 'friendly_mustang':
					$styling_options['primary']          = '#FC9618';
					$styling_options['accent']           = '#FFF5EA';
					$styling_options['background']       = '#FFFCF9';
					$styling_options['headings']         = '#3C352E';
					$styling_options['text']             = '#6B6A69';
					$styling_options['border']           = '#E4DDD3';
					$styling_options['side_bg']          = '#FFFFFF';
					$styling_options['side_mt']          = '#6B6A69';
					$styling_options['text_light']       = '#948D84';
					$styling_options['text_ex_light']    = '#BDB6AD';
					$styling_options['text_primary_btn'] = '#ffffff';
					break;

				case 'natural_green':
					$styling_options['primary']          = '#21CF3D';
					$styling_options['accent']           = '#F1FFF3';
					$styling_options['background']       = '#F9FFFA';
					$styling_options['headings']         = '#354538';
					$styling_options['text']             = '#646564';
					$styling_options['border']           = '#D3E9D7';
					$styling_options['side_bg']          = '#00533A';
					$styling_options['side_mt']          = '#D7FFDD';
					$styling_options['text_light']       = '#879789';
					$styling_options['text_ex_light']    = '#ACBBAF';
					$styling_options['text_primary_btn'] = '#ffffff';
					break;

				case 'royal_purple':
					$styling_options['primary']          = '#954FB6';
					$styling_options['accent']           = '#FBF1FF';
					$styling_options['background']       = '#FDF9FF';
					$styling_options['headings']         = '#3F3444';
					$styling_options['text']             = '#636364';
					$styling_options['border']           = '#E8DEED';
					$styling_options['side_bg']          = '#20003F';
					$styling_options['side_mt']          = '#F3D9FF';
					$styling_options['text_light']       = '#988D9D';
					$styling_options['text_ex_light']    = '#BFB4C5';
					$styling_options['text_primary_btn'] = '#ffffff';
					break;

				case 'custom':
					$styling_options['primary']          = ir_get_settings( 'ir_frontend_appearance_custom_primary' );
					$styling_options['accent']           = ir_get_settings( 'ir_frontend_appearance_custom_accent' );
					$styling_options['background']       = ir_get_settings( 'ir_frontend_appearance_custom_background' );
					$styling_options['headings']         = ir_get_settings( 'ir_frontend_appearance_custom_headings' );
					$styling_options['text']             = ir_get_settings( 'ir_frontend_appearance_custom_text' );
					$styling_options['border']           = ir_get_settings( 'ir_frontend_appearance_custom_border' );
					$styling_options['side_bg']          = ir_get_settings( 'ir_frontend_appearance_custom_side_bg' );
					$styling_options['side_mt']          = ir_get_settings( 'ir_frontend_appearance_custom_side_mt' );
					$styling_options['text_light']       = ir_get_settings( 'ir_frontend_appearance_custom_text_light' );
					$styling_options['text_ex_light']    = ir_get_settings( 'ir_frontend_appearance_custom_text_ex_light' );
					$styling_options['text_primary_btn'] = ir_get_settings( 'ir_frontend_appearance_custom_text_primary_btn' );
					break;

				default:
					$styling_options['primary']          = '#2067FA';
					$styling_options['accent']           = '#F3F9FB';
					$styling_options['background']       = '#FBFCFF';
					$styling_options['headings']         = '#2E353C';
					$styling_options['text']             = '#666666';
					$styling_options['border']           = '#D6D8E7';
					$styling_options['side_bg']          = '#FFFFFF';
					$styling_options['side_mt']          = '#666666';
					$styling_options['text_light']       = '#868E96';
					$styling_options['text_ex_light']    = '#ADB5BD';
					$styling_options['text_primary_btn'] = '#F5F3EC';
					break;
			}

			$styling_options['font_family'] = ir_get_settings( 'ir_frontend_appearance_font_family' );
			$styling_options['font_size']   = ir_get_settings( 'ir_frontend_appearance_font_size' );

			/**
			 * Filter frontend styling options list
			 *
			 * @since 5.0.0
			 *
			 * @param array  $styling_options      List of preset colors.
			 * @param string $color_scheme       Configured color scheme value.
			 */
			$styling_options = apply_filters( 'ir_filter_frontend_styling_options', $styling_options, $color_scheme );

			return ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-dynamic-css.template.php',
				$styling_options,
				1
			);
		}

		/**
		 * Get global tab settings
		 *
		 * @since 5.0.0
		 *
		 * @return array    Array of configured global tab settings for frontend dashboard.
		 */
		public function get_global_tab_settings() {
			$overview_block       = [];
			$courses_block        = [];
			$quizzes_block        = [];
			$products_block       = [];
			$commissions_block    = [];
			$assignments_block    = [];
			$essays_block         = [];
			$quiz_attempts_block  = [];
			$comments_block       = [];
			$course_reports_block = [];
			$groups_block         = [];
			$certificates_block   = [];
			$settings_block       = [];

			$enable_overview_block       = ir_get_settings( 'ir_frontend_overview_block' );
			$enable_courses_block        = ir_get_settings( 'ir_frontend_courses_block' );
			$enable_quizzes_block        = ir_get_settings( 'ir_frontend_quizzes_block' );
			$enable_settings_block       = ir_get_settings( 'ir_frontend_settings_block' );
			$enable_products_block       = ir_get_settings( 'ir_frontend_products_block' );
			$enable_commissions_block    = ir_get_settings( 'ir_frontend_commissions_block' );
			$enable_assignments_block    = ir_get_settings( 'ir_frontend_assignments_block' );
			$enable_essays_block         = ir_get_settings( 'ir_frontend_essays_block' );
			$enable_quiz_attempts_block  = ir_get_settings( 'ir_frontend_quiz_attempts_block' );
			$enable_comments_block       = ir_get_settings( 'ir_frontend_comments_block' );
			$enable_course_reports_block = ir_get_settings( 'ir_frontend_course_reports_block' );
			$enable_groups_block         = ir_get_settings( 'ir_frontend_groups_block' );
			$enable_certificates_block   = ir_get_settings( 'ir_frontend_certificates_block' );

			if ( $enable_overview_block ) {
				$overview_block = [
					'tabLabelsArray'   => __( 'Overview', 'wdm_instructor_role' ),
					'classNameArray'   => 'overview-page',
					'tabIconArray'     => 0,
					'tabIndexArray'    => 0,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_courses_block ) {
				$courses_block = [
					'tabLabelsArray'   => \LearnDash_Custom_Label::get_label( 'courses' ),
					'classNameArray'   => 'wisdm-all-courses',
					'tabIconArray'     => 1,
					'tabIndexArray'    => 1,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_quizzes_block ) {
				$quizzes_block = [
					'tabLabelsArray'   => \LearnDash_Custom_Label::get_label( 'quizzes' ),
					'classNameArray'   => 'wisdm-all-quizzes',
					'tabIconArray'     => 4,
					'tabIndexArray'    => 2,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_products_block ) {
				$products_block = [
					'tabLabelsArray'   => __( 'Products', 'wdm_instructor_role' ),
					'classNameArray'   => 'wisdm-instructor-products',
					'tabIconArray'     => 3,
					'tabIndexArray'    => 3,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_commissions_block ) {
				$commissions_block = [
					'tabLabelsArray'   => __( 'Commissions', 'wdm_instructor_role' ),
					'classNameArray'   => 'wisdm-instructor-commissions',
					'tabIconArray'     => 11,
					'tabIndexArray'    => 4,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_assignments_block ) {
				$assignments_block = [
					'tabLabelsArray'   => __( 'Assignments', 'wdm_instructor_role' ),
					'classNameArray'   => 'ir-assignments',
					'tabIconArray'     => 15,
					'tabIndexArray'    => 5,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_essays_block ) {
				$essays_block = [
					'tabLabelsArray'   => __( 'Essays', 'wdm_instructor_role' ),
					'classNameArray'   => 'submitted-essays',
					'tabIconArray'     => 26,
					'tabIndexArray'    => 6,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_quiz_attempts_block ) {
				$quiz_attempts_block = [
					'tabLabelsArray'   => sprintf( /* translators: Quiz Label. */ __( '%s Attempts', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'quiz' ) ),
					'classNameArray'   => 'wisdm-quiz-attempts',
					'tabIconArray'     => 28,
					'tabIndexArray'    => 7,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_comments_block ) {
				$comments_block = [
					'tabLabelsArray'   => __( 'Comments', 'wdm_instructor_role' ),
					'classNameArray'   => 'wisdm-instructor-comments',
					'tabIconArray'     => 5,
					'tabIndexArray'    => 8,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_course_reports_block ) {
				$course_reports_block = [
					'tabLabelsArray'   => sprintf( /* translators: Course Label. */ __( '%s Reports', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) ),
					'classNameArray'   => 'wisdm-course-reports',
					'tabIconArray'     => 17,
					'tabIndexArray'    => 9,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_groups_block ) {
				$groups_block = [
					'tabLabelsArray'   => \LearnDash_Custom_Label::get_label( 'group' ),
					'classNameArray'   => 'wisdm-groups',
					'tabIconArray'     => 10,
					'tabIndexArray'    => 10,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_certificates_block ) {
				$certificates_block = [
					'tabLabelsArray'   => __( 'Certificates', 'wdm_instructor_role' ),
					'classNameArray'   => 'wisdm-certificates',
					'tabIconArray'     => 29,
					'tabIndexArray'    => 11,
					'tooltipTextArray' => '',
				];
			}

			if ( $enable_settings_block ) {
				$settings_block = [
					'tabLabelsArray'   => __( 'Settings', 'wdm_instructor_role' ),
					'classNameArray'   => 'dashboard-settings',
					'tabIconArray'     => 7,
					'tabIndexArray'    => 12,
					'tooltipTextArray' => '',
				];
			}

			// Configure tab settings.
			$tab_settings = array_merge_recursive(
				$overview_block,
				$courses_block,
				$quizzes_block,
				$products_block,
				$commissions_block,
				$assignments_block,
				$essays_block,
				$quiz_attempts_block,
				$comments_block,
				$course_reports_block,
				$groups_block,
				$certificates_block,
				$settings_block
			);

			// Add create new course button above tab titles.
			$tab_settings['topButton'] = sprintf(
				/* translators: Course Label */
				__( '+ Create New %s', 'wdm_instructor_role' ),
				\LearnDash_Custom_Label::get_label( 'course' )
			);

			$tab_settings['isCourseUrl'] = true;

			return $tab_settings;
		}

		/**
		 * Get global overview settings.
		 *
		 * @since 5.0.0
		 *
		 * @return string
		 */
		public function get_global_overview_settings() {
			$enable_course_tile             = ir_get_settings( 'ir_frontend_overview_course_tile_block' );
			$enable_students_tile           = ir_get_settings( 'ir_frontend_overview_student_tile_block' );
			$enable_submissions_tile        = ir_get_settings( 'ir_frontend_overview_submissions_tile_block' );
			$enable_quiz_attempts           = ir_get_settings( 'ir_frontend_overview_quiz_attempts_tile_block' );
			$enable_earnings_tile           = ir_get_settings( 'ir_frontend_overview_earnings_block' );
			$enable_top_courses_tile        = ir_get_settings( 'ir_frontend_overview_top_courses_block' );
			$enable_course_reports_tile     = ir_get_settings( 'ir_frontend_overview_course_progress_block' );
			$enable_latest_submissions_tile = ir_get_settings( 'ir_frontend_overview_submissions_block' );

			$overview_block_setting  = '';
			$overview_block_sections = [
				'earnings'       => $enable_earnings_tile,
				'courses'        => $enable_top_courses_tile,
				'reports'        => $enable_course_reports_tile,
				'sub'            => $enable_latest_submissions_tile,
				'courses-count'  => $enable_course_tile,
				'students-count' => $enable_students_tile,
				'sub-count'      => $enable_submissions_tile,
				'quiz-count'     => $enable_quiz_attempts,
			];

			// Configure overview block settings.
			foreach ( $overview_block_sections as $key => $enabled ) {
				if ( $enabled ) {
					$overview_block_setting .= " data-$key='true'";
				} else {
					$overview_block_setting .= " data-$key='false'";
				}
			}

			return $overview_block_setting;
		}

		/**
		 * Create New Frontend Dashboard Page.
		 *
		 * @since 5.0.0
		 */
		public function create_new_frontend_dashboard_page() {
			$response = [
				'status'  => 'error',
				'message' => __( 'Some error occurred. Please refresh and try again', 'wdm_instructor_role' ),
			];

			if ( ! current_user_can( 'manage_options' ) ) {
				echo wp_json_encode(
					[
						'status'  => 'error',
						'message' => __( 'Sorry you do not have sufficient privileges to perform this action. Please contact admin.', 'wdm_instructor_role' ),
					]
				);
				wp_die();
			}

			// Verify nonce.
			if ( ! wp_verify_nonce( ir_filter_input( 'nonce', INPUT_GET ), 'ir-create-dashboard-page' ) ) {
				echo wp_json_encode( $response );
				wp_die();
			}

			// Create new dashboard page.
			$new_page_args = [
				'post_title'   => __( 'Instructor Dashboard', 'wdm_instructor_role' ),
				'post_status'  => 'draft',
				'post_author'  => get_current_user_id(),
				'post_type'    => 'page',
				'post_content' => ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-static-dashboard-block.template.php',
					[],
					true
				),
				'meta_input'   => [
					'_wp_page_template' => 'ir-wisdm-dashboard.template.php',
				],
			];

			// If gutenberg disabled, publish page instead of drafting.
			if ( ! ir_is_gutenberg_enabled() ) {
				$new_page_args['post_status'] = 'publish';
			}

			/**
			 * Filter new dashboard page creation post arguments.
			 *
			 * @since 5.0.0
			 *
			 * @param array $new_page_args  Post arguments array.
			 */
			$new_page_args = apply_filters( 'ir_filter_create_new_dashboard_args', $new_page_args );

			// Insert the post into the database.
			$page_id = wp_insert_post( $new_page_args );

			if ( empty( $page_id ) || is_wp_error( $page_id ) ) {
				wp_safe_redirect(
					add_query_arg(
						[
							'page'     => 'instuctor',
							'tab'      => 'ir-dashboard-settings',
							'ir_error' => 1,
						],
						admin_url( 'admin.php' )
					)
				);
				exit();
			} else {
				$page_url = add_query_arg(
					[
						'post'   => $page_id,
						'action' => 'edit',
					],
					admin_url( 'post.php' )
				);

				// If gutenberg disabled, visit the actual page instead of edit page.
				if ( ! ir_is_gutenberg_enabled() ) {
					$page_url = get_permalink( $page_id );
				}

				// Save page ID in options.
				update_option( 'ir_frontend_dashboard_page', $page_id );

				$login_redirect = ir_filter_input( 'login_redirect', INPUT_POST, 'bool' );

				// Set login redirect to frontend dashboard.
				if ( $login_redirect ) {
					ir_set_settings( 'wdm_login_redirect_page', $page_id );
				}

				wp_safe_redirect( $page_url );
				exit();
			}

			wp_die();
		}

		/**
		 * Reset frontend dashboard page meta data.
		 *
		 * @since 5.0.0
		 *
		 * @param int $post_id  ID of the post being trashed.
		 */
		public function reset_frontend_dashboard_page( $post_id ) {
			// Check if post type is page.
			if ( 'page' !== get_post_type( $post_id ) ) {
				return;
			}

			$frontend_dashboard_page = intval( get_option( 'ir_frontend_dashboard_page', false ) );

			// Check if frontend dashboard set and if the same page is trashed.
			if ( false === $frontend_dashboard_page || $post_id !== $frontend_dashboard_page ) {
				return;
			}

			// Clear dashboard meta.
			delete_option( 'ir_frontend_dashboard_page' );
		}

		/**
		 * Save frontend dashboard settings
		 *
		 * @since 5.9.0
		 */
		public function save_frontend_settings() {
			// If not admin then return.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_nonce', $_POST ) || ! wp_verify_nonce( ir_filter_input( 'ir_nonce' ), 'ir_frontend_settings_nonce' ) ) {
				return;
			}

			ir_set_settings( 'wdm_id_ir_dash_pri_menu', 'off' );
			if ( isset( $_POST['wdm_id_ir_dash_pri_menu'] ) ) {
				ir_set_settings( 'wdm_id_ir_dash_pri_menu', 'on' );
			}

			ir_set_settings( 'ir_frontend_dashboard_page', '' );
			if ( isset( $_POST['ir_frontend_dashboard_page'] ) ) {
				update_option( 'ir_frontend_dashboard_page', sanitize_text_field( wp_unslash( $_POST['ir_frontend_dashboard_page'] ) ) );
			}

			ir_set_settings( 'wdm_login_redirect_page', '' );
			ir_set_settings( 'wdm_login_redirect', 'off' );
			if ( isset( $_POST['wdm_login_redirect'] ) ) {
				ir_set_settings( 'wdm_login_redirect', 'on' );
				ir_set_settings( 'wdm_login_redirect_page', get_option( 'ir_frontend_dashboard_page', '' ) );
			}

			if ( ! ir_is_gutenberg_enabled() ) {
				ir_set_settings( 'ir_frontend_overview_block', '' );
				if ( isset( $_POST['ir_frontend_overview_block'] ) ) {
					ir_set_settings( 'ir_frontend_overview_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_courses_block', '' );
				if ( isset( $_POST['ir_frontend_courses_block'] ) ) {
					ir_set_settings( 'ir_frontend_courses_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_quizzes_block', '' );
				if ( isset( $_POST['ir_frontend_quizzes_block'] ) ) {
					ir_set_settings( 'ir_frontend_quizzes_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_settings_block', '' );
				if ( isset( $_POST['ir_frontend_settings_block'] ) ) {
					ir_set_settings( 'ir_frontend_settings_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_products_block', '' );
				if ( isset( $_POST['ir_frontend_products_block'] ) ) {
					ir_set_settings( 'ir_frontend_products_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_commissions_block', '' );
				if ( isset( $_POST['ir_frontend_commissions_block'] ) ) {
					ir_set_settings( 'ir_frontend_commissions_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_assignments_block', '' );
				if ( isset( $_POST['ir_frontend_assignments_block'] ) ) {
					ir_set_settings( 'ir_frontend_assignments_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_essays_block', '' );
				if ( isset( $_POST['ir_frontend_essays_block'] ) ) {
					ir_set_settings( 'ir_frontend_essays_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_quiz_attempts_block', '' );
				if ( isset( $_POST['ir_frontend_quiz_attempts_block'] ) ) {
					ir_set_settings( 'ir_frontend_quiz_attempts_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_comments_block', '' );
				if ( isset( $_POST['ir_frontend_comments_block'] ) ) {
					ir_set_settings( 'ir_frontend_comments_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_course_reports_block', '' );
				if ( isset( $_POST['ir_frontend_course_reports_block'] ) ) {
					ir_set_settings( 'ir_frontend_course_reports_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_groups_block', '' );
				if ( isset( $_POST['ir_frontend_groups_block'] ) ) {
					ir_set_settings( 'ir_frontend_groups_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_certificates_block', '' );
				if ( isset( $_POST['ir_frontend_certificates_block'] ) ) {
					ir_set_settings( 'ir_frontend_certificates_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_overview_course_tile_block', '' );
				if ( isset( $_POST['ir_frontend_overview_course_tile_block'] ) ) {
					ir_set_settings( 'ir_frontend_overview_course_tile_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_overview_student_tile_block', '' );
				if ( isset( $_POST['ir_frontend_overview_student_tile_block'] ) ) {
					ir_set_settings( 'ir_frontend_overview_student_tile_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_overview_submissions_tile_block', '' );
				if ( isset( $_POST['ir_frontend_overview_submissions_tile_block'] ) ) {
					ir_set_settings( 'ir_frontend_overview_submissions_tile_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_overview_quiz_attempts_tile_block', '' );
				if ( isset( $_POST['ir_frontend_overview_quiz_attempts_tile_block'] ) ) {
					ir_set_settings( 'ir_frontend_overview_quiz_attempts_tile_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_overview_course_progress_block', '' );
				if ( isset( $_POST['ir_frontend_overview_course_progress_block'] ) ) {
					ir_set_settings( 'ir_frontend_overview_course_progress_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_overview_top_courses_block', '' );
				if ( isset( $_POST['ir_frontend_overview_top_courses_block'] ) ) {
					ir_set_settings( 'ir_frontend_overview_top_courses_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_overview_earnings_block', '' );
				if ( isset( $_POST['ir_frontend_overview_earnings_block'] ) ) {
					ir_set_settings( 'ir_frontend_overview_earnings_block', 'on' );
				}
				ir_set_settings( 'ir_frontend_overview_submissions_block', '' );
				if ( isset( $_POST['ir_frontend_overview_submissions_block'] ) ) {
					ir_set_settings( 'ir_frontend_overview_submissions_block', 'on' );
				}
				$this->update_dashboard_page_content();
			}
		}
	}
}
