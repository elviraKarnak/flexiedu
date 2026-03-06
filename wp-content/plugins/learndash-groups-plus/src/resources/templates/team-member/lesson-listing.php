<?php
/**
 * List of assignments on lessons as team members.
 *
 * @since 1.0.0
 * @version 2.1.5
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore wpspin ldquiz ldquizntf quizinfo courseinfo notcompleted quizid
 */

use LearnDash\Groups_Plus\Utility\SharedFunctions;
use LearnDash\Groups_Plus\Utility\Timer;

$hide_groups_plus_lock_column_in_detail_team_member_report = get_site_option( 'hide_groups_plus_lock_column_in_detail_team_member_report', 'no' );
?>
<style>
	.div-table-container .div-table-col.ld-lock-unlock-header,
	.ld-lock-unlock {
		display: <?php echo $hide_groups_plus_lock_column_in_detail_team_member_report === 'no' ? 'initial' : 'none'; ?>;
	}
</style>
<?php
$user_courses_lessons_quizzes = [];

// Filter as per query parameter for user id.
if ( empty( $selected_team_member_id ) ) {
	$user_group_users = ! empty( $user_group_users ) ? [ $user_group_users[0] ] : [];
} else {
	$user_group_users = [ get_userdata( $selected_team_member_id ) ];
}
$user_courses_lessons_quizzes = [
	'user_id'   => 0,
	'course_id' => 0,
];

$design_setting = get_site_option( 'design_setting' );
if ( ! is_array( $design_setting ) ) {
	$design_setting = [];
}
if (
	! isset( $design_setting['color_codes'] )
	|| ! is_array( $design_setting['color_codes'] )
) {
	$design_setting['color_codes'] = [];
}

if ( empty( $design_setting['color_codes']['not_start'] ) ) {
	$design_setting['color_codes']['not_start'] = '#FF0000';
}
if ( empty( $design_setting['color_codes']['in_progress'] ) ) {
	$design_setting['color_codes']['in_progress'] = '#FFA500';
}
if ( empty( $design_setting['color_codes']['completed'] ) ) {
	$design_setting['color_codes']['completed'] = '#008000';
}

// check whether show all quiz attempts or just one.
$has_show_all_quiz_attempts = get_site_option( 'show_all_quiz_attempts' );

$is_gravity_form_active = SharedFunctions::is_gravity_form_active();

foreach ( $user_group_users as $user ) {
	$user_id = $user->ID;

	$user_quiz_meta = get_user_meta( $user_id, '_sfwd-quizzes', true );
	if ( ! is_array( $user_quiz_meta ) ) {
		$user_quiz_meta = [];
	}
	if ( empty( $filter_course_id ) ) {
		$courses = empty( $result_courses ) ? [] : [ $result_courses[0]->ID ];
	} else {
		$courses = [ $filter_course_id ];
	}
	if ( ( is_array( $courses ) ) && ( ! empty( $courses ) ) ) {
		foreach ( $courses as $course ) {
			$course_id   = $course;
			$course_post = get_post( $course_id );
			// Get a list of lessons to loop.
			$lessons = learndash_get_course_lessons_list( $course_id, null, [ 'num' => 0 ] );

			// Course Status.
			$course_status = learndash_course_status( $course_id, $user->ID );
			if ( $course_status == 'Completed' ) {
				$course_color = $design_setting['color_codes']['completed'];
			} elseif ( $course_status == 'In Progress' ) {
				$course_color = $design_setting['color_codes']['in_progress'];
			} else {
				$course_color = $design_setting['color_codes']['not_start'];
			}
			$course_cert = learndash_get_course_certificate_link( $course_id, $user_id );
			$progress    = learndash_course_progress(
				[
					'user_id'   => $user_id,
					'course_id' => $course_id,
					'array'     => true,
				]
			);

			$user_courses_lessons_quizzes = [
				'user_id'                    => $user->ID,
				'user_name'                  => $user->first_name . ' ' . $user->last_name,
				'course_id'                  => $course_id,
				'course_status'              => $course_status,
				'course_name'                => isset( $course_post->post_title ) ? $course_post->post_title : '',
				'course_color'               => $course_color,
				'lessons_data'               => [],
				'course_quizzes_data'        => [],
				'course_certificate'         => $course_cert,
				'course_progress_percentage' => $progress['percentage'],
			];

			if ( ( is_array( $lessons ) ) && ( ! empty( $lessons ) ) ) {
				// Loop course's lessons.
				foreach ( $lessons as $lesson ) {
					$post      = $lesson['post'];
					$lesson_id = $lesson['post']->ID;

					// Lesson Status.
					$lesson_status          = '';
					$lesson_completion_time = '';
					if ( learndash_is_lesson_complete( $user_id, $lesson_id, $course_id ) ) {
						$lesson_status          = 'Completed';
						$lesson_color           = $design_setting['color_codes']['completed'];
						$lesson_args            = [
							'course_id'     => $course_id,
							'user_id'       => $user_id,
							'post_id'       => $lesson_id,
							'activity_type' => 'lesson',
						];
						$lesson_completion_time = learndash_get_user_activity( $lesson_args );
						if ( ! empty( $lesson_completion_time ) && $lesson_completion_time->activity_completed ) {
							$lesson_completion_time = learndash_adjust_date_time_display( $lesson_completion_time->activity_completed );
						} else {
							$lesson_completion_time = 'Unknown';
						}
					} else {
						$lesson_status = 'Not Completed';
						$lesson_color  = $design_setting['color_codes']['not_start'];
					}

					$lessons_data = [
						'lesson_id'              => $lesson_id,
						'lesson_name'            => $post->post_title,
						'lesson_url'             => learndash_get_step_permalink( $lesson_id, $course_id ),
						'lesson_status'          => $lesson_status,
						'lesson_completion_time' => $lesson_completion_time,
						'lesson_color'           => $lesson_color,
						'time_spent'             => Timer::get_time_spent( $post, $user->ID ),
						'topics_data'            => [],
						'quizzes_data'           => [],
						'assignments_data'       => [],
					];

					// Get lesson's topics.
					$topics = learndash_get_topic_list( $lesson_id, $course_id );

					if ( ( is_array( $topics ) ) && ( ! empty( $topics ) ) ) {
						// Loop Topics.
						foreach ( $topics as $topic ) {
							$topic_id = $topic->ID;

							// Topic Status.
							$topic_completed_date = '';
							if ( learndash_is_topic_complete( $user_id, $topic_id, $course_id ) ) {
								if ( $lessons_data['lesson_color'] != $design_setting['color_codes']['completed'] ) {
									$lessons_data['lesson_color'] = $design_setting['color_codes']['in_progress'];
								}
								$topic_color    = $design_setting['color_codes']['completed'];
								$topic_args     = [
									'course_id'     => $course_id,
									'user_id'       => $user_id,
									'post_id'       => $topic_id,
									'activity_type' => 'topic',
								];
								$topic_activity = learndash_get_user_activity( $topic_args );

								if ( ! empty( $topic_activity ) ) {
									$topic_color          = $design_setting['color_codes']['completed'];
									$topic_completed_date = learndash_adjust_date_time_display( $topic_activity->activity_completed );
								} else {
									$topic_color          = $design_setting['color_codes']['not_start'];
									$topic_completed_date = 'Not Started';
								}
							} else {
								$topic_color          = $design_setting['color_codes']['not_start'];
								$topic_completed_date = 'Not Started';
							}

							$topics_data = [
								'topic_id'             => $topic_id,
								'topic_name'           => $topic->post_title,
								'topic_url'            => get_post_permalink( $topic_id ),
								'topic_color'          => $topic_color,
								'topic_completed_date' => $topic_completed_date,
								'time_spent'           => Timer::get_time_spent( $topic, $user->ID ),
								'quizzes_data'         => [],
								'assignments_data'     => [],
							];


							/**  Get quiz of lesson */
							// check if lesson has quiz.
							$topic_quiz_args = [
								'post_type'  => [ 'sfwd-quiz' ],
								'meta_key'   => 'topic_id',
								'meta_value' => $topic_id,
							];
							$topic_quizzes   = new WP_Query( $topic_quiz_args );

							// Get Topic's Quizzes.

							$topic_quizzes = learndash_get_lesson_quiz_list( $topic_id, $user_id, $course_id );

							if ( ( is_array( $topic_quizzes ) ) && ( ! empty( $topic_quizzes ) ) ) {
								foreach ( $topic_quizzes as $quiz ) {
									$quiz_id  = $quiz['post']->ID;
									$pt       = 0;
									$attempts = learndash_get_user_quiz_attempts( $user_id, $quiz_id );
									if (
										$quiz_id
										&& is_array( $attempts )
										&& count( $attempts )
									) {
										// $pt = do_shortcode("[courseinfo show='cumulative_percentage'  user_id='".$user_id."' course_id='".$course_id."']");
										$pt         = do_shortcode( '[quizinfo show="percentage"  user_id="' . $user_id . '" quiz="' . $quiz_id . '"]' );
										$quiz_score = ! empty( $pt ) ? $pt . '%' : '-';
									} else {
										$quiz_score = '-';
									}

									$certificate_link  = '';
									$pro_quizid        = $statistic_ref_id = $quiz_pro_statistics_on = 0;
									$quiz_attempts_key = [];

									if ( $quiz_id ) {
										if ( $quiz['status'] == 'completed' ) {
											$cert = learndash_certificate_details( $quiz_id, $user_id );
											if ( ( isset( $cert['certificateLink'] ) ) && ( ! empty( $cert['certificateLink'] ) ) ) {
												$certificate_link = $cert['certificateLink'];
											}
										}

										if ( $pt >= 0 ) {
											$quiz_pro_statistics_on = learndash_get_setting( $quiz_id, 'statisticsOn', true );

											$user_quiz_meta_ = array_reverse( $user_quiz_meta );
											if ( $has_show_all_quiz_attempts && ! empty( array_keys( array_column( $user_quiz_meta, 'quiz' ), $quiz_id ) ) ) {
												$quiz_attempts_key = array_keys( array_column( $user_quiz_meta, 'quiz' ), $quiz_id );

												$user_quiz_meta_ = $user_quiz_meta;
											} else {
												$key = array_search( $quiz_id, array_column( $user_quiz_meta_, 'quiz' ) );

												$pro_quizid       = isset( $user_quiz_meta_[ $key ]['pro_quizid'] ) ?? 0;
												$statistic_ref_id = isset( $user_quiz_meta_[ $key ]['statistic_ref_id'] ) ?? 0;

												$quiz_attempts_key = [ $key ];
											}
										}
									}

									$quizzes_data = [
										'quiz_id'          => $quiz_id,
										'quiz_name'        => $quiz['post']->post_title,
										'quiz_url'         => get_post_permalink( $quiz_id ),
										'quiz_score'       => $quiz_score,
										'quiz_certificate' => $certificate_link,
										'quiz_statistics_on' => $quiz_pro_statistics_on,
										'pro_quizid'       => $pro_quizid,
										'statistic_ref_id' => $statistic_ref_id,
										'time_spent'       => Timer::get_time_spent( $quiz['post'], $user->ID ),
									];
									// Quiz Attempts.
									$quizzes_data['quiz_attempts'] = [];
									foreach ( $quiz_attempts_key as $quiz_attempt_key ) {
										$quiz_score = $pro_quizid = $statistic_ref_id = 0;
										if ( isset( $user_quiz_meta_[ $quiz_attempt_key ] ) && $user_quiz_meta_[ $quiz_attempt_key ]['quiz'] == $quiz_id ) {
											$quiz_score       = $user_quiz_meta_[ $quiz_attempt_key ]['percentage'];
											$pro_quizid       = $user_quiz_meta_[ $quiz_attempt_key ]['pro_quizid'];
											$statistic_ref_id = $user_quiz_meta_[ $quiz_attempt_key ]['statistic_ref_id'];
										}

										$quiz_score = ! empty( $quiz_score ) ? $quiz_score . '%' : '-';

										$certificate_link_ = '';
										if ( ! empty( $certificate_link ) ) {
											$certificate_link_ = $certificate_link . '&time=' . $user_quiz_meta_[ $quiz_attempt_key ]['time'];
										}


										// Essays.
										$essays_data = [];
										if ( $quiz_id /* && $quiz['status'] != 'notcompleted' */ ) {
											$quiz_questions = learndash_get_quiz_questions( $quiz_id );
											foreach ( $quiz_questions as $quiz_question_id => $quiz_question_pro_id ) {
												$question_type = get_post_meta( $quiz_question_id, 'question_type', true );

												if (
													$question_type === 'essay'
													&& isset( $user_quiz_meta_[ $quiz_attempt_key ]['graded'][ $quiz_question_pro_id ] )
												) {
													$essay_post_id                = $user_quiz_meta_[ $quiz_attempt_key ]['graded'][ $quiz_question_pro_id ]['post_id'];
													$question_post_id             = (int) get_post_meta( $essay_post_id, 'question_post_id', true );
													$question_post_title          = get_the_title( $question_post_id );
													$essay_comments_number        = get_comments_number( $essay_post_id );
													$last_essay_attempted_details = learndash_get_essay_details( $essay_post_id );
													$essays_data[]                = [
														'essay_id' => $essay_post_id,
														'essay_url' => get_post_permalink( $essay_post_id ),
														'question_post_id' => $question_post_id,
														'question_post_title' => $question_post_title,
														'points' => $last_essay_attempted_details['points'],
														'status' => $last_essay_attempted_details['status'],
														'comments' => $essay_comments_number,
													];
												}
											}
										}

										$quizzes_data['quiz_attempts'][] = [
											'quiz_id'     => $quiz_id,
											'quiz_name'   => $quiz['post']->post_title,
											'quiz_url'    => get_post_permalink( $quiz_id ),
											'quiz_score'  => $quiz_score,
											'quiz_certificate' => $certificate_link_,
											'quiz_statistics_on' => $quiz_pro_statistics_on,
											'pro_quizid'  => $pro_quizid,
											'statistic_ref_id' => $statistic_ref_id,
											'essays_data' => $essays_data,
										];
									}




									$topics_data['quizzes_data'][] = $quizzes_data;
								}
							}

							/**  Get assignments of topic */
							$topic_assignments = learndash_get_user_assignments( $topic_id, $user_id );
							if ( is_array( $topic_assignments ) && ! empty( $topic_assignments ) ) {
								$points_enabled = learndash_get_setting( $topic_id, 'lesson_assignment_points_enabled' );
								if ( 'on' === $points_enabled ) {
									$is_points_enabled = 1;
									$assignment_points = learndash_get_setting( $topic_id, 'lesson_assignment_points_amount' );
								} else {
									$is_points_enabled = 0;
									$assignment_points = 0;
								}

								// Loop Assignments.
								foreach ( $topic_assignments as $topic_assignment ) {
									$assignment_id = $topic_assignment->ID;

									if ( learndash_is_assignment_approved_by_meta( $assignment_id ) ) {
										$is_approved = true;
									} else {
										$is_approved = false;
									}
									$assignments_data = [
										'assignment_id'   => $assignment_id,
										'assignment_name' => $topic_assignment->post_title,
										'assignment_url'  => get_post_permalink( $assignment_id ),
										'assignment_media_url' => get_post_meta( $assignment_id, 'file_link', true ),
										'is_approved'     => $is_approved,
										'is_points_enabled' => $is_points_enabled,
										'assignment_points' => $assignment_points,
										'comments'        => get_comments_number( $assignment_id ),
									];

									$assignments_data['is_gf_enabled'] = $is_gravity_form_active;
									if ( $is_gravity_form_active ) {
										$gf_page_url                      = get_post_meta( $topic_id, 'gf-page-url', true );
										$gf_form_id                       = get_post_meta( $topic_id, 'gf-form-id', true );
										$gf_form_field_id                 = get_post_meta( $topic_id, 'gf-form-field-id', true );
										$gf_form_field_team_member_id_key = get_post_meta( $topic_id, 'gf-form-field-team-member-id', true );
										$has_gf_entries                   = false;
										$gf_chosen_text                   = '';

										if (
											'on' == learndash_get_setting( $topic_id, 'lesson_assignment_upload' )
											&& ! empty( $gf_page_url )
											&& ! empty( $gf_form_id )
											&& ! empty( $gf_form_field_id )
										) {
											$gf_form                        = \GFAPI::get_form( $gf_form_id );
											$gf_hidden_assignment_id_field  = 0;
											$gf_hidden_team_member_id_field = 0;
											// Let's iterate through the "fields" array and find our fields there.
											foreach ( $gf_form['fields'] as $field ) {
												if ( isset( $field['inputName'] ) && $field['inputName'] === 'assignment_id' ) {
													$gf_hidden_assignment_id_field = $field['id'];
												} elseif ( isset( $field['inputName'] ) && $field['inputName'] === 'team_member_id' ) {
													$gf_hidden_team_member_id_field = $field['id'];
												}
											}

											// Check if the hidden team_member_id field set to form. if set then use that user id, else use gf-form-field-team-member-id meta set under the post(lesson/topic) settings.
											if ( isset( $gf_hidden_team_member_id_field ) && is_int( $gf_hidden_team_member_id_field ) ) {
												$gf_form_field_team_member_id_key = $gf_hidden_team_member_id_field;
											}

											$assignments_data['is_gf_enabled_for_post'] = true;
											$assignments_data['gf_assignment_status']   = 'pending';
											$search_criteria                            = [];
											$search_criteria['field_filters'][]         = [
												'key'   => $gf_form_field_team_member_id_key,
												'value' => $user_id,
											];

											if ( $gf_hidden_assignment_id_field ) {
												$search_criteria['field_filters'][] = [
													'key' => $gf_hidden_assignment_id_field,
													'value' => $assignments_data['assignment_id'],
												];
											}

											$entries        = \GFAPI::get_entries( $gf_form_id, $search_criteria );
											$has_gf_entries = false;
											$choice_text    = '';
											if ( is_array( $entries ) && ! empty( $entries ) ) {
												$has_gf_entries                     = true;
												$assignments_data['has_gf_entries'] = $has_gf_entries;

												$selected_value = $entries[0][ $gf_form_field_id ];
												$field_one      = \GFAPI::get_field( $gf_form_id, $gf_form_field_id );
												if ( $selected_value && is_array( $field_one->choices ) ) {
													$choices_key = array_search( $selected_value, array_column( $field_one->choices, 'value' ) );
													$choice_text = $field_one->choices[ $choices_key ]['text'];
												}
												if ( $choice_text === 'Competent' || $choice_text === 'Satisfactory' ) {
													$assignments_data['gf_assignment_status'] = 'approved';
												} else {
													$assignments_data['gf_assignment_status'] = 'resubmit';
												}
											}
											// Appending the assignment_id and team_member_id to GF url.
											$gf_page_url = $gf_page_url . ( parse_url( $gf_page_url, PHP_URL_QUERY ) ? '&' : '?' )
												. 'assignment_id=' . $assignment_id . '&team_member_id=' . $user_id
												. '&team_member_email=' . $user->user_email;

											$assignments_data['gf_page_url'] = $gf_page_url;
										}
									}
									$topics_data['assignments_data'][] = $assignments_data;
								}
							}

							$lessons_data['topics_data'][] = $topics_data;
						}
					}

					/**  Get quiz of lesson */
					// check if lesson has quiz.
					$lesson_quizzes = learndash_get_lesson_quiz_list( $lesson_id, $user_id, $course_id );


					if ( ( is_array( $lesson_quizzes ) ) && ( ! empty( $lesson_quizzes ) ) ) {
						// Loop Quizzes.
						foreach ( $lesson_quizzes as $quiz ) {
							$quiz_id  = $quiz['post']->ID;
							$pt       = 0;
							$attempts = learndash_get_user_quiz_attempts( $user_id, $quiz_id );

							if (
								$quiz_id
								&& is_array( $attempts )
								&& count( $attempts )
							) {
								// $pt = do_shortcode("[courseinfo show='cumulative_percentage'  user_id='".$user_id."' course_id='".$course_id."']");
								$pt         = do_shortcode( '[quizinfo show="percentage"  user_id="' . $user_id . '" quiz="' . $quiz_id . '"]' );
								$quiz_score = $pt ? $pt . '%' : '-';
							} else {
								$quiz_score = '-';
							}

							$certificate_link = '';
							$pro_quizid       = $statistic_ref_id = $quiz_pro_statistics_on = 0;
							if ( $quiz_id ) {
								if ( $quiz['status'] == 'completed' ) {
									$cert = learndash_certificate_details( $quiz_id, $user_id );
									if ( ( isset( $cert['certificateLink'] ) ) && ( ! empty( $cert['certificateLink'] ) ) ) {
										$certificate_link = $cert['certificateLink'];
									}
								}

								if ( $pt >= 0 ) {
									$quiz_pro_statistics_on = learndash_get_setting( $quiz_id, 'statisticsOn', true );

									if ( $has_show_all_quiz_attempts && ! empty( array_keys( array_column( $user_quiz_meta, 'quiz' ), $quiz_id ) ) ) {
										$quiz_attempts_key = array_keys( array_column( $user_quiz_meta, 'quiz' ), $quiz_id );

										$user_quiz_meta_ = $user_quiz_meta;
									} else {
										$user_quiz_meta_ = array_reverse( $user_quiz_meta );
										$key             = array_search( $quiz_id, array_column( $user_quiz_meta_, 'quiz' ) );


										$pro_quizid       = isset( $user_quiz_meta_[ $key ]['pro_quizid'] ) ?? 0;
										$statistic_ref_id = isset( $user_quiz_meta_[ $key ]['statistic_ref_id'] ) ?? 0;

										$quiz_attempts_key = [ $key ];
									}
								}
							}

							$quizzes_data = [
								'quiz_id'            => $quiz_id,
								'quiz_name'          => $quiz['post']->post_title,
								'quiz_url'           => get_post_permalink( $quiz_id ),
								'quiz_score'         => $quiz_score,
								'quiz_certificate'   => $certificate_link,
								'quiz_statistics_on' => $quiz_pro_statistics_on,
								'pro_quizid'         => $pro_quizid,
								'statistic_ref_id'   => $statistic_ref_id,
								'time_spent'         => Timer::get_time_spent( $quiz['post'], $user->ID ),
							];

							// Quiz Attempts.
							$quizzes_data['quiz_attempts'] = [];
							foreach ( $quiz_attempts_key as $quiz_attempt_key ) {
								$quiz_score = $pro_quizid = $statistic_ref_id = 0;
								if ( isset( $user_quiz_meta_[ $quiz_attempt_key ] ) && $user_quiz_meta_[ $quiz_attempt_key ]['quiz'] == $quiz_id ) {
									$quiz_score       = $user_quiz_meta_[ $quiz_attempt_key ]['percentage'];
									$pro_quizid       = $user_quiz_meta_[ $quiz_attempt_key ]['pro_quizid'];
									$statistic_ref_id = $user_quiz_meta_[ $quiz_attempt_key ]['statistic_ref_id'];
								}
								$quiz_score = ! empty( $quiz_score ) ? $quiz_score . '%' : '-';

								$certificate_link_ = '';
								if ( ! empty( $certificate_link ) ) {
									$certificate_link_ = $certificate_link . '&time=' . $user_quiz_meta_[ $quiz_attempt_key ]['time'];
								}

								// Essays.
								$essays_data = [];
								if ( $quiz_id /* && $quiz['status'] != 'notcompleted' */ ) {
									$quiz_questions = learndash_get_quiz_questions( $quiz_id );
									foreach ( $quiz_questions as $quiz_question_id => $quiz_question_pro_id ) {
										$question_type = get_post_meta( $quiz_question_id, 'question_type', true );

										if (
											$question_type === 'essay'
											&& isset( $user_quiz_meta_[ $quiz_attempt_key ]['graded'][ $quiz_question_pro_id ] )
										) {
											$essay_post_id                = $user_quiz_meta_[ $quiz_attempt_key ]['graded'][ $quiz_question_pro_id ]['post_id'];
											$question_post_id             = (int) get_post_meta( $essay_post_id, 'question_post_id', true );
											$question_post_title          = get_the_title( $question_post_id );
											$essay_comments_number        = get_comments_number( $essay_post_id );
											$last_essay_attempted_details = learndash_get_essay_details( $essay_post_id );
											$essays_data[]                = [
												'essay_id' => $essay_post_id,
												'essay_url' => get_post_permalink( $essay_post_id ),
												'question_post_id' => $question_post_id,
												'question_post_title' => $question_post_title,
												'points'   => $last_essay_attempted_details['points'],
												'status'   => $last_essay_attempted_details['status'],
												'comments' => $essay_comments_number,
											];
										}
									}
								}

								$quizzes_data['quiz_attempts'][] = [
									'quiz_id'            => $quiz_id,
									'quiz_name'          => $quiz['post']->post_title,
									'quiz_url'           => get_post_permalink( $quiz_id ),
									'quiz_score'         => $quiz_score,
									'quiz_certificate'   => $certificate_link_,
									'quiz_statistics_on' => $quiz_pro_statistics_on,
									'pro_quizid'         => $pro_quizid,
									'statistic_ref_id'   => $statistic_ref_id,
									'essays_data'        => $essays_data,
								];
							}

							// Essays.
							$quizzes_data['essays_data'] = [];
							// if($quiz_id /* && $quiz['status'] != 'notcompleted' */ ){
							// $quiz_questions = learndash_get_quiz_questions($quiz_id);
							// foreach($quiz_questions as $quiz_question_id => $quiz_question_pro_id){
							// $question_type = get_post_meta( $quiz_question_id, 'question_type', true );
							// if($question_type === 'essay'){
							// if( isset($user_quiz_meta_[$quiz_attempts_key[0]]['graded']) ){
							// $essay_post_id = end($user_quiz_meta_[$quiz_attempts_key[0]]['graded'])['post_id'];
							// }
							// else{
							// continue;
							// }

							// $question_post_id = (int) get_post_meta( $essay_post_id, 'question_post_id', true );
							// $question_post_title = get_the_title($question_post_id);
							// $essay_comments_number = get_comments_number($essay_post_id);
							// $last_essay_attempted_details = learndash_get_essay_details($essay_post_id);
							// $quizzes_data['essays_data'][] = array("essay_id"        => $essay_post_id,
							// "essay_url"          => get_post_permalink($essay_post_id),
							// "question_post_id"   => $question_post_id,
							// "question_post_title" => $question_post_title,
							// "points"         => $last_essay_attempted_details['points'],
							// "status"             => $last_essay_attempted_details['status'],
							// "comments"           => $essay_comments_number
							// );
							// }
							// }
							// }

							$lessons_data['quizzes_data'][] = $quizzes_data;
						}
					}

					/**  Get assignments of lesson */
					$lesson_assignments = learndash_get_user_assignments( $lesson_id, $user_id );
					if ( is_array( $lesson_assignments ) && ! empty( $lesson_assignments ) ) {
						$points_enabled = learndash_get_setting( $lesson_id, 'lesson_assignment_points_enabled' );
						if ( 'on' === $points_enabled ) {
							$is_points_enabled = 1;
							$assignment_points = learndash_get_setting( $lesson_id, 'lesson_assignment_points_amount' );
						} else {
							$is_points_enabled = 0;
							$assignment_points = 0;
						}
						// Loop Assignments.
						foreach ( $lesson_assignments as $lesson_assignment ) {
							$assignment_id = $lesson_assignment->ID;
							if ( learndash_is_assignment_approved_by_meta( $assignment_id ) ) {
								$is_approved = true;
							} else {
								$is_approved = false;
							}
							$assignments_data = [
								'assignment_id'        => $assignment_id,
								'assignment_name'      => $lesson_assignment->post_title,
								'assignment_url'       => get_post_permalink( $assignment_id ),
								'assignment_media_url' => get_post_meta( $assignment_id, 'file_link', true ),
								'is_approved'          => $is_approved,
								'is_points_enabled'    => $is_points_enabled,
								'assignment_points'    => $assignment_points,
								'comments'             => get_comments_number( $assignment_id ),
							];

							$assignments_data['is_gf_enabled'] = $is_gravity_form_active;
							if ( $is_gravity_form_active ) {
								$gf_page_url                      = get_post_meta( $lesson_id, 'gf-page-url', true );
								$gf_form_id                       = get_post_meta( $lesson_id, 'gf-form-id', true );
								$gf_form_field_id                 = get_post_meta( $lesson_id, 'gf-form-field-id', true );
								$gf_form_field_team_member_id_key = get_post_meta( $lesson_id, 'gf-form-field-team-member-id', true );
								$has_gf_entries                   = false;
								$gf_chosen_text                   = '';

								if (
									'on' == learndash_get_setting( $lesson_id, 'lesson_assignment_upload' )
									&& ! empty( $gf_page_url )
									&& ! empty( $gf_form_id )
									&& ! empty( $gf_form_field_id )
								) {
									$gf_form                        = \GFAPI::get_form( $gf_form_id );
									$gf_hidden_assignment_id_field  = 0;
									$gf_hidden_team_member_id_field = 0;
									// Let's iterate through the "fields" array and find our fields there.
									foreach ( $gf_form['fields'] as $field ) {
										if ( isset( $field['inputName'] ) && $field['inputName'] === 'assignment_id' ) {
											$gf_hidden_assignment_id_field = $field['id'];
										} elseif ( isset( $field['inputName'] ) && $field['inputName'] === 'team_member_id' ) {
											$gf_hidden_team_member_id_field = $field['id'];
										}
									}

									// Check if the hidden team_member_id field set to form. if set then use that user id, else use gf-form-field-team-member-id meta set under the post(lesson/topic) settings.
									if ( isset( $gf_hidden_team_member_id_field ) && is_int( $gf_hidden_team_member_id_field ) ) {
										$gf_form_field_team_member_id_key = $gf_hidden_team_member_id_field;
									}

									$assignments_data['is_gf_enabled_for_post'] = true;
									$assignments_data['gf_assignment_status']   = 'pending';
									$search_criteria                            = [];
									$search_criteria['field_filters'][]         = [
										'key'   => $gf_form_field_team_member_id_key,
										'value' => $user_id,
									];


									if ( $gf_hidden_assignment_id_field ) {
										$search_criteria['field_filters'][] = [
											'key'   => $gf_hidden_assignment_id_field,
											'value' => $assignments_data['assignment_id'],
										];
									}

									$entries        = \GFAPI::get_entries( $gf_form_id, $search_criteria );
									$has_gf_entries = false;
									$choice_text    = '';
									if ( is_array( $entries ) && ! empty( $entries ) ) {
										$has_gf_entries                     = true;
										$assignments_data['has_gf_entries'] = $has_gf_entries;

										$selected_value = $entries[0][ $gf_form_field_id ];
										$field_one      = \GFAPI::get_field( $gf_form_id, $gf_form_field_id );
										if ( $selected_value && is_array( $field_one->choices ) ) {
											$choices_key = array_search( $selected_value, array_column( $field_one->choices, 'value' ) );
											$choice_text = $field_one->choices[ $choices_key ]['text'];
										}
										if ( $choice_text === 'Competent' || $choice_text === 'Satisfactory' ) {
											$assignments_data['gf_assignment_status'] = 'approved';
										} else {
											$assignments_data['gf_assignment_status'] = 'resubmit';
										}
									}

									// Appending the assignment_id and team_member_id to GF url.
									$gf_page_url                     = $gf_page_url . ( parse_url( $gf_page_url, PHP_URL_QUERY ) ? '&' : '?' )
										. 'assignment_id=' . $assignment_id . '&team_member_id=' . $user_id
										. '&team_member_email=' . $user->user_email;
									$assignments_data['gf_page_url'] = $gf_page_url;
								}
							}

							$lessons_data['assignments_data'][] = $assignments_data;
						}
					}

					$user_courses_lessons_quizzes['lessons_data'][] = $lessons_data;
				}
			}


			/**  Get quiz of course */
			// check if course has quiz.
			$course_quizzes = learndash_get_course_quiz_list( $course_id, $user_id );

			if ( ( is_array( $course_quizzes ) ) && ( ! empty( $course_quizzes ) ) ) {
				// Loop Quizzes.
				foreach ( $course_quizzes as $quiz ) {
					$quiz_id  = $quiz['post']->ID;
					$pt       = 0;
					$attempts = learndash_get_user_quiz_attempts( $user_id, $quiz_id );

					if (
						$quiz_id
						&& is_array( $attempts )
						&& count( $attempts )
					) {
						// $pt = do_shortcode("[courseinfo show='cumulative_percentage'  user_id='".$user_id."' course_id='".$course_id."']");
						$pt         = do_shortcode( '[quizinfo show="percentage"  user_id="' . $user_id . '" quiz="' . $quiz_id . '"]' );
						$quiz_score = $pt ? $pt . '%' : '-';
					} else {
						$quiz_score = '-';
					}

					$certificate_link = '';
					$pro_quizid       = $statistic_ref_id = $quiz_pro_statistics_on = 0;
					if ( $quiz_id ) {
						if ( $quiz['status'] == 'completed' ) {
							$cert = learndash_certificate_details( $quiz_id, $user_id );
							if ( ( isset( $cert['certificateLink'] ) ) && ( ! empty( $cert['certificateLink'] ) ) ) {
								$certificate_link = $cert['certificateLink'];
							}
						}

						if ( $pt >= 0 ) {
							$quiz_pro_statistics_on = learndash_get_setting( $quiz_id, 'statisticsOn', true );

							if ( $has_show_all_quiz_attempts && ! empty( array_keys( array_column( $user_quiz_meta, 'quiz' ), $quiz_id ) ) ) {
								$quiz_attempts_key = array_keys( array_column( $user_quiz_meta, 'quiz' ), $quiz_id );

								$user_quiz_meta_ = $user_quiz_meta;
							} else {
								$user_quiz_meta_ = array_reverse( $user_quiz_meta );
								$key             = array_search( $quiz_id, array_column( $user_quiz_meta_, 'quiz' ) );

								$pro_quizid       = isset( $user_quiz_meta_[ $key ]['pro_quizid'] ) ?? 0;
								$statistic_ref_id = isset( $user_quiz_meta_[ $key ]['statistic_ref_id'] ) ?? 0;

								$quiz_attempts_key = [ $key ];
							}
						}
					}

					$quizzes_data = [
						'quiz_id'            => $quiz_id,
						'quiz_name'          => $quiz['post']->post_title,
						'quiz_url'           => get_post_permalink( $quiz_id ),
						'quiz_score'         => $quiz_score,
						'quiz_certificate'   => $certificate_link,
						'quiz_statistics_on' => $quiz_pro_statistics_on,
						'pro_quizid'         => $pro_quizid,
						'statistic_ref_id'   => $statistic_ref_id,
						'time_spent'         => Timer::get_time_spent( $quiz['post'], $user->ID ),
					];

					// Quiz Attempts.
					$quizzes_data['quiz_attempts'] = [];

					foreach ( $quiz_attempts_key as $quiz_attempt_key ) {
						$quiz_score = $pro_quizid = $statistic_ref_id = 0;
						if ( isset( $user_quiz_meta_[ $quiz_attempt_key ] ) && $user_quiz_meta_[ $quiz_attempt_key ]['quiz'] == $quiz_id ) {
							$quiz_score       = $user_quiz_meta_[ $quiz_attempt_key ]['percentage'];
							$pro_quizid       = $user_quiz_meta_[ $quiz_attempt_key ]['pro_quizid'];
							$statistic_ref_id = $user_quiz_meta_[ $quiz_attempt_key ]['statistic_ref_id'];
						}
						$quiz_score = ! empty( $quiz_score ) ? $quiz_score . '%' : '-';

						$certificate_link_ = '';
						if ( ! empty( $certificate_link ) ) {
							$certificate_link_ = $certificate_link . '&time=' . $user_quiz_meta_[ $quiz_attempt_key ]['time'];
						}

						// Essays.
						$essays_data = [];
						if ( $quiz_id /* && $quiz['status'] != 'notcompleted' */ ) {
							$quiz_questions = learndash_get_quiz_questions( $quiz_id );
							foreach ( $quiz_questions as $quiz_question_id => $quiz_question_pro_id ) {
								$question_type = get_post_meta( $quiz_question_id, 'question_type', true );

								if (
									$question_type === 'essay'
									&& isset( $user_quiz_meta_[ $quiz_attempts_key[0] ]['graded'][ $quiz_question_pro_id ] )
								) {
									$essay_post_id                = $user_quiz_meta_[ $quiz_attempts_key[0] ]['graded'][ $quiz_question_pro_id ]['post_id'];
									$question_post_id             = (int) get_post_meta( $essay_post_id, 'question_post_id', true );
									$question_post_title          = get_the_title( $question_post_id );
									$essay_comments_number        = get_comments_number( $essay_post_id );
									$last_essay_attempted_details = learndash_get_essay_details( $essay_post_id );
									$essays_data[]                = [
										'essay_id'         => $essay_post_id,
										'essay_url'        => get_post_permalink( $essay_post_id ),
										'question_post_id' => $question_post_id,
										'question_post_title' => $question_post_title,
										'points'           => $last_essay_attempted_details['points'],
										'status'           => $last_essay_attempted_details['status'],
										'comments'         => $essay_comments_number,
									];
								}
							}
						}

						$quizzes_data['quiz_attempts'][] = [
							'quiz_id'            => $quiz_id,
							'quiz_name'          => $quiz['post']->post_title,
							'quiz_url'           => get_post_permalink( $quiz_id ),
							'quiz_score'         => $quiz_score,
							'quiz_certificate'   => $certificate_link_,
							'quiz_statistics_on' => $quiz_pro_statistics_on,
							'pro_quizid'         => $pro_quizid,
							'statistic_ref_id'   => $statistic_ref_id,
							'essays_data'        => $essays_data,
						];
					}



					$user_courses_lessons_quizzes['course_quizzes_data'][] = $quizzes_data;
				}
			}
		}
	}
}

// user meta of unlock/lock lesson of course.
$lock_courses_lessons = get_user_meta( $user_courses_lessons_quizzes['user_id'], 'lock_courses_lessons', true );
if ( ! empty( $lock_courses_lessons ) && isset( $lock_courses_lessons[ $user_courses_lessons_quizzes['course_id'] ] ) ) {
	$lock_courses_lessons = $lock_courses_lessons[ $user_courses_lessons_quizzes['course_id'] ];
} else {
	$lock_courses_lessons = [];
}

?>

<div class="div-team-container">
	<h5 class="course-progress-percentage"><?php esc_html_e( 'Course: ' . ( isset( $user_courses_lessons_quizzes['course_name'] ) ? $user_courses_lessons_quizzes['course_name'] : '' ) . ' ( ' . ( isset( $user_courses_lessons_quizzes['course_progress_percentage'] ) ? $user_courses_lessons_quizzes['course_progress_percentage'] : '0' ) . '% )', 'learndash-groups-plus' ); ?>
	</h5>
	<h5 class="course-time-spent">
		<?php
		esc_html_e( 'Time spent: ', 'learndash-groups-plus' );
		echo do_shortcode( '[time user-id="' . $user_courses_lessons_quizzes['user_id'] . '" course-id="' . $user_courses_lessons_quizzes['course_id'] . '"]' );
		?>
	</h5>
	<?php if ( ! empty( $user_courses_lessons_quizzes['course_certificate'] ) ) : ?>
		<p><?php _e( 'Certificate', 'learndash-groups-plus' ); ?>: <a class="btn" href="<?php echo esc_url( $user_courses_lessons_quizzes['course_certificate'] ); ?>" target="_blank"><?php _e( 'Print PDF', 'learndash-groups-plus' ); ?></a>
		</p>
	<?php endif; ?>
</div>

<div class="div-table-container">
	<div class="div-table lesson-table" data-nonce="<?php echo esc_attr( wp_create_nonce( 'learndash-groups-plus-lesson-table-nonce' ) ); ?>" data-course-id="<?php esc_attr_e( $user_courses_lessons_quizzes['course_id'], 'learndash-groups-plus' ); ?>" data-user-id="<?php esc_attr_e( $user_courses_lessons_quizzes['user_id'], 'learndash-groups-plus' ); ?>">
		<div class="div-table-row-header">
			<div class="div-table-col ld-lock-unlock-header" align="center">
				<?php esc_html_e( 'Lock', 'learndash-groups-plus' ); ?>
			</div>
			<div class="div-table-col" align="center">
				<?php printf( esc_html( '%s' ), esc_attr( LearnDash_Custom_Label::get_label( 'lesson' ) ) ); ?>
			</div>
			<div class="div-table-col"><?php esc_html_e( 'Completed On', 'learndash-groups-plus' ); ?></div>
			<div class="div-table-col"><?php esc_html_e( 'Time Spent', 'learndash-groups-plus' ); ?></div>
			<span class="learndash-groups-plus-icon-arrow main-list"></span>
		</div>
		<?php
		if ( empty( $user_courses_lessons_quizzes['lessons_data'] ) ) {
			?>
			<div class="div-table-row-parent">
				<div class="div-table-row" align="center">
					<?php _e( 'No record found.', 'learndash-groups-plus' ); ?>
				</div>
			</div>
			<?php
		}
		if ( ! empty( $user_courses_lessons_quizzes['lessons_data'] ) ) {
			foreach ( $user_courses_lessons_quizzes['lessons_data'] as $lesson_row ) {
				?>
				<div class="div-table-row-parent">
					<div class="div-table-row" style="color:<?php echo $lesson_row['lesson_color']; ?>" data-lesson-id="<?php esc_attr_e( $lesson_row['lesson_id'], 'learndash-groups-plus' ); ?>">
						<span class="ld-lock-unlock"><i class="fas <?php echo ( ! empty( $lock_courses_lessons ) && $lock_courses_lessons['lesson_id'] == $lesson_row['lesson_id'] ? 'fa-lock' : 'fa-unlock' ); ?>"></i></span>
						<div class="div-table-col"><a href="<?php esc_attr_e( $lesson_row['lesson_url'], 'learndash-groups-plus' ); ?>" target="_blank"><?php esc_html_e( $lesson_row['lesson_name'], 'learndash-groups-plus' ); ?></a>
						</div>
						<div class="div-table-col"><?php esc_html_e( $lesson_row['lesson_completion_time'], 'learndash-groups-plus' ); ?></div>
						<div class="div-table-col"><?php esc_html_e( $lesson_row['time_spent'], 'learndash-groups-plus' ); ?></div>

						<?php if ( ! empty( $lesson_row['topics_data'] ) || ! empty( $lesson_row['quizzes_data'] ) || ! empty( $lesson_row['assignments_data'] ) ) : ?>
							<span class="learndash-groups-plus-icon-arrow"></span>
						<?php endif; ?>

					</div>
					<?php if ( ! empty( $lesson_row['topics_data'] ) || ! empty( $lesson_row['quizzes_data'] ) || ! empty( $lesson_row['assignments_data'] ) ) : ?>
						<div class="lesson-content">
							<?php if ( ! empty( $lesson_row['topics_data'] ) ) : ?>
								<h6>Topics:</h6>
								<div class="div-table topic-table">
									<div class="div-table-row-header">
										<div class="div-table-col" align="center">
											<?php printf( esc_html( '%s' ), esc_attr( LearnDash_Custom_Label::get_label( 'topic' ) ) ); ?>
										</div>
										<div class="div-table-col"><?php esc_html_e( 'Completed On', 'learndash-groups-plus' ); ?></div>
										<div class="div-table-col"><?php esc_html_e( 'Time Spent', 'learndash-groups-plus' ); ?></div>
									</div>
									<?php foreach ( $lesson_row['topics_data'] as $topic_row ) { ?>
										<div class="div-table-row-parent">
											<div class="div-table-row" style="color:<?php echo $topic_row['topic_color']; ?>">
												<div class="div-table-col">
													<a href="<?php esc_attr_e( $topic_row['topic_url'], 'learndash-groups-plus' ); ?>" target="_blank"><?php esc_html_e( $topic_row['topic_name'], 'learndash-groups-plus' ); ?></a>
												</div>
												<div class="div-table-col"><?php esc_html_e( $topic_row['topic_completed_date'], 'learndash-groups-plus' ); ?></div>
												<div class="div-table-col"><?php esc_html_e( $topic_row['time_spent'], 'learndash-groups-plus' ); ?></div>
												<?php if ( ! empty( $topic_row['quizzes_data'] ) || ! empty( $topic_row['assignments_data'] ) ) : ?>
													<span class="learndash-groups-plus-icon-arrow"></span>
												<?php endif; ?>
											</div>
											<?php if ( ! empty( $topic_row['quizzes_data'] ) || ! empty( $topic_row['assignments_data'] ) ) : ?>
												<div class="topic-content">
													<?php if ( ! empty( $topic_row['quizzes_data'] ) ) : ?>
														<h6>
															<?php esc_html_e( 'Topic Quizzes', 'learndash-groups-plus' ); ?>:
														</h6>
														<div class="div-table quiz-table">
															<div class="div-table-row-header">
																<div class="div-table-col" align="center">
																	<?php printf( esc_html( '%s' ), esc_attr( LearnDash_Custom_Label::get_label( 'quiz' ) ) ); ?>
																</div>
																<div class="div-table-col">
																	<?php esc_html_e( 'Score', 'learndash-groups-plus' ); ?>
																</div>
																<div class="div-table-col">
																	<?php esc_html_e( 'Statistics', 'learndash-groups-plus' ); ?>
																</div>
																<div class="div-table-col">
																	<?php esc_html_e( 'Time Spent', 'learndash-groups-plus' ); ?>
																</div>
																<div class="div-table-col">
																	<?php esc_html_e( 'Certificate', 'learndash-groups-plus' ); ?>
																</div>
															</div>
															<?php foreach ( $topic_row['quizzes_data'] as $quiz_row ) { ?>
																<div class="div-table-row-parent">
																	<?php foreach ( $quiz_row['quiz_attempts'] as $quiz_attempt ) { ?>
																		<div class="div-table-row">
																			<div class="div-table-col">
																				<a href="<?php esc_attr_e( $quiz_attempt['quiz_url'], 'learndash-groups-plus' ); ?>" target="_blank">
																					<?php esc_html_e( $quiz_attempt['quiz_name'], 'learndash-groups-plus' ); ?>
																				</a>
																			</div>
																			<div class="div-table-col"><?php esc_html_e( $quiz_attempt['quiz_score'], 'learndash-groups-plus' ); ?>
																			</div>
																			<div class="div-table-col">
																				<?php
																				if ( SharedFunctions::is_quiz_notification_for_ld_active() && $quiz_attempt['quiz_statistics_on'] && $quiz_attempt['statistic_ref_id'] ) {
																					echo '<a class="elc_ldquiz_load" data-statistic-nonce="' . wp_create_nonce( 'elc_ldquizntf_statistic' ) . '" data-statistic-ref-id="' . esc_attr( $quiz_attempt['statistic_ref_id'] ) . '" data-quiz-id="' . esc_attr( $quiz_attempt['pro_quizid'] ) . '" ><span class="ld-icon ld-icon-assignment"></span></a>';
																				} elseif ( $quiz_attempt['quiz_statistics_on'] && $quiz_attempt['statistic_ref_id'] ) {
																					?>
																					<a class="user_statistic" data-statistic-nonce="<?php echo esc_attr( wp_create_nonce( 'statistic_nonce_' . $quiz_attempt['statistic_ref_id'] . '_' . get_current_user_id() . '_' . $user_id ) ); ?>" data-user-id="<?php esc_attr_e( $user_id, 'learndash-groups-plus' ); ?>" data-quiz-id="<?php esc_attr_e( $quiz_attempt['pro_quizid'], 'learndash-groups-plus' ); ?>" data-ref-id="<?php esc_attr_e( $quiz_attempt['statistic_ref_id'], 'learndash-groups-plus' ); ?>" href="#"><span class="ld-icon ld-icon-assignment"></span></a>
																					<?php
																				} else {
																					echo '-';
																				}
																				?>
																			</div>
																			<div class="div-table-col"><?php _e( $quiz_row['time_spent'], 'learndash-groups-plus' ); ?></div>
																			<div class="div-table-col">
																				<?php if ( ! empty( $quiz_attempt['quiz_certificate'] ) ) { ?>
																					<a class="certificate-icon" href="<?php esc_attr_e( $quiz_attempt['quiz_certificate'], 'learndash-groups-plus' ); ?>" target="_blank">
																						<img width="50px" src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/certificate.png'; ?>
													alt=" <?php _e( 'Print PDF', 'learndash-groups-plus' ); ?>" srcset="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/certificate.svg'; ?>>
												<path id=" Layer_1" />
																						</img>
																					</a>
																					<?php
																				} else {
																					echo '-';
																				}
																				?>
																			</div>
																		</div>

																		<?php if ( ! empty( $quiz_attempt['essays_data'] ) ) : ?>
																			<div class="quiz-content">
																				<div class="div-table essay-table">
																					<div class="div-table-row-header">
																						<div class="div-table-col" align="center">
																							<?php esc_html_e( 'Essays', 'learndash-groups-plus' ); ?>
																						</div>
																						<div class="div-table-col">
																							<?php esc_html_e( 'Comments', 'learndash-groups-plus' ); ?>
																						</div>
																						<div class="div-table-col">
																							<?php esc_html_e( 'Status', 'learndash-groups-plus' ); ?>
																						</div>
																						<div class="div-table-col">
																							<?php esc_html_e( 'Points', 'learndash-groups-plus' ); ?>
																						</div>
																					</div>

																					<?php foreach ( $quiz_attempt['essays_data'] as $essay_row ) : ?>
																						<div class="div-table-row">
																							<div class="div-table-col">
																								<a href="<?php echo esc_url( $essay_row['essay_url'] ); ?>" target="_blank">
																									<?php esc_html_e( $essay_row['question_post_title'], 'learndash-groups-plus' ); ?>
																								</a>
																							</div>
																							<div class="div-table-col">
																								<a href="<?php echo esc_url( $essay_row['essay_url'] ); ?>" target="_blank">
																									<span class="ld-icon ld-icon-comments"></span>
																									<?php esc_html_e( $essay_row['comments'], 'learndash-groups-plus' ); ?>
																								</a>
																							</div>
																							<div class="div-table-col">
																								<?php
																								if ( $essay_row['status'] === 'graded' ) :
																									echo wp_kses_post( learndash_status_bubble( $essay_row['status'], 'essay', false ) );
																								else :
																									?>
																									<p class="essay-status">
																										<input type="number" name="essay_points[<?php echo $essay_row['essay_id']; ?>]" min="0" max="<?php _e( $essay_row['points']['total'], 'learndash-groups-plus' ); ?>" step="1" /> /
																										<?php _e( $essay_row['points']['total'], 'learndash-groups-plus' ); ?>
																										<button type="button" class="apr_essay_btn" data-essay-id="<?php echo esc_attr( $essay_row['essay_id'] ); ?>"><?php _e( 'Approve', 'learndash-groups-plus' ); ?></button>
																									</p>
																								<?php endif; ?>
																							</div>
																							<div class="div-table-col">
																								<?php esc_html_e( $essay_row['points']['awarded'] . '/' . $essay_row['points']['total'], 'learndash-groups-plus' ); ?>
																							</div>
																						</div>
																						<?php
																					endforeach; // essays_data.
																					?>
																				</div>
																			</div>
																			<?php
																		endif; // essays_data.
																		?>

																	<?php } ?>

																</div>
															<?php } ?>
														</div>
													<?php endif; ?>

													<?php if ( ! empty( $topic_row['assignments_data'] ) ) : ?>
														<h6><?php _e( 'Topic Assignments:', 'learndash-groups-plus' ); ?></h6>
														<div class="div-table assignment-table">
															<div class="div-table-row-header">
																<div class="div-table-col div-assignment-name" align="center">
																	<?php esc_html_e( 'File Name', 'learndash-groups-plus' ); ?>
																</div>
																<div class="div-table-col div-comments" align="center">
																	<?php esc_html_e( 'Comments', 'learndash-groups-plus' ); ?>
																</div>
																<div class="div-table-col div-approve" align="center">
																	<?php esc_html_e( 'Action', 'learndash-groups-plus' ); ?>
																</div>
															</div>
															<?php foreach ( $topic_row['assignments_data'] as $assignment_row ) { ?>
																<div class="div-table-row">
																	<div class="div-table-col div-assignment-name"><a href="<?php echo esc_url( $assignment_row['assignment_media_url'] ); ?>" target="_blank"><?php esc_html_e( $assignment_row['assignment_name'], 'learndash-groups-plus' ); ?></a>
																	</div>
																	<div class="div-table-col div-comments">
																		<a href="<?php echo esc_url( $assignment_row['assignment_url'] ); ?>" target="_blank">
																			<span class="ld-icon ld-icon-comments"></span>
																			<?php esc_html_e( $assignment_row['comments'], 'learndash-groups-plus' ); ?>
																		</a>
																	</div>
																	<div class="div-table-col div-approve <?php _e( $assignment_row['is_approved'] ? 'approved' : '', 'learndash-groups-plus' ); ?>" align="center">
																		<?php
																		if ( $assignment_row['is_gf_enabled_for_post'] ) {
																			if ( $assignment_row['is_approved'] || $assignment_row['gf_assignment_status'] === 'approved' ) :
																				?>
																				<a href="javascript:;" id="assignment-id-<?php echo esc_attr( $assignment_row['assignment_id'] ); ?>" class="btn-learndash-groups-plus-assignment-approved apr_btn" data-assignment-id="<?php echo esc_attr( $assignment_row['assignment_id'] ); ?>">
																					<?php _e( 'Approved', 'learndash-groups-plus' ); ?>
																				</a>
																				<?php
																			elseif ( $assignment_row['gf_assignment_status'] === 'resubmit' ) :
																				?>
																				<a href="<?php echo esc_url( $assignment_row['gf_page_url'] ); ?>" target="_blank" class="btn-learndash-groups-plus-assignment-rejected">
																					<?php _e( 'Resubmit', 'learndash-groups-plus' ); ?>
																				</a>
																				<?php
																			else :
																				?>
																				<a href="<?php echo esc_url( $assignment_row['gf_page_url'] ); ?>" target="_blank" class="btn-learndash-groups-plus-assignment-grade">
																					<?php _e( 'Grade', 'learndash-groups-plus' ); ?>
																				</a>
																				<?php
																			endif;
																		} else {
																			if ( $assignment_row['is_approved'] ) :
																				_e( 'Approved', 'learndash-groups-plus' );
																			else :
																				if ( $assignment_row['is_points_enabled'] ) :
																					?>
																					<p>
																						<input type="number" name="assignment[<?php echo $assignment_row['assignment_id']; ?>]" min="0" max="<?php _e( $assignment_row['assignment_points'], 'learndash-groups-plus' ); ?>" step="1" /> /
																						<?php _e( $assignment_row['assignment_points'], 'learndash-groups-plus' ); ?>
																					</p>
																				<?php endif; ?>

																				<button type="button" class="apr_btn" data-assignment-id="<?php echo esc_attr( $assignment_row['assignment_id'] ); ?>"><?php _e( 'Approve', 'learndash-groups-plus' ); ?></button>
																			<?php endif; ?>
																		<?php } ?>
																	</div>
																</div>
															<?php } ?>
														</div>
													<?php endif; ?>

												</div>
											<?php endif; ?>
										</div>
									<?php } ?>

								</div>
							<?php endif; ?>

							<?php if ( ! empty( $lesson_row['quizzes_data'] ) ) : ?>
								<h6><?php esc_html_e( 'Quizzes', 'learndash-groups-plus' ); ?>:</h6>
								<div class="div-table quiz-table">
									<div class="div-table-row-header">
										<div class="div-table-col" align="center">
											<?php printf( esc_html( '%s' ), esc_attr( LearnDash_Custom_Label::get_label( 'quiz' ) ) ); ?>
										</div>
										<div class="div-table-col"><?php esc_html_e( 'Score', 'learndash-groups-plus' ); ?></div>
										<div class="div-table-col">
											<?php esc_html_e( 'Statistics', 'learndash-groups-plus' ); ?>
										</div>
										<div class="div-table-col">
											<?php esc_html_e( 'Time Spent', 'learndash-groups-plus' ); ?>
										</div>
										<div class="div-table-col"><?php esc_html_e( 'Certificate', 'learndash-groups-plus' ); ?>
										</div>
									</div>
									<?php foreach ( $lesson_row['quizzes_data'] as $quiz_row ) { ?>
										<div class="div-table-row-parent">
											<?php foreach ( $quiz_row['quiz_attempts'] as $quiz_attempt ) { ?>
												<div class="div-table-row">
													<div class="div-table-col">
														<a href="<?php esc_attr_e( $quiz_attempt['quiz_url'], 'learndash-groups-plus' ); ?>" target="_blank">
															<?php esc_html_e( $quiz_attempt['quiz_name'], 'learndash-groups-plus' ); ?>
														</a>
													</div>
													<div class="div-table-col"><?php esc_html_e( $quiz_attempt['quiz_score'], 'learndash-groups-plus' ); ?></div>
													<div class="div-table-col">
														<?php
														if ( SharedFunctions::is_quiz_notification_for_ld_active() && $quiz_attempt['quiz_statistics_on'] && $quiz_attempt['statistic_ref_id'] ) {
															echo '<a class="elc_ldquiz_load" data-statistic-nonce="' . wp_create_nonce( 'elc_ldquizntf_statistic' ) . '" data-statistic-ref-id="' . esc_attr( $quiz_attempt['statistic_ref_id'] ) . '" data-quiz-id="' . esc_attr( $quiz_attempt['pro_quizid'] ) . '"><span class="ld-icon ld-icon-assignment"></span></a>';
														} elseif ( $quiz_attempt['quiz_statistics_on'] && $quiz_attempt['statistic_ref_id'] ) {
															?>
															<a class="user_statistic" data-statistic-nonce="<?php echo esc_attr( wp_create_nonce( 'statistic_nonce_' . $quiz_attempt['statistic_ref_id'] . '_' . get_current_user_id() . '_' . $user_id ) ); ?>" data-user-id="<?php esc_attr_e( $user_id, 'learndash-groups-plus' ); ?>" data-quiz-id="<?php esc_attr_e( $quiz_attempt['pro_quizid'], 'learndash-groups-plus' ); ?>" data-ref-id="<?php esc_attr_e( $quiz_attempt['statistic_ref_id'], 'learndash-groups-plus' ); ?>" href="#"><span class="ld-icon ld-icon-assignment"></span></a>
															<?php
														} else {
															echo '-';
														}
														?>
													</div>
													<div class="div-table-col"><?php _e( $quiz_row['time_spent'], 'learndash-groups-plus' ); ?></div>
													<div class="div-table-col">
														<?php
														if ( ! empty( $quiz_attempt['quiz_certificate'] ) ) {
															?>
															<a class="certificate-icon" href="<?php esc_attr_e( $quiz_attempt['quiz_certificate'], 'learndash-groups-plus' ); ?>" target="_blank">
																<img width="50px" src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/certificate.png'; ?>
										alt=" <?php _e( 'Print PDF', 'learndash-groups-plus' ); ?>" srcset="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/certificate.svg'; ?>>
									<path id=" Layer_1" />
																</img>
															</a>
															<?php
														} else {
															echo '-';
														}
														?>
													</div>
												</div>

												<?php if ( ! empty( $quiz_attempt['essays_data'] ) ) : ?>
													<div class="quiz-content">
														<div class="div-table essay-table">
															<div class="div-table-row-header">
																<div class="div-table-col" align="center">
																	<?php esc_html_e( 'Essays', 'learndash-groups-plus' ); ?>
																</div>
																<div class="div-table-col">
																	<?php esc_html_e( 'Comments', 'learndash-groups-plus' ); ?>
																</div>
																<div class="div-table-col">
																	<?php esc_html_e( 'Status', 'learndash-groups-plus' ); ?>
																</div>
																<div class="div-table-col">
																	<?php esc_html_e( 'Points', 'learndash-groups-plus' ); ?>
																</div>
															</div>

															<?php foreach ( $quiz_attempt['essays_data'] as $essay_row ) : ?>
																<div class="div-table-row">
																	<div class="div-table-col">
																		<a href="<?php echo esc_url( $essay_row['essay_url'] ); ?>" target="_blank">
																			<?php esc_html_e( $essay_row['question_post_title'], 'learndash-groups-plus' ); ?>
																		</a>
																	</div>
																	<div class="div-table-col">
																		<a href="<?php echo esc_url( $essay_row['essay_url'] ); ?>" target="_blank">
																			<span class="ld-icon ld-icon-comments"></span>
																			<?php esc_html_e( $essay_row['comments'], 'learndash-groups-plus' ); ?>
																		</a>
																	</div>
																	<div class="div-table-col">
																		<?php
																		if ( $essay_row['status'] === 'graded' ) :
																			echo wp_kses_post( learndash_status_bubble( $essay_row['status'], 'essay', false ) );
																		else :
																			?>
																			<p class="essay-status">
																				<input type="number" name="essay_points[<?php echo $essay_row['essay_id']; ?>]" min="0" max="<?php _e( $essay_row['points']['total'], 'learndash-groups-plus' ); ?>" step="1" /> /
																				<?php _e( $essay_row['points']['total'], 'learndash-groups-plus' ); ?>
																				<button type="button" class="apr_essay_btn" data-essay-id="<?php echo esc_attr( $essay_row['essay_id'] ); ?>"><?php _e( 'Approve', 'learndash-groups-plus' ); ?></button>
																			</p>
																		<?php endif; ?>
																	</div>
																	<div class="div-table-col">
																		<?php esc_html_e( $essay_row['points']['awarded'] . '/' . $essay_row['points']['total'], 'learndash-groups-plus' ); ?>
																	</div>
																</div>
																<?php
															endforeach; // essays_data.
															?>
														</div>
													</div>
													<?php
												endif; // essays_data.
												?>

											<?php } ?>

										</div>
									<?php } ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $lesson_row['assignments_data'] ) ) : ?>
								<h6><?php _e( 'Assignments:', 'learndash-groups-plus' ); ?></h6>
								<div class="div-table assignment-table">
									<div class="div-table-row-header">
										<div class="div-table-col div-assignment-name" align="center">
											<?php esc_html_e( 'File Name', 'learndash-groups-plus' ); ?>
										</div>
										<div class="div-table-col div-comments" align="center">
											<?php esc_html_e( 'Comments', 'learndash-groups-plus' ); ?>
										</div>
										<div class="div-table-col div-approve" align="center">
											<?php esc_html_e( 'Action', 'learndash-groups-plus' ); ?>
										</div>
									</div>
									<?php
									foreach ( $lesson_row['assignments_data'] as $assignment_row ) {
										?>
										<div class="div-table-row">
											<div class="div-table-col div-assignment-name"><a href="<?php echo esc_url( $assignment_row['assignment_media_url'] ); ?>" target="_blank"><?php esc_html_e( $assignment_row['assignment_name'], 'learndash-groups-plus' ); ?></a>
											</div>
											<div class="div-table-col div-comments">
												<a href="<?php echo esc_url( $assignment_row['assignment_url'] ); ?>" target="_blank">
													<span class="ld-icon ld-icon-comments"></span>
													<?php esc_html_e( $assignment_row['comments'], 'learndash-groups-plus' ); ?>
												</a>
											</div>
											<div class="div-table-col div-approve <?php _e( $assignment_row['is_approved'] ? 'approved' : '', 'learndash-groups-plus' ); ?>" align="center">
												<?php
												if ( ! empty( $assignment_row['is_gf_enabled_for_post'] ) && $assignment_row['is_gf_enabled_for_post'] ) {
													if ( $assignment_row['is_approved'] || $assignment_row['gf_assignment_status'] === 'approved' ) :
														?>
														<a href="javascript:;" id="assignment-id-<?php echo esc_attr( $assignment_row['assignment_id'] ); ?>" class="btn-learndash-groups-plus-assignment-approved apr_btn" data-assignment-id="<?php echo esc_attr( $assignment_row['assignment_id'] ); ?>">
															<?php _e( 'Approved', 'learndash-groups-plus' ); ?>
														</a>
														<?php
													elseif ( $assignment_row['gf_assignment_status'] === 'resubmit' ) :
														?>
														<a href="<?php echo esc_url( $assignment_row['gf_page_url'] ); ?>" target="_blank" class="btn-learndash-groups-plus-assignment-rejected">
															<?php _e( 'Resubmit', 'learndash-groups-plus' ); ?>
														</a>
														<?php
													else :
														?>
														<a href="<?php echo esc_url( $assignment_row['gf_page_url'] ); ?>" target="_blank" class="btn-learndash-groups-plus-assignment-grade">
															<?php _e( 'Grade', 'learndash-groups-plus' ); ?>
														</a>
														<?php
													endif;
												} else {
													if ( $assignment_row['is_approved'] ) :
														_e( 'Approved', 'learndash-groups-plus' );
													else :
														if ( $assignment_row['is_points_enabled'] ) :
															?>
															<p>
																<input type="number" name="assignment[<?php echo $assignment_row['assignment_id']; ?>]" min="0" max="<?php _e( $assignment_row['assignment_points'], 'learndash-groups-plus' ); ?>" step="1" /> /
																<?php _e( $assignment_row['assignment_points'], 'learndash-groups-plus' ); ?>
															</p>
														<?php endif; ?>
														<button type="button" class="apr_btn" data-assignment-id="<?php echo esc_attr( $assignment_row['assignment_id'] ); ?>"><?php _e( 'Approve', 'learndash-groups-plus' ); ?></button>
													<?php endif; ?>
												<?php } ?>
											</div>
										</div>
									<?php } ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php
			}
		} // foreach of lesson
		?>


		<?php if ( ! empty( $user_courses_lessons_quizzes['course_quizzes_data'] ) ) : ?>
			<h5><?php _e( 'Course Quizzes', 'learndash-groups-plus' ); ?></h5>
			<div class="course-quizzes-content">
				<div class="div-table quiz-table">
					<div class="div-table-row-header div-table-row-header-course-quizzes">
						<div class="div-table-col" align="center">
							<?php printf( esc_html( '%s' ), esc_attr( LearnDash_Custom_Label::get_label( 'quiz' ) ) ); ?>
						</div>
						<div class="div-table-col"><?php esc_html_e( 'Score', 'learndash-groups-plus' ); ?></div>
						<div class="div-table-col">
							<?php esc_html_e( 'Statistics', 'learndash-groups-plus' ); ?>
						</div>
						<div class="div-table-col">
							<?php esc_html_e( 'Time Spent', 'learndash-groups-plus' ); ?>
						</div>
						<div class="div-table-col"><?php esc_html_e( 'Certificate', 'learndash-groups-plus' ); ?></div>
					</div>
					<?php
					foreach ( $user_courses_lessons_quizzes['course_quizzes_data'] as $quiz_row ) {
						?>
						<div class="div-table-row-parent">
							<?php foreach ( $quiz_row['quiz_attempts'] as $quiz_attempt ) { ?>
								<div class="div-table-row">
									<div class="div-table-col">
										<a href="<?php esc_attr_e( $quiz_attempt['quiz_url'], 'learndash-groups-plus' ); ?>" target="_blank"><?php esc_html_e( $quiz_attempt['quiz_name'], 'learndash-groups-plus' ); ?>
										</a>
									</div>
									<div class="div-table-col"><?php esc_html_e( $quiz_attempt['quiz_score'], 'learndash-groups-plus' ); ?></div>
									<div class="div-table-col">
										<?php
										if ( SharedFunctions::is_quiz_notification_for_ld_active() && $quiz_attempt['quiz_statistics_on'] && $quiz_attempt['statistic_ref_id'] ) {
											echo '<a class="elc_ldquiz_load" data-statistic-nonce="' . wp_create_nonce( 'elc_ldquizntf_statistic' ) . '" data-statistic-ref-id="' . esc_attr( $quiz_attempt['statistic_ref_id'] ) . '" data-quiz-id="' . esc_attr( $quiz_attempt['pro_quizid'] ) . '"><span class="ld-icon ld-icon-assignment"></span></a>';
										} elseif ( $quiz_attempt['quiz_statistics_on'] && $quiz_attempt['statistic_ref_id'] ) {
											?>
											<a class="user_statistic" data-statistic-nonce="<?php echo esc_attr( wp_create_nonce( 'statistic_nonce_' . $quiz_attempt['statistic_ref_id'] . '_' . get_current_user_id() . '_' . $user_id ) ); ?>" data-user-id="<?php esc_attr_e( $user_id, 'learndash-groups-plus' ); ?>" data-quiz-id="<?php esc_attr_e( $quiz_attempt['pro_quizid'], 'learndash-groups-plus' ); ?>" data-ref-id="<?php esc_attr_e( $quiz_attempt['statistic_ref_id'], 'learndash-groups-plus' ); ?>" href="#"><span class="ld-icon ld-icon-assignment"></span></a>
											<?php
										} else {
											echo '-';
										}
										?>
									</div>
									<div class="div-table-col"><?php _e( $quiz_row['time_spent'], 'learndash-groups-plus' ); ?></div>
									<div class="div-table-col">
										<?php
										if ( ! empty( $quiz_attempt['quiz_certificate'] ) ) {
											?>
											<a class="certificate-icon" href="<?php esc_attr_e( $quiz_attempt['quiz_certificate'], 'learndash-groups-plus' ); ?>" target="_blank">
												<img width="50px" src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/certificate.png'; ?>
									alt=" <?php _e( 'Print PDF', 'learndash-groups-plus' ); ?>" srcset="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/certificate.svg'; ?>>
								<path id=" Layer_1" />
												</img>
											</a>
											<?php
										} else {
											echo '-';
										}
										?>
									</div>
								</div>
								<?php if ( ! empty( $quiz_attempt['essays_data'] ) ) : ?>
									<div class="quiz-content">
										<div class="div-table essay-table">
											<div class="div-table-row-header">
												<div class="div-table-col" align="center">
													<?php esc_html_e( 'Essays', 'learndash-groups-plus' ); ?>
												</div>
												<div class="div-table-col">
													<?php esc_html_e( 'Comments', 'learndash-groups-plus' ); ?>
												</div>
												<div class="div-table-col">
													<?php esc_html_e( 'Status', 'learndash-groups-plus' ); ?>
												</div>
												<div class="div-table-col">
													<?php esc_html_e( 'Points', 'learndash-groups-plus' ); ?>
												</div>
											</div>

											<?php foreach ( $quiz_attempt['essays_data'] as $essay_row ) : ?>
												<div class="div-table-row">
													<div class="div-table-col">
														<a href="<?php echo esc_url( $essay_row['essay_url'] ); ?>" target="_blank">
															<?php esc_html_e( $essay_row['question_post_title'], 'learndash-groups-plus' ); ?>
														</a>
													</div>
													<div class="div-table-col">
														<a href="<?php echo esc_url( $essay_row['essay_url'] ); ?>" target="_blank">
															<span class="ld-icon ld-icon-comments"></span>
															<?php esc_html_e( $essay_row['comments'], 'learndash-groups-plus' ); ?>
														</a>
													</div>
													<div class="div-table-col">
														<?php
														if ( $essay_row['status'] === 'graded' ) :
															echo wp_kses_post( learndash_status_bubble( $essay_row['status'], 'essay', false ) );
														else :
															?>
															<p class="essay-status">
																<input type="number" name="essay_points[<?php echo $essay_row['essay_id']; ?>]" min="0" max="<?php _e( $essay_row['points']['total'], 'learndash-groups-plus' ); ?>" step="1" /> /
																<?php _e( $essay_row['points']['total'], 'learndash-groups-plus' ); ?>
																<button type="button" class="apr_essay_btn" data-essay-id="<?php echo esc_attr( $essay_row['essay_id'] ); ?>"><?php _e( 'Approve', 'learndash-groups-plus' ); ?></button>
															</p>
														<?php endif; ?>
													</div>
													<div class="div-table-col">
														<?php esc_html_e( $essay_row['points']['awarded'] . '/' . $essay_row['points']['total'], 'learndash-groups-plus' ); ?>
													</div>
												</div>
												<?php
											endforeach; // essays_data.
											?>
										</div>
									</div>
									<?php
								endif; // essays_data.
								?>

							<?php } ?>

						</div>
					<?php } ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Statistics overlay popup -->
<div id="wpProQuiz_user_overlay" style="display: none;">
	<div class="wpProQuiz_modal_window" style="padding: 20px; overflow: scroll;">
		<input type="button" value="Close" class="button-primary" style="position: fixed; top: 48px; right: 59px; z-index: 160001;" id="wpProQuiz_overlay_close">
		<div id="wpProQuiz_user_content" style="margin-top: 20px;"></div>
		<div id="wpProQuiz_loadUserData" class="wpProQuiz_blueBox" style="background-color: #F8F5A8; display: none; margin: 50px;">
			<img alt="load" src="<?php echo esc_url( admin_url( '/images/wpspin_light.gif' ) ); ?>" />
			<?php esc_html_e( 'Loading', 'learndash-groups-plus' ); ?>
		</div>
	</div>
	<div class="wpProQuiz_modal_backdrop"></div>
</div>
