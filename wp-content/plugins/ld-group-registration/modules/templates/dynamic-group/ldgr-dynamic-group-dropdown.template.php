<?php
/**
 * Template: LDGR Dynamic group dropdown.
 *
 * @since 4.3.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<br>
<br>
<div class="ldgr_dynamic_options" <?php echo ( 'individual' == $default_option ) ? 'style="display:none;"' : ''; ?>>
	<label for="ldgr_action">
		<strong><?php esc_html_e( 'I want to ', 'wdm_ld_group' ); ?></strong>
	</label>
	<select name="ldgr_dynamic_option" class="ldgr_dynamic_options_select">
		<option value='create_new'>
		<?php
		printf(
			/* translators: Group label */
			esc_html__( 'Create new %s', 'wdm_ld_group' ),
			$group_label
		);
		?>
		</option>
		<?php if ( $is_enabled_dynamic_course ) { ?>
		<option value='add_courses'>
			<?php
			printf(
				/* translators: Group label */
				esc_html__( 'Add %1$s to existing %2$s', 'wdm_ld_group' ),
				$course_label,
				$group_label
			);
			?>
		</option>
		<?php } ?>
		<option value='increase_seats'>
		<?php
		printf(
			/* translators: Group label */
			esc_html__( 'Increase seats to existing %s', 'wdm_ld_group' ),
			$lower_group_label
		);
		?>
		</option>
	</select>
</div>

<div class="ldgr_dynamic_values" style="display:none;">
	<label for="ldgr_action">
		<strong>
		<?php
		echo esc_html(
			sprintf(
			/* translators: Group label. */
				_x( '%s Name', 'Additional Group Options', 'wdm_ld_group' ),
				$group_label
			)
		);
		?>
			</strong>
	</label>
	<select name="ldgr_dynamic_value"
		class="ldgr_dynamic_values_select"
		data-nonce="<?php echo esc_attr( $ldgr_nonce ); ?>">
		<option value=''>
			<?php
			printf(
				/* translators: Group label */
				esc_html__( 'Select %s Name ', 'wdm_ld_group' ),
				$group_label
			);
			?>
		</option>
		<?php
		if ( ! empty( $group_ids ) ) {
			foreach ( $group_ids as $key => $value ) {
				if ( ! defined( 'LEARNDASH_VERSION' ) ) {
					return;
				}
				// Only include published/active groups and skip drafted/inactive groups.
				if ( false === get_post_status( $value ) || 'publish' !== get_post_status( $value ) ) {
					continue;
				}
				$courses    = learndash_group_enrolled_courses( $value );
				$user_limit = get_post_meta( $value, 'wdm_group_total_users_limit_' . $value, true );
				?>
				<option
				value='<?php echo esc_attr( $value ); ?>'
				data-courses='<?php echo esc_attr( htmlspecialchars( json_encode( $courses ), ENT_QUOTES, 'UTF-8' ) ); ?>'
				data-users='<?php echo esc_attr( htmlspecialchars( $user_limit, ENT_QUOTES, 'UTF-8' ) ); ?>'>
					<?php echo esc_html( get_the_title( $value ) ); ?></option>
				<?php
			}
		}
		?>
	</select>
</div>
