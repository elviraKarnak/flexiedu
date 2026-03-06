<?php
/**
 * Course Reports User Single Record Template.
 *
 * @var $user_meta              object  Object containing all user details.
 * @var $completed_percentage   int     Percentage of course completed by the user.
 * @var $completed_steps        int     No of steps completed by the user.
 * @var $total_steps            int     Total no of steps in the course.
 * @var $course_completed_on    string  Date on which the course was completed.
 *
 * @since 3.3.0
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<tr>
	<td><?php echo esc_html( $user_meta->data->user_login ); ?></td>
	<td><?php echo esc_html( $user_meta->data->user_email ); ?></td>
	<td data-order="<?php echo esc_attr( $completed_percentage ); ?>"><?php echo esc_html( $completed_percentage ); ?></td>
	<td data-order="<?php echo ( $total_steps > 0 ) ? floatval( $completed_steps / $total_steps ) : ''; ?>"><?php echo esc_html( $completed_steps . '/' . $total_steps ); ?></td>
	<td data-order="<?php echo esc_attr( $course_completed_timestamp ); ?>"><?php echo esc_html( $course_completed_on ); ?></td>
	<td>
		<a
			href="javascript:wdm_show_email_form('<?php echo esc_html( $user_meta->data->user_email ); ?>');"
			title="
			<?php
			// translators: Student email.
			echo esc_html( sprintf( __( 'E-Mail to %s', 'wdm_instructor_role' ), $user_meta->data->user_email ) );
			?>
			">
			<?php esc_html_e( 'E-Mail', 'wdm_instructor_role' ); ?>
		</a>
	</td>
</tr>
