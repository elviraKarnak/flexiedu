<?php
/**
 * Template for the Edit Panel User Grade
 *
 * @since 2.0.0
 * @updated 4.2.0
 * @version 4.3.2
 *
 * @var integer         $user_id
 * @var integer         $gradebook_id
 * @var LD_GB_UserGrade $user_grade
 * @var string          $grade_format
 */

defined( 'ABSPATH' ) || die();

$user = new WP_User( $user_id );

?>

<div class="ld-gb-frontend-gradebook-edit-panel-user-grade">

	<h3>
		<?php
		printf(
			/* translators: %s is the User's Display Name */
			_x( 'Grade for %s', 'Frontend Gradebook Edit Panel Title', 'learndash-gradebook' ),
			$user->display_name
		)
		?>

		<?php if ( $user_grade->get_user_grade() ) : ?>
			<span class="user-grade-separator">:</span>&nbsp;<span class="user-grade ld-gb-grade" style="background: <?php echo $user_grade->get_display_grade_color( $user_grade->get_user_grade() ); ?>; color: <?php echo ( ( learndash_gradebook_is_light_color( $user_grade->get_display_grade_color( $user_grade->get_user_grade() ) ) ) ? 'inherit' : '#fff' ); ?>"><?php $user_grade->display_user_grade( $grade_format ); ?></span>
		<?php endif; ?>

	</h3>

</div>
