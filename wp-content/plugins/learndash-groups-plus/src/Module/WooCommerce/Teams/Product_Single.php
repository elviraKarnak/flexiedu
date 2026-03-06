<?php
/**
 * WooCommerce Team Purchase Product Single functionality.
 *
 * @since 2.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore nopaging numberposts
 */

namespace LearnDash\Groups_Plus\Module\WooCommerce\Teams;

use Exception;
use WP_Post;
use WC_Product;
use WP_User;

use LearnDash\Core\Utilities\Cast;

use LearnDash\Groups_Plus\Module\Base as Module_Base;
use LearnDash\Groups_Plus\Module\Module_Interface;

use LearnDash\Groups_Plus\lucatume\DI52\App;

use LearnDash\Groups_Plus\Utility\SharedFunctions;

/**
 * Class Teams Product Single.
 *
 * @since 2.0.0
 */
class Product_Single extends Module_Base implements Module_Interface {
	/**
	 * Whether to show the Team Name textbox.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	private $show_team_name_field = true;

	/**
	 * The hidden Team ID that is submitted if the Team Name textbox isn't shown.
	 *
	 * @since 2.0.0
	 *
	 * @var int
	 */
	private $hidden_team_id = 0;

	/**
	 * The Parent Group IDs belonging to the logged in user.
	 *
	 * @since 2.0.0
	 *
	 * @var array<int>
	 */
	private $parent_group_ids;

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

		add_action( 'wp', [ $this, 'setup' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'woocommerce_groups_plus_teams_add_to_cart', [ $this, 'output_add_to_cart_template' ], 30 );
		add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'output_add_to_cart_form_elements' ] );
	}

	/**
	 * Method to contain function call related to AJAX hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function hook_ajax(): void {
		if (
			! SharedFunctions::is_woocommerce_active()
			|| 'no' === get_option( 'enable_wc', 'no' )
		) {
			return;
		}

		add_action( 'wp_ajax_get_courses_of_variation_product', [ $this, 'get_courses_of_variation_product' ] );
		add_action( 'wp_ajax_nopriv_get_courses_of_variation_product', [ $this, 'get_courses_of_variation_product' ] );
	}

	/**
	 * Sets up some global class members so that we don't need to query them multiple times.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function setup(): void {
		if ( ! is_singular( 'product' ) ) {
			return;
		}

		global $post;

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$product = wc_get_product( $post->ID );

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$form_data = $this->get_add_to_cart_form_data( $product->get_id() );

		$this->show_team_name_field = $form_data['show_team_name_field'];
		$this->hidden_team_id       = $form_data['hidden_team_id'];
		$this->parent_group_ids     = $form_data['parent_group_ids'];
	}

	/**
	 * Enqueues our frontend assets for Product Single.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		if ( ! is_singular( 'product' ) ) {
			return;
		}

		global $post;

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$product = wc_get_product( $post->ID );

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		if ( ! Add_To_Cart::product_can_be_used_for_teams( $product->get_id() ) ) {
			return;
		}

		$asset = require LEARNDASH_GROUPS_PLUS_DIR . 'build/product-single.asset.php';

		$version = false;
		if ( ! empty( $asset['version'] ) ) {
			$version = $asset['version'];
		}

		wp_enqueue_script(
			'learndash-groups-plus-product-single',
			LEARNDASH_GROUPS_PLUS_URL . 'build/product-single.js',
			[ 'jquery' ],
			$version,
			true
		);

		wp_localize_script(
			'learndash-groups-plus-product-single',
			'LearnDash_Groups_Plus_Product_Single',
			[
				'currency_symbol' => get_woocommerce_currency_symbol(),
				'product_price'   => $product->get_price(),
				'is_new_team'     => $this->show_team_name_field,
			]
		);
	}

	/**
	 * Output Simple Product's Add To Cart template when trying to load one for the Teams Product Type.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function output_add_to_cart_template() {
		wc_get_template( 'single-product/add-to-cart/simple.php' );
	}

	/**
	 * Add form elements to the Product Single Template above the add to cart button.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function output_add_to_cart_form_elements() {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		if ( ! Add_To_Cart::product_can_be_used_for_teams( $product->get_id() ) ) {
			return;
		}

		$hide_exclude_from_publicly_sold_seats_checkbox = Cast::to_bool(
			get_site_option( 'hide_exclude_from_publicly_sold_seats_checkbox' )
		);

		wp_nonce_field( 'learndash_groups_plus_add_teams_product', 'groups_plus_teams_product_nonce' );
		?>

		<table class="variations learndash-groups-plus-team-product-variations" cellspacing="0">
			<tbody>
				<?php
				if ( $this->show_team_name_field ) {
					$this->show_team_name_field_field();
					$this->show_exclude_from_publicly_sold_seats_field( $hide_exclude_from_publicly_sold_seats_checkbox );
				} elseif ( $this->hidden_team_id !== 0 ) {
					$this->show_hidden_team_id_field();
				} else {
					$this->show_team_dropdown_field();
				}

				if (
					$product->is_type( 'variable' )
					|| $product->is_type( 'variable-subscription' )
				) {
					$this->show_variable_product_fields();
				} else {
					$this->show_included_courses( $product->get_id() );
				}
				?>
			</tbody>
		</table>

		<?php
		if (
			$product->is_type(
				Cast::to_string(
					App::container()->getVar( 'Teams_WooCommerce_Product_Type' )
				)
			)
			|| (
				SharedFunctions::is_woocommerce_subscription_active()
				&& $product->is_type( 'subscription' )
			)
		) {
			$this->show_total_price( $product );
		}
	}

	/**
	 * Outputs the Team Name Textbox field.
	 *
	 * TODO: Split into frontend template files.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function show_team_name_field_field(): void {
		?>
		<tr>
			<td class="label">
				<label for="team_name">
					<?php
					echo esc_html(
						sprintf(
							// translators: team.
							__( 'Enter %s name', 'learndash-groups-plus' ),
							learndash_get_custom_label_lower( 'team' )
						)
					);
					?>
				</label>
			</td>
			<td class="value">
				<input type="text" name="team_name" id="team_name" required/>
			</td>
		</tr>
		<?php
	}

	/**
	 * Outputs the Exclude from Publicly Sold Seats field.
	 * Even when the field is hidden, we still need to submit the value.
	 *
	 * TODO: Split into frontend template files.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $hide  Whether to hide the field.
	 *
	 * @return void
	 */
	protected function show_exclude_from_publicly_sold_seats_field( bool $hide = false ): void {
		?>
		<tr style="<?php echo ( $hide === true ? 'display: none;' : 'display: block;' ); ?>">
			<td class="label">
				<label for="exclude_from_individual_seat_purchase">
					<?php
					echo esc_html(
						/**
						 * This filter is documented in src/Module/WooCommerce/Organizations.php
						 */
						apply_filters(
							'change_label_of_exclude_from_publicly_sold_seats', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- This is an existing filter that is used in other areas
							__( 'Exclude from publicly sold seats:', 'learndash-groups-plus' )
						)
					);
					?>
				</label>
				<input type="checkbox" class="" name="exclude_from_individual_seat_purchase" value="1" checked/>
			</td>
		</tr>
		<?php
	}

	/**
	 * Outputs a hidden field with the existing Team ID that will be used for the purchase.
	 *
	 * TODO: Split into frontend template files.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function show_hidden_team_id_field(): void {
		?>
		<tr>
			<td>
				<input
					type="hidden"
					name="choose_team"
					value="<?php echo esc_attr( Cast::to_string( $this->hidden_team_id ) ); ?>"
				/>
			</td>
		</tr>
		<?php
	}

	/**
	 * Outputs a dropdown of Teams that can be used for the purchase.
	 *
	 * TODO: Split into frontend template files.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function show_team_dropdown_field(): void {
		?>
		<tr>
			<td class="label">
				<label for="choose_team">
					<?php
					echo esc_html(
						sprintf(
							// translators: team.
							__( 'Choose %s name:', 'learndash-groups-plus' ),
							learndash_get_custom_label_lower( 'team' )
						)
					);
					?>
				</label>
			</td>
			<td class="value">
				<select name="choose_team" id="choose_team" required>
					<?php foreach ( $this->parent_group_ids as $parent_group_id ) : ?>
						<option
							value="<?php echo esc_attr( Cast::to_string( $parent_group_id ) ); ?>"
							class="attached enabled"
						>
							<?php echo esc_html( get_the_title( $parent_group_id ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php
	}

	/**
	 * Outputs a list of Courses included for the chosen Variation, populated via Ajax.
	 *
	 * TODO: Split into frontend template files.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function show_variable_product_fields(): void {
		?>
		<tr>
			<td class="label">
				<label for="">
					<?php
					echo esc_html(
						sprintf(
							// translators: Team courses package.
							__( '%1$s %2$s package:', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'team' ),
							learndash_get_custom_label_lower( 'courses' )
						)
					)
					?>
				</label>
			</td>
			<td class="value">
				<ul id="organization_courses_list" class="team_courses_list variable_product">
				</ul>
			</td>
		</tr>
		<?php
	}

	/**
	 * Outputs the available Courses for the Product and their prices.
	 *
	 * TODO: Split into frontend template files.
	 *
	 * @since 2.0.0
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return void
	 */
	protected function show_included_courses( int $product_id ): void {
		$product_courses = $this->get_product_team_courses_data( $product_id );
		?>
		<tr>
			<td class="label">
				<label for="">
					<?php
					echo esc_html(
						sprintf(
							// translators: Select team courses.
							__( 'Select %1$s %2$s:', 'learndash-groups-plus' ),
							learndash_get_custom_label_lower( 'team' ),
							learndash_get_custom_label_lower( 'courses' )
						)
					)
					?>
				</label>
			</td>
			<td class="value">
				<input
					type="text"
					id="courses_search_box"
					placeholder="<?php echo esc_attr( __( 'Search for names...', 'learndash-groups-plus' ) ); ?>"
					title="Type in a name"
				/>
				<ul id="team_courses_list">
					<?php foreach ( $product_courses as $course_data ) : ?>
						<?php
						$course_price_without_currency = wc_trim_zeros( $course_data['course_price'] );
						$course_price_with_currency    = wc_price( $course_data['course_price'] );
						$course_id                     = Cast::to_string( $course_data['course_id'] );
						?>
						<li>
							<input
								type="checkbox"
								name="my_team_courses[<?php echo esc_attr( $course_id ); ?>]"
								value="<?php echo esc_attr( $course_price_without_currency ); ?>"
								class="learndash-groups-plus-team-product-qty"
								id="groups_plus_team_course_<?php echo esc_attr( $course_id ); ?>"
							/>
							<label for="groups_plus_team_course_<?php echo esc_attr( $course_id ); ?>">
								<?php echo esc_html( $course_data['course_name'] ); ?> <?php
								echo wp_kses_post(
									sprintf(
										// translators: Course Price per-Seat.
										__( '(%s/seat)', 'learndash-groups-plus' ),
										$course_price_with_currency
									)
								);
								?>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
			</td>
		</tr>
		<?php
	}

	/**
	 * Outputs the total calculated price, updated via JavaScript.
	 *
	 * TODO: Split into frontend template files.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Product $product Product Object.
	 *
	 * @return void
	 */
	protected function show_total_price( WC_Product $product ): void {
		?>
		<div class="btn-learndash-groups-plus-team-price">
			<?php echo esc_html( __( 'Total price so far:', 'learndash-groups-plus' ) ); ?>
			<?php echo wp_kses_post( $product->get_price_html() ); ?>
		</div>
		<?php
	}

	/**
	 * Determines what elements should be shown or hidden based on the Product ID.
	 * Also returns any necessary information to build out those fields.
	 *
	 * @since 2.0.0
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return array{show_team_name_field: bool, hidden_team_id: int, parent_group_ids: array<int>}
	 */
	protected function get_add_to_cart_form_data( int $product_id ): array {
		$result = [
			'show_team_name_field' => true,
			'hidden_team_id'       => 0,
			'parent_group_ids'     => [],
		];

		if ( ! is_user_logged_in() ) {
			return $result;
		}

		$user = wp_get_current_user();

		if ( ! in_array( 'group_leader', (array) $user->roles, true ) ) {
			return $result;
		}

		$result['parent_group_ids'] = $this->get_parent_group_ids_for_user( $user );

		if ( empty( $result['parent_group_ids'] ) ) {
			return $result;
		}

		$result['show_team_name_field'] = false;
		$result['hidden_team_id']       = $result['parent_group_ids'][0] ?? 0;

		return $result;
	}

	/**
	 * Gets the Parent Group IDs accessible to a given User.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_User $user User Object.
	 *
	 * @return array<int>
	 */
	protected function get_parent_group_ids_for_user( WP_User $user ): array {
		$administrators_group_ids = learndash_get_administrators_group_ids( $user->ID );

		if ( empty( $administrators_group_ids ) ) {
			return [];
		}

		$orderby = get_site_option( 'group_orderby' );
		$orderby = ! empty( $orderby ) ? $orderby : 'title';
		$order   = get_site_option( 'group_order' );
		$order   = ! empty( $order ) ? $order : 'ASC';

		return get_posts(
			[
				'numberposts' => -1,
				'post_type'   => 'groups',
				'post__in'    => $administrators_group_ids,
				'orderby'     => Cast::to_string( $orderby ),
				'order'       => Cast::to_string( $order ),
				'post_parent' => 0,
				'nopaging'    => true,
				'fields'      => 'ids',
				's'           => '-[FAMILY]',
				'meta_query'  => [
					'relation' => 'AND',
					[
						'key'   => SharedFunctions::$is_non_organization_team,
						'value' => '1',
					],
				],
			]
		);
	}

	/**
	 * Returns an array of data for Courses associated with the Team Product.
	 *
	 * TODO: Optimize to avoid looping directly through the Courses and grabbing their data.
	 *
	 * @since 2.0.0
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return array<array{course_id: int, course_name: string, course_price: float}>
	 */
	protected function get_product_team_courses_data( int $product_id ): array {
		$groups_plus_team_courses = get_post_meta(
			$product_id,
			SharedFunctions::$groups_plus_team_courses_meta_field,
			true
		);

		if (
			empty( $groups_plus_team_courses )
			|| ! is_array( $groups_plus_team_courses )
		) {
			$groups_plus_team_courses = [];
		}

		$result = [];
		foreach ( $groups_plus_team_courses as $groups_plus_team_course_id ) {
			$groups_plus_team_course_id = Cast::to_int( $groups_plus_team_course_id );
			$post                       = get_post( $groups_plus_team_course_id );

			if ( ! empty( $post ) ) {
				$course_name  = get_the_title( $groups_plus_team_course_id );
				$course_price = get_post_meta( $groups_plus_team_course_id, '_course_price', true );

				if ( empty( $course_price ) ) {
					$course_price = 0;
				}

				$result[] = [
					'course_id'    => $groups_plus_team_course_id,
					'course_name'  => $course_name,
					'course_price' => Cast::to_float( $course_price ),
				];
			}
		}

		usort(
			$result,
			function( $a, $b ) {
				return strcmp( $a['course_name'], $b['course_name'] );
			}
		);

		return $result;
	}

	/**
	 * Retrieves all Courses assigned for a Variation for use in a admin-ajax.php call.
	 *
	 * @since 2.0.0
	 *
	 * @throws Exception On error.
	 *
	 * @return void
	 */
	public function get_courses_of_variation_product() {
		/**
		 * The JS that calls this Ajax callback is reused across all the other Product Types as well.
		 *
		 * Once that is refactored, the nonce check here should be fixed.
		 *
		 * TODO: Add nonce check.
		 */
		$post_data = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- This Ajax callback is used elsewhere in the plugin.

		if (
			empty( $post_data['data'] )
			|| empty( $post_data['data']['variation_id'] )
		) {
			wp_send_json_error(
				[
					'errors' => __( 'Malformed request data.', 'learndash-groups-plus' ),
				]
			);
		}

		$variation_id = Cast::to_int(
			sanitize_text_field(
				$post_data['data']['variation_id']
			)
		);

		$product = wc_get_product( $variation_id );

		if ( ! $product instanceof WC_Product ) {
			wp_send_json_error(
				[
					'errors' => __( 'Error retrieving variation product.', 'learndash-groups-plus' ),
				]
			);
		}

		if ( ! Add_To_Cart::product_can_be_used_for_teams( $product->get_parent_id() ) ) {
			// Return rather than send JSON to ensure other usages of this Action can run successfully.
			return;
		}

		$groups_plus_team_courses = get_post_meta( $variation_id, SharedFunctions::$groups_plus_team_courses_meta_field, true );

		if (
			empty( $groups_plus_team_courses )
			|| ! is_array( $groups_plus_team_courses )
		) {
			$groups_plus_team_courses = [];
		}

		$html_string = '';

		foreach ( $groups_plus_team_courses as $groups_plus_team_course ) {
			$post = get_post( $groups_plus_team_course );
			if ( ! empty( $post ) ) {
				$html_string .= '<li>' . get_the_title( $groups_plus_team_course ) . '</li>';
			}
		}

		wp_send_json_success(
			[
				'html' => $html_string,
			]
		);
	}
}
