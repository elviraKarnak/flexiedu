<?php
/**
 * Course Reports User Records Template.
 *
 * @var $course_id          int     ID of the course.
 * @var $pagination_count   int     Current page number.
 * @var $course_users_paged array   List of course users on each page.
 * @var $page_count         int     Total number of pages.
 *
 * @since 3.3.0
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div id="user_info">
	<h3><?php echo __( 'User Information', 'wdm_instructor_role' ); ?></h3>
	<div id="reports_table_div">
		<div class="CL"></div>

		<!-- <form action="" method="post" id="wdm_pagination_frm">
			<?php echo __( 'Search', 'wdm_instructor_role' ); ?>
			<input id="filter" type="text"> <?php echo __( 'Show', 'wdm_instructor_role' ); ?>
			<input type="hidden" value="<?php echo $course_id; ?>" name="course_id" />
			<select name="wdm_pagination_select" onchange="jQuery('#wdm_pagination_frm').submit();">
				<option value="10" <?php echo ( 10 == $pagination_count ) ? 'selected' : ''; ?>>10</option>
				<option value="25" <?php echo ( 25 == $pagination_count ) ? 'selected' : ''; ?>>25</option>
				<option value="50" <?php echo ( 50 == $pagination_count ) ? 'selected' : ''; ?>>50</option>
				<option value="100" <?php echo ( 100 == $pagination_count ) ? 'selected' : ''; ?>>100</option>
			</select>
			<?php echo __( 'Records', 'wdm_instructor_role' ); ?>
		</form> -->

		<!--Table shows Name, Email, etc-->
		<table class="footable" data-page-navigation=".pagination" data-filter="#filter" id="wdm_report_tbl" >
			<thead>
				<tr>
					<th data-sort-initial="descending" data-class="expand">
						<?php esc_html_e( 'Name', 'wdm_instructor_role' ); ?>
					</th>
					<th>
						<?php esc_html_e( 'E-Mail ID', 'wdm_instructor_role' ); ?>
					</th>
					<th data-hide="phone" >
						<?php esc_html_e( 'Progress %', 'wdm_instructor_role' ); ?>
					</th>
					<th data-hide="phone" >
						<?php esc_html_e( 'Completed Steps', 'wdm_instructor_role' ); ?>
					</th>
					<th data-hide="phone,tablet" >
						<?php echo __( 'Completed On', 'wdm_instructor_role' ); ?>
					</th>
					<th data-hide="phone,tablet" data-sort-ignore="true">
						<?php echo __( 'Email', 'wdm_instructor_role' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php $this->display_course_report_user_records( $course_users_paged, $course_id ); ?>
			</tbody>
			<!-- <tfoot class="wdm-pagination">
				<tr>
					<td colspan="10" id="wdm_paged_td">
						<?php if ( $page_count > 1 ) : ?>
							<div class="tablenav-pages">
								<span class="pagination-links">
									<a class="first-page wdm-paged" id="wdm_first_page" title="Go to the first page" href="javascript:wdm_js_ajax_pagination(0);">«</a>
									<a class="prev-page wdm-paged" id="wdm_prev_page" title="Go to the previous page" href="javascript:wdm_js_ajax_pagination(0);">‹</a>
									<span class="paging-input">
										<span id="wdm_paged_start_num">1</span> of <span class="total-pages"><?php esc_html_e( $page_count ); ?></span>
									</span>
									<a class="next-page wdm-paged" id="wdm_next_page" title="Go to the next page" href="javascript:wdm_js_ajax_pagination(1);">›</a>
									<a class="last-page wdm-paged" id="wdm_last_page" title="Go to the last page" href="javascript:wdm_js_ajax_pagination(<?php esc_html_e( $page_count - 1 ); ?>);">»</a>
								</span>
							</div>
						<?php endif; ?>
					</td>
				</tr>
			</tfoot> -->
		</table>
	</div>
	<div class="CL"></div>
	<form method="post" action="">
		<input type="hidden" value="<?php echo $course_id; ?>" name="post_id_report" id="post_id_report" />
		<input class="wdm-button" type="submit" value=" <?php printf( /* translators: Course label */ esc_attr__( 'Export %s Data', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>"/>
	</form>
</div>

<!--For popup email div for individual-->
<div id="popUpDiv" style="display: none; top: 245.75px; left: 17%;">
	<div style="clear:both"></div>
	<table class="widefat" id="wdm_tbl_staff_mail"> <?php // cspell:disable-line . ?>
		<thead>
			<tr>
				<th colspan="2">
					<strong>
						<?php echo __( 'Send E-Mail To Individual Member', 'wdm_instructor_role' ); ?>
					</strong>
					<p id="wdm_close_pop" colspan="1" onclick="popup( 'popUpDiv' )">
						<span class="dashicons dashicons-no"></span>
					</p>
				</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td>
					<?php echo __( 'To', 'wdm_instructor_role' ); ?>
				</td>
				<td>
					<input type="text" id="wdm_staff_mail_id" value="" readonly="readonly">
				</td>
			</tr>
			<tr>
				<td>
					<?php echo __( 'Subject', 'wdm_instructor_role' ); ?>
				</td>
				<td>
					<input type="text" id="wdm_staff_mail_subject" value="">
				</td>
			</tr>
			<tr>
				<td>
					<?php echo __( 'Body', 'wdm_instructor_role' ); ?>
				</td>
				<td>
					<textarea id="wdm_staff_mail_body" rows="8"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input
						class="button-primary"
						type="button"
						name="wdm_btn_send_mail"
						value="<?php esc_attr_e( 'Send E-Mail', 'wdm_instructor_role' ); ?>"
						id="wdm_btn_send_mail"
						onclick="wdm_individual_send_email();"
					/>
					<span id="wdm_staff_mail_msg"></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>
