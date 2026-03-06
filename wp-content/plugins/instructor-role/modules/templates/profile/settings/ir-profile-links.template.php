<?php
/**
 * Instructor Profile links Template for user profile page
 *
 * @since 4.2.1
 *
 * @var int $user_id    User ID of a user
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
if ( wdm_is_instructor() ) {
	?>
	<div class="button button-primary ir-nav-button">
	<?php $author_link = ir_get_instructor_profile_link( $user_id ); ?>
	<?php
	$ir_enable_profile_links = get_option( 'ir_enable_profile_links', false );
	if ( 'on' == $ir_enable_profile_links ) {
		?>
									<a style="color:white;text-decoration:none;" href="<?php echo esc_url( $author_link ); ?>" target="_blank" rel="noopener noreferrer">
		<?php esc_html_e( 'View Instructor\'s Profile', 'wdm_instructor_role' ); ?>
									</a>
								<?php
	} else {
			$profile_link_tooltip = esc_html__( 'Instructor Profile is currently disabled by the admin', 'wdm_instructor_role' );
		?>
									<s><a style="color:white;text-decoration:none;" href="#"><span title="<?php echo esc_attr( $profile_link_tooltip ); ?>"><?php esc_html_e( 'View Instructor\'s Profile', 'wdm_instructor_role' ); ?></span></a></s>
									<?php
	}
	?>
	</div>
	<?php
} elseif ( wdm_is_instructor( $user_id ) ) {
	?>
<br>
<div class="button button-primary ir-nav-button">
	<?php $author_link = ir_get_instructor_profile_link( $user_id ); ?>
	<?php
	$ir_enable_profile_links = get_option( 'ir_enable_profile_links', false );
	if ( 'on' == $ir_enable_profile_links ) {
		?>
									<a style="color:white;text-decoration:none;" href="<?php echo esc_url( $author_link ); ?>" target="_blank" rel="noopener noreferrer">
		<?php esc_html_e( 'View Instructor\'s Profile', 'wdm_instructor_role' ); ?>
									</a>
								<?php
	} else {
			$profile_link_tooltip = esc_html__( 'Enable the Instructor Profile Setting from here to View the Instructor\'s Profile. (here linked to the Profile Setting on the site)', 'wdm_instructor_role' );
		?>
									<s><a style="color:white;text-decoration:none;" href="/wp-admin/admin.php?page=instuctor&tab=ir-profile"><span title="<?php echo esc_attr( $profile_link_tooltip ); ?>"><?php esc_html_e( 'View Instructor\'s Profile', 'wdm_instructor_role' ); ?></span></a></s>
									<?php
	}
	?>
</div>
<div class="button button-primary ir-nav-button">
	<a style="color:white;text-decoration:none;" href="
	<?php
	echo esc_url( home_url( 'wp-login.php?action=wdm_ir_switch_user&user_id=' . sanitize_text_field( $_GET['user_id'] ) ) );
	?>
	" target="_blank" rel="noopener noreferrer">
		<?php _e( 'View Instructor\'s Dashboard', 'instructor-registration' ); ?>
	</a>
</div>
<br>
	<?php
}
