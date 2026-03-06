<?php
/**
 * Course Header Template
 *
 * @since 4.0
 *
 * @var array   $settings_count
 * @var string  $current_setting_count
 * @var float   $step_width
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<section class="ir-course-progress ir-course-header">
	<div class="ir-flex">
		<div class="irb-dots" data-active-dot="<?php echo esc_attr( $current_setting_count ); ?>">
			<?php for ( $i = 0; $i < $settings_count; $i++ ) : ?>
				<span class="<?php echo ( $i < $current_setting_count ) ? 'ir-active-dot' : ''; ?>"></span>
			<?php endfor; ?>
			<div class='ir-progress-bar' style="<?php echo ( 0 < $current_setting_count ) ? 'width: ' . esc_attr( ( ( $current_setting_count - 1 ) * $step_width ) ) . '%' : ''; ?>"></div>
		</div>
		<div class="irb-progress-text">
			<?php
			echo esc_html(
				sprintf(
					// translators: 1: Current setting count 2: Total settings count.
					__( '%1$d out of %2$d settings', 'wdm_instructor_role' ),
					$current_setting_count,
					$settings_count
				)
			);
			?>
		</div>
	</div>
	<div class="ir-course-footer">
		<button id="ir-save-and-continue" class="irb-btn"><?php esc_html_e( 'Continue', 'wdm_instructor_role' ); ?></button>
	</div>
</section>
