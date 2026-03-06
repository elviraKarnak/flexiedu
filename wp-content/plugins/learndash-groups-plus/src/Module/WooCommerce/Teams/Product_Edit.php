<?php
/**
 * WooCommerce Team Purchase Product Edit functionality.
 *
 * @since 2.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore classname
 */

namespace LearnDash\Groups_Plus\Module\WooCommerce\Teams;

use LearnDash\Groups_Plus\Module\Base as Module_Base;
use LearnDash\Groups_Plus\Module\Module_Interface;

use LearnDash\Core\Utilities\Cast;

use LearnDash\Groups_Plus\lucatume\DI52\App;

use LearnDash\Groups_Plus\Model\WooCommerce\Product\Teams as Teams_Product;
use LearnDash\Groups_Plus\Utility\SharedFunctions;

/**
 * Class Teams Product Edit.
 *
 * @since 2.0.0
 */
class Product_Edit extends Module_Base implements Module_Interface {
	/**
	 * Method to contain function call related to action hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function hook_actions(): void {
		if (
			! SharedFunctions::is_woocommerce_active()
			|| 'no' === get_option( 'enable_wc', 'no' )
		) {
			return;
		}

		add_action( 'init', [ $this, 'load_new_product_type' ], 11 );

		add_action( 'admin_enqueue_scripts', [ $this, 'groups_plus_teams_custom_js' ], 99 );

		// Output Data Panel.
		add_action( 'woocommerce_product_data_panels', [ $this, 'groups_plus_team_courses_options_product_tab_content' ] );

		// Save Post Meta for different valid Product Types that can use our Meta.
		add_action( 'woocommerce_process_product_meta_groups_plus_teams', [ $this, 'save_groups_plus_team_courses_option_field' ] );
		add_action( 'woocommerce_process_product_meta_subscription', [ $this, 'save_groups_plus_team_courses_option_field' ] );
		add_action( 'woocommerce_process_product_meta_variable', [ $this, 'save_groups_plus_team_courses_option_field' ] );
		add_action( 'woocommerce_process_product_meta_variable-subscription', [ $this, 'save_groups_plus_team_courses_option_field' ] );

		// Product variation hooks.
		add_action( 'woocommerce_product_after_variable_attributes', [ $this, 'render_variation_group_selector' ], 10, 3 );
		add_action( 'woocommerce_save_product_variation', [ $this, 'store_variation_related_courses' ], 10, 2 );
	}

	/**
	 * Method to contain function call related to filter hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function hook_filters(): void {
		if (
			! SharedFunctions::is_woocommerce_active()
			|| 'no' === get_option( 'enable_wc', 'no' )
		) {
			return;
		}

		// Set up Product Type.
		add_filter( 'woocommerce_product_class', [ $this, 'woocommerce_product_class' ], 20, 4 );
		add_filter( 'product_type_selector', [ $this, 'add_teams_product' ], 30 );

		// Enable Virtual and Downloadable options.
		add_filter( 'product_type_options', [ $this, 'add_virtual_and_downloadable_checks' ] );

		// Set up Data Panel.
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'custom_product_tabs' ], 30 );
	}

	/**
	 * Load in our Product Type.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function load_new_product_type() {
		new Teams_Product();
	}

	/**
	 * Show pricing fields for Teams products.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function groups_plus_teams_custom_js() {
		global $post;
		$current_screen = get_current_screen();

		if ( ! $post
			|| ! is_a( $post, 'WP_Post' )
			|| $post->post_type !== 'product'
		) {
			return;
		}

		$asset                = require LEARNDASH_GROUPS_PLUS_DIR . 'build/product-edit.asset.php';
		$product_object       = wc_get_product( $post->ID );
		$is_groups_plus_teams = $product_object && App::container()->getVar( 'Teams_WooCommerce_Product_Type' ) === $product_object->get_type();

		wp_enqueue_script(
			'learndash-groups-plus-product-edit',
			LEARNDASH_GROUPS_PLUS_URL . 'build/product-edit.js',
			[ 'jquery' ],
			$asset['version'],
			true
		);

		wp_localize_script(
			'learndash-groups-plus-product-edit',
			'LearnDash_Groups_Plus_Product_Edit',
			[
				'is_groups_plus_teams' => Cast::to_bool( $is_groups_plus_teams ),
				'product_type'         => App::container()->getVar( 'Teams_WooCommerce_Product_Type' ),
			]
		);
	}

	/**
	 * Filter class name used for Teams product.
	 *
	 * @since 2.0.0
	 *
	 * @param string $classname       Class Name for the Product.
	 * @param string $product_type    Product Type.
	 * @param string $variation_type  Variation Type.
	 * @param int    $product_id      Product ID.
	 *
	 * @return string
	 */
	public function woocommerce_product_class( $classname, $product_type, $variation_type, $product_id ) {
		if ( $product_type === App::container()->getVar( 'Teams_WooCommerce_Product_Type' ) ) {
			$classname = Teams_Product::class;
		}

		return $classname;
	}

	/**
	 * Adds the Teams Product Type to WooCommerce.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string> $types  Product Types.
	 *
	 * @return array<string>
	 */
	public function add_teams_product( $types ) {
		$types[ App::container()->getVar( 'Teams_WooCommerce_Product_Type' ) ] = sprintf(
			// translators: Teams.
			__( 'LearnDash %s', 'learndash-groups-plus' ),
			learndash_get_custom_label( 'teams' )
		);

		return $types;
	}

	/**
	 * Enable Virtual and Downloadable options for our Product Type.
	 *
	 * @since 2.0.0
	 *
	 * @param array<array<string>> $options  Product Type Options.
	 *
	 * @return array<array<string>>
	 */
	public function add_virtual_and_downloadable_checks( $options ) {
		if ( isset( $options['virtual'] ) ) {
			$options['virtual'] = wp_parse_args(
				$options['virtual'],
				[
					'wrapper_class' => '',
				]
			);

			$options['virtual']['wrapper_class'] = $options['virtual']['wrapper_class'] . ' show_if_groups_plus_teams';
		}

		if ( isset( $options['downloadable'] ) ) {
			$options['downloadable'] = wp_parse_args(
				$options['downloadable'],
				[
					'wrapper_class' => '',
				]
			);

			$options['downloadable']['wrapper_class'] = $options['downloadable']['wrapper_class'] . ' show_if_groups_plus_teams';
		}

		return $options;
	}

	/**
	 * Add a custom product tab.
	 *
	 * @since 2.0.0
	 *
	 * @param array<int|string, array{ label: string, target: string, class: array<string> }> $tabs  Product Tabs.
	 *
	 * @return non-empty-array<int|string, array{ label: string, target: string, class: array<string> }>
	 */
	public function custom_product_tabs( $tabs ) {
		$tabs[ App::container()->getVar( 'Teams_WooCommerce_Product_Type' ) ] = [
			'label'  => sprintf(
				// translators: LearnDash Team label courses label.
				__( 'LearnDash %1$s %2$s', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' ),
				learndash_get_custom_label_lower( 'courses' )
			),
			'target' => 'groups_plus_team_courses_options',
			'class'  => [ 'show_if_groups_plus_teams', 'show_if_variable', 'show_if_subscription' ],
		];

		return $tabs;
	}

	/**
	 * Contents of the courses options product tab.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function groups_plus_team_courses_options_product_tab_content() {
		global $post, $woocommerce;
		$courses = $this->list_courses();

		$values = get_post_meta( $post->ID, SharedFunctions::$groups_plus_team_courses_meta_field, true );
		if ( ! $values ) {
			$values = [ 0 ];
		}

		$checked = checked(
			get_post_meta(
				$post->ID,
				SharedFunctions::$is_team_purchase_enable,
				true
			),
			'1',
			false
		);

		$custom_attributes = [];
		if ( $checked ) {
			$custom_attributes['checked'] = true;
		}

		wp_nonce_field( 'learndash_groups_plus_save_teams_product', 'groups_plus_teams_product_nonce' );

		?>
		<div id='groups_plus_team_courses_options' class='panel woocommerce_options_panel'>
			<div class='options_group show_if_subscription show_if_variable'>
				<?php
					woocommerce_wp_text_input(
						[
							'type'              => 'checkbox',
							'class'             => '',
							'id'                => SharedFunctions::$is_team_purchase_enable,
							'name'              => SharedFunctions::$is_team_purchase_enable,
							'value'             => '1',
							'label'             => sprintf(
								// translators: team.
								__( 'Enable %s purchase', 'learndash-groups-plus' ),
								learndash_get_custom_label_lower( 'team' )
							),
							'description'       => sprintf(
								// translators: team.
								__( 'check to enable %s purchase', 'learndash-groups-plus' ),
								learndash_get_custom_label_lower( 'team' )
							),
							'custom_attributes' => $custom_attributes,
						]
					);
				?>
			</div>
			<div class='options_group show_if_groups_plus_teams show_if_subscription'>
				<?php
					// TODO: Replace with a lazy load implementation.
					woocommerce_wp_select(
						[
							'id'                => SharedFunctions::$groups_plus_team_courses_meta_field,
							'name'              => '_groups_plus_team_courses[]',
							'label'             => \LearnDash_Custom_Label::get_label( 'course' ),
							'description'       => sprintf(
								// translators: Course.
								__( 'Select LearnDash %s.', 'learndash-groups-plus' ),
								\LearnDash_Custom_Label::get_label( 'course' )
							),
							'options'           => $courses,
							'value'             => $values,
							'custom_attributes' => [
								'multiple' => true,
							],
						]
					);
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Returns a list of all Courses on the site.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string>
	 */
	private static function list_courses() {
		$posts   = get_posts(
			[
				'post_type'      => 'sfwd-courses',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'fields'         => 'ids',
			]
		);
		$courses = [];

		foreach ( $posts as $id ) {
			$course_price = wc_price(
				Cast::to_float(
					get_post_meta(
						$id,
						'_course_price',
						true
					)
				)
			);

			$courses[ $id ] = '#' . esc_html( Cast::to_string( $id ) ) . ' - ' . get_the_title( $id ) . ' ' . wp_strip_all_tags(
				sprintf(
					// translators: The Course per-Seat Price.
					__( '(%s/seat)', 'learndash-groups-plus' ),
					$course_price
				)
			);
		}

		return $courses;
	}

	/**
	 * Variation fields for our Product Type.
	 *
	 * @since 2.0.0
	 *
	 * @param int           $variation_index  Variation index.
	 * @param array<string> $variation_data   Variation data. Deprecated with WooCommerce 4.4.0.
	 * @param \WP_Post      $variation        Variation post.
	 *
	 * @return void
	 */
	public function render_variation_group_selector( $variation_index, $variation_data, $variation ) {
		$is_team_purchase_enable = get_post_meta( $variation->post_parent, SharedFunctions::$is_team_purchase_enable, true );

		if ( $is_team_purchase_enable !== '1' ) {
			return;
		}

		$courses = self::list_courses();

		wp_nonce_field( 'learndash_groups_plus_save_teams_product', 'groups_plus_teams_product_nonce' );

		?>

		<div class="form-row form-row-full">

			<?php
			$seat_value = Cast::to_int(
				get_post_meta(
					$variation->ID,
					SharedFunctions::$variable_product_allow_seats,
					true
				)
			);

			woocommerce_wp_text_input(
				[
					'type'        => 'number',
					'id'          => SharedFunctions::$variable_product_allow_seats,
					'name'        => '_variable_product_allow_seats[' . $variation_index . ']',
					'label'       => __( 'Allow seats', 'learndash-groups-plus' ),
					'description' => __( 'How many seats you wanted to allow.', 'learndash-groups-plus' ),
					'value'       => $seat_value,
				]
			);

			$values = get_post_meta( $variation->ID, SharedFunctions::$groups_plus_team_courses_meta_field, true );

			// TODO: Replace with a lazy load implementation.
			woocommerce_wp_select(
				[
					'id'                => esc_attr( SharedFunctions::$groups_plus_team_courses_meta_field ),
					'name'              => '_groups_plus_team_courses[' . esc_attr( Cast::to_string( $variation_index ) ) . '][]',
					'label'             => esc_html( \LearnDash_Custom_Label::get_label( 'course' ) ),
					'options'           => $courses,
					'value'             => $values,
					'custom_attributes' => [
						'multiple' => true,
					],
				]
			);

			?>

		</div>

		<?php
	}

	/**
	 * Update Post Meta for Variations for our Product Type.
	 *
	 * @since 2.0.0
	 *
	 * @param int $variation_id     Variation ID.
	 * @param int $variation_index  Variation Index.
	 *
	 * @return void
	 */
	public function store_variation_related_courses( $variation_id, $variation_index ) {
		if (
			! SharedFunctions::filter_has_var( 'groups_plus_teams_product_nonce', INPUT_POST )
			|| ! wp_verify_nonce(
				Cast::to_string(
					SharedFunctions::filter_input(
						'groups_plus_teams_product_nonce',
						INPUT_POST
					)
				),
				'learndash_groups_plus_save_teams_product'
			)
		) {
			return;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$post_data = wp_unslash( $_POST );

		if (
			SharedFunctions::filter_has_var( SharedFunctions::$groups_plus_team_courses_meta_field, INPUT_POST )
			&& ! empty( SharedFunctions::filter_input_array( SharedFunctions::$groups_plus_team_courses_meta_field, INPUT_POST ) )
		) {
			update_post_meta( $variation_id, SharedFunctions::$groups_plus_team_courses_meta_field, SharedFunctions::filter_input_array( SharedFunctions::$groups_plus_team_courses_meta_field, INPUT_POST )[ $variation_index ] );
		} else {
			delete_post_meta( $variation_id, SharedFunctions::$groups_plus_team_courses_meta_field );
		}

		if ( SharedFunctions::filter_has_var( SharedFunctions::$variable_product_allow_seats, INPUT_POST ) ) {
			update_post_meta( $variation_id, SharedFunctions::$variable_product_allow_seats, $post_data['_variable_product_allow_seats'][ $variation_index ] );
		} else {
			delete_post_meta( $variation_id, SharedFunctions::$variable_product_allow_seats );
		}
	}

	/**
	 * Save Post Meta for non-Variations for our Product Type.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id  Current Post ID.
	 *
	 * @return void
	 */
	public function save_groups_plus_team_courses_option_field( $post_id ) {
		if (
			! SharedFunctions::filter_has_var( 'groups_plus_teams_product_nonce', INPUT_POST )
			|| ! wp_verify_nonce(
				Cast::to_string(
					SharedFunctions::filter_input(
						'groups_plus_teams_product_nonce',
						INPUT_POST
					)
				),
				'learndash_groups_plus_save_teams_product'
			)
		) {
			return;
		}

		$post_data = wp_unslash( $_POST );

		if ( isset( $post_data[ SharedFunctions::$is_team_purchase_enable ] ) ) {
			update_post_meta( $post_id, SharedFunctions::$is_team_purchase_enable, $post_data[ SharedFunctions::$is_team_purchase_enable ] );
		} else {
			delete_post_meta( $post_id, SharedFunctions::$is_team_purchase_enable );
		}

		if ( isset( $post_data[ SharedFunctions::$groups_plus_team_courses_meta_field ] ) ) {
			update_post_meta( $post_id, SharedFunctions::$groups_plus_team_courses_meta_field, $post_data[ SharedFunctions::$groups_plus_team_courses_meta_field ] );
		} else {
			delete_post_meta( $post_id, SharedFunctions::$groups_plus_team_courses_meta_field );
		}
	}
}
