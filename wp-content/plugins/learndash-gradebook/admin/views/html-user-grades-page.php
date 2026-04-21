<?php
/**
 * HTMl for the User Grades screen.
 *
 * @since 1.2.0
 *
 * @var int $gradebook Current Gradebook
 * @var LD_GB_UserGrade $user_grade User grade object.
 * @var bool $is_weighted If grades are weighted or not.
 * @var WP_User $user Currently being viewed user.
 * @var array $grade_statuses
 * @var array $grade_status_options
 *
 * @package LearnDash_Gradebook
 *
 * cspell:ignore overidden .
 */

defined( 'ABSPATH' ) || die();

?>

<?php if ( isset( $_GET['return'] ) && isset( $_GET['referrer'] ) ) : ?>
	<p>
		<a href="<?php echo $_GET['referrer']; ?>" class="button ld-gb-button ld-gb-return-button">
			<?php
			switch ( $_GET['return'] ) {
				case 'gradebook':
					_e( 'Return to the Gradebook', 'learndash-gradebook' );
					break;

				case 'gradebook-edit':
					_e( 'Return to the Gradebook Edit Screen', 'learndash-gradebook' );
					break;
			}
			?>
		</a>
	</p>
<?php endif; ?>

<div id="ld-gb-gradebook">

	<?php if ( isset( $_GET['ld_gb_back_to_gradebook'] ) ) : ?>
		<div class="notice notice-info ld-gb-notice inline">
			<p>
				<?php
				printf(
					_x( 'To go back to the Gradebook %1$sclick here%2$s.', 'Both %s are HTML links to go back to the main Gradebook page', 'learndash-gradebook' ), // cspell: disable-line -- That's why I don't like the practice to include HTML in translations.
					'<a href="' . admin_url( 'admin.php?page=learndash-gradebook' ) . '">',
					'</a>'
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<h3>
		<?php
		printf(
			_x( 'Grade for %s', "User Grade Page Title. %s is the User's Display Name", 'learndash-gradebook' ),
			$user->display_name
		)
		?>
		<span class="user-grade-separator">:</span>&nbsp;<span class="user-grade ld-gb-grade"
				style="background: <?php $user_grade->display_user_grade_color(); ?>;"><?php $user_grade->display_user_grade(); ?></span>
	</h3>

	<?php if ( $user_grade->get_components() ) : ?>
		<?php foreach ( $user_grade->get_components() as $component ) : ?>

			<div id="ld-gb-component-<?php echo $component['id']; ?>" class="ld-gb-gradebook-component">
				<div class="ld-gb-gradebook-component-header">
					<div class="ld-gb-gradebook-component-name">
						<?php echo $component['name']; ?>
					</div>

					<div
							class="ld-gb-gradebook-component-overall-grade <?php echo $component['overridden'] ? 'overridden' : ''; ?>"
							data-ld-gb-component-grade="<?php echo $component['averaged_score']; ?>"
							data-user-id="<?php echo $user->ID; ?>"
							data-component-id="<?php echo $component['id']; ?>">

						<?php if ( ! ld_gb_get_option_field( 'disable_component_override', false ) ) : ?>

							<a href="#" class="ld-gb-gradebook-component-edit-open" data-ld-gb-component-edit>
							<?php
							if ( $component['overridden'] ) {
								_ex( 'Modify', 'Modify Overridden Component link text', 'learndash-gradebook' );
							} else {
								_ex( 'Override', 'Override Component Grade link text', 'learndash-gradebook' );
							}
							?>
								</a>

							<span class="ld-gb-grade-overidden-icon dashicons dashicons-info"></span>

						<?php endif; ?>

							<?php echo LD_GB_UserGrade::display_grade_html( $component['averaged_score'] ); ?>

						<?php if ( ! ld_gb_get_option_field( 'disable_component_override', false ) ) : ?>

							<?php if ( $is_weighted ) : ?>
								<span class="ld-gb-gradebook-component-weight">
									<?php
									printf(
										_x( 'Weight: %s', 'User Grade Page: Component Weight Display. %s is the Component Weight', 'learndash-gradebook' ),
										$component['weight'] . '%'
									);
									?>
								</span>
							<?php endif; ?>

							<div class="ld-gb-gradebook-component-edit">

								<a href="#" class="ld-gb-gradebook-component-edit-close" data-ld-gb-component-cancel
								aria-label="<?php _ex( 'Cancel', 'Cancel Overriding Component link text', 'learndash-gradebook' ); ?>">
									<span class="dashicons dashicons-no"></span>
								</a>

								<?php
								ld_gb_do_field_number(
									[
										'no_init' => true,
										'name'    => 'ld-gb-component-override',
										'id'      => false,
										'min'     => 0,
										'value'   => $component['averaged_score'],
										'postfix' => '%',
									]
								);
								?>

								<div class="ld-gb-gradebook-component-edit-actions">
									<button type="button" class="button" data-ld-gb-component-submit>
										<?php _ex( 'Submit', 'Submit Overridden Component', 'learndash-gradebook' ); ?>
									</button>

									<a href="#" class="ld-gb-cancel" data-ld-gb-component-delete
									><?php _ex( 'Remove', 'Remove Overridden Component', 'learndash-gradebook' ); ?></a>
								</div>
							</div>

						<?php endif; ?>
					</div>
				</div>

				<?php if ( ! ld_gb_get_option_field( 'disable_component_override', false ) ) : ?>

					<div class="ld-gb-gradebook-component-overridden-notice notice notice-warning ld-gb-notice inline"
						<?php echo $component['overridden'] ? '' : 'style="display: none;"'; ?>>
						<p>
							<?php _e( 'Grade is being overridden.', 'learndash-gradebook' ); ?>
						</p>
					</div>

				<?php endif; ?>

				<table class="ld-gb-gradebook-component-grades">
					<thead>
					<tr>
						<th colspan="3">
							<?php _ex( 'Name', 'Component Grade Name Label', 'learndash-gradebook' ); ?>
						</th>

						<th class="ld-gb-gradebook-component-grade-score">
							<?php _ex( 'Score', 'Component Grade Score Label', 'learndash-gradebook' ); ?>
						</th>
					</tr>
					</thead>

					<tbody>
					<?php
					$no_grades = ! $component['grades'];

					// Append manual template
					$component['grades'][] = [
						'name'  => '',
						'score' => '',
						'type'  => 'manual',
					];
					?>
					<?php foreach ( $component['grades'] as $i => $grade ) : ?>
						<?php $is_template = $i === count( $component['grades'] ) - 1; ?>
						<tr class="ld-gb-gradebook-component-grade-display <?php echo $i % 2 === 1 ? 'odd' : 'even'; ?>"
							<?php echo $is_template ? 'data-template' : ''; ?>>
							<td class="ld-gb-gradebook-component-grade-name">

									<span class="ld-gb-gradebook-component-grade-icon"
											title="<?php echo ld_gb_get_grade_type_name( $grade['type'] ); ?>">
										<span class="dashicons <?php echo ld_gb_grade_icon( $grade['type'] ); ?>">
										</span>
									</span>

								<span class="ld-gb-gradebook-component-grade-name-content">
										<?php if ( isset( $grade['post_id'] ) && $grade['post_id'] ) : ?>
											<a href="<?php echo get_edit_post_link( $grade['post_id'] ); ?>" target="_blank">
												<?php echo $grade['name']; ?>
											</a>

											<?php if ( isset( $grade['pending_essay_questions'] ) && ! empty( $grade['pending_essay_questions'] ) ) : ?>

												<p>
													<?php
													echo esc_html(
														sprintf(
															// translators: %s is the labels for Essay and Questions
															__( 'Ungraded %1$s %2$s:', 'learndash-gradebook' ),
															learndash_get_custom_label( 'essay' ),
															learndash_get_custom_label( 'questions' )
														)
													);
													?>
												</p>

												<ul style="list-style-type: disc; margin-left: 1.5em;">
													<?php foreach ( $grade['pending_essay_questions'] as $question ) : ?>

														<li>
															<a href="<?php echo get_edit_post_link( $question['post_id'] ); ?>" target="_blank">
																<?php echo get_the_title( $question['post_id'] ); ?>
															</a>
														</li>

													<?php endforeach; ?>
												</ul>

											<?php endif; ?>

										<?php else : ?>
											<?php echo $grade['name']; ?>
										<?php endif; ?>
									</span>
								&nbsp;
							</td>

							<td class="ld-gb-gradebook-component-grade-actions">

								<?php
								if ( ( ! isset( $grade['status'] ) || $grade['status'] !== 'pending' ) &&
									( $grade['type'] !== 'assignment' || get_post_type( $grade['post_id'] ) == 'sfwd-assignment' ) ) :
									?>

									<div class="ld-gb-gradebook-component-grade-actions-container">
										<a href="#" class="ld-gb-gradebook-component-grade-edit" data-edit-grade>
											<?php _ex( 'Edit', 'Component Grade Edit link text', 'learndash-gradebook' ); ?>
										</a>

										<?php if ( $grade['type'] == 'manual' ) : ?>
											&nbsp;
											<a href="#" class="ld-gb-gradebook-component-grade-remove ld-gb-cancel"
											data-remove-manual-grade>
												<?php _ex( 'Remove', 'Component Manual Grade Remove link text', 'learndash-gradebook' ); ?>
											</a>
										<?php endif; ?>
									</div>

								<?php endif; ?>

							</td>

							<td class="ld-gb-gradebook-component-grade-timestamp">
								<?php
								if ( isset( $grade['completed'] ) && $grade['completed'] ) :

									echo ld_gb_get_datetimestamp( $grade['completed'] );

								endif;
								?>
							</td>

							<td class="ld-gb-gradebook-component-grade-score">
									<span class="ld-gb-gradebook-component-grade-score-content"
											data-value-id="grade-score">
										<?php echo ( isset( $grade['score_display'] ) ? $grade['score_display'] : '' ); ?>
									</span>
							</td>
						</tr>
						<?php
						if ( ( ! isset( $grade['status'] ) || $grade['status'] !== 'pending' ) &&
							( $grade['type'] !== 'assignment' || get_post_type( $grade['post_id'] ) == 'sfwd-assignment' ) ) :
							?>

							<tr class="ld-gb-gradebook-component-grade-editform"
								<?php echo $is_template ? 'data-template' : ''; ?>>
								<td colspan="3">

									<input type="hidden" name="grade-gradebook" value="<?php echo $gradebook; ?>"/>
									<input type="hidden" name="grade-type" value="<?php echo $grade['type']; ?>"/>
									<input type="hidden" name="grade-component"
										value="<?php echo $component['id']; ?>"/>
									<input type="hidden" name="grade-user_id" value="<?php echo $user->ID; ?>"/>

									<div class="ld-gb-gradebook-component-grade-message">
										<div class="notice inline notice-error">
											<p>
												<?php _e( 'Name and Score are required', 'learndash-gradebook' ); ?>
											</p>
										</div>

										<?php if ( ! ld_gb_get_option_field( 'disable_manual_grades', false ) ) : ?>

											<div class="notice inline notice-success">
												<p>
													<?php _e( 'Manual Grade successfully added', 'learndash-gradebook' ); ?>
												</p>
											</div>

										<?php endif; ?>
									</div>

									<?php if ( $grade['type'] !== 'manual' ) : ?>

										<input type="hidden" name="grade-post_id"
											value="<?php echo $grade['post_id']; ?>"/>
										<input type="hidden" name="grade-score"
											value="<?php echo ( isset( $grade['original_score'] ) ? $grade['original_score'] : '0' ); ?>"/>

									<?php elseif ( ! ld_gb_get_option_field( 'disable_manual_grades', false ) ) : ?>

										<input type="hidden" name="grade-new"
											value="<?php echo $is_template ? '1' : '0'; ?>"/>
										<input type="hidden" name="grade-previous_name"
											value="<?php echo $grade['name']; ?>"/>

										<label>
											<?php _ex( 'Name', 'Manual Grade Name Label', 'learndash-gradebook' ); ?><br/>
											<?php
											ld_gb_do_field_text(
												[
													'no_init' => true,
													'name' => 'grade-name',
													'value' => $grade['name'],
												]
											);
											?>
										</label>

										<label>
											<?php _ex( 'Score', 'Manual Grade Score Label', 'learndash-gradebook' ); ?><br/>
											<input type="number" name="grade-score" min="0" max="100"
												value="<?php echo esc_attr( ( isset( $grade['original_score'] ) ? $grade['original_score'] : '' ) ); ?>" data-default="<?php echo apply_filters( 'learndash_gradebook_manual_grade_default_score', '' ); ?>"/>
											<br/>
										</label>

									<?php endif; ?>

									<?php if ( ! isset( $grade['status'] ) || $grade['status'] !== 'pending' ) : ?>

										<label>
											<?php _ex( 'Status', 'Component Grade Status label', 'learndash-gradebook' ); ?><br/>
											<?php
											ld_gb_do_field_select(
												[
													'no_init' => true,
													'name' => 'grade-status',
													'select2_disable' => true,
													'option_none' => _x( 'No Special Status', 'No Status option for Grade Status', 'learndash-gradebook' ),
													'options' => $grade_status_options,
													'value' => ( isset( $grade['status'] ) && $grade['status'] ) ? $grade['status'] : '',
													'default_option' => [
														'' => _x( '- No Special Status -', 'No Status option for Grade Status', 'learndash-gradebook' ),
													],
													'no_options' => [
														'' => _x( '- No Statuses -', 'No Grade Status available text', 'learndash-gradebook' ),
													],
												]
											);
											?>
										</label>

									<?php endif; ?>

									<div class="ld-gb-gradebook-component-grade-editform-actions">
										<button type="button" data-submit-edit-grade
												class="button ld-gb-gradebook-component-grade-submit">
											<?php
											if ( $is_template ) {
												_ex( 'Add', 'Component Grade Add button text', 'learndash-gradebook' );
											} else {
												_ex( 'Change', 'Component Grade Change button text', 'learndash-gradebook' );
											}
											?>
										</button>

										<a href="#" class="ld-gb-cancel ld-gb-gradebook-component-grade-cancel"
											<?php echo $is_template ? 'data-cancel-add-manual-grade' : 'data-cancel-edit-grade'; ?>>
											<?php
											if ( $is_template ) {
												_ex( 'Cancel', 'Component Grade Edit Cancel button text', 'learndash-gradebook' );
											} else {
												_ex( 'Close', 'Component Grade Edit Close button text', 'learndash-gradebook' );
											}
											?>
										</a>
									</div>
								</td>
							</tr>

						<?php endif; ?>

					<?php endforeach; ?>

					<tr class="ld-gb-gradebook-component-no-grades" data-no-grades
						<?php echo $no_grades ? '' : 'style="display: none;"'; ?>>
						<td colspan="3">
							<?php _e( 'No Grades Yet', 'learndash-gradebook' ); ?>
						</td>
					</tr>

					</tbody>
				</table>

				<?php if ( ! ld_gb_get_option_field( 'disable_manual_grades', false ) ) : ?>

					<button type="button" class="button ld-gb-gradebook-component-grade-add" data-add-manual-grade>
						<?php _e( 'Add Manual Grade', 'learndash-gradebook' ); ?>
					</button>

				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	<?php else : ?>
		<?php _e( 'No Components setup.', 'learndash-gradebook' ); ?>
	<?php endif; ?>

</div>
