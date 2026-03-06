import './index.scss';
import { Tab, Tabs, TabList, TabPanel } from 'react-tabs';
import Select from '../commons/emotion-styles-provider/select.js';
import 'react-tabs/style/react-tabs.css';
import { __ } from '@wordpress/i18n';
import React, { Component, CSSProperties } from 'react';
import WisdmLoader from '../commons/loader/index.js';
import ComponentDatepicker from './component-date-filter.js';
import Modal, { closeStyle } from 'simple-react-modal';
window.ld_api_settings = {
	'sfwd-courses': 'sfwd-courses',
	'sfwd-lessons': 'sfwd-lessons',
	'sfwd-topic': 'sfwd-topic',
	'sfwd-quiz': 'sfwd-quiz',
	'sfwd-question': 'sfwd-question',
	users: 'users',
	groups: 'groups',
};

class Checkbox extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			isChecked: props.isChecked == 'yes' ? true : false,
			name: props.name,
			label: props.label,
			value: 'yes',
			always_checked: props.always_checked,
			disabled: props.always_checked == 'yes' ? 'disabled' : '',
		};
	}

	toggleChange = () => {
		if ( this.state.always_checked != 'yes' ) {
			this.setState( {
				isChecked: ! this.state.isChecked,
			} );
		}
	};

	render() {
		return (
			<div className="checkbox-wrapper">
				<label>
					<input
						type="checkbox"
						name={ this.state.name }
						value={ this.state.value }
						defaultChecked={ this.state.isChecked }
						onChange={ this.toggleChange }
						disabled={ this.state.disabled }
					/>
					{ this.state.label }
				</label>
			</div>
		);
	}
}

// cspell:disable-next-line
function getQuizesByCoursesAccessible( courseList, quizzes ) {
	const user_type = wisdmLdReportsGetUserType();
	let filtered_quizzes = [];
	if ( 'group_leader' == user_type ) {
		const courseIds = Array();
		courseList.forEach( function ( course ) {
			courseIds.push( course.value );
		} );

		quizzes.forEach( function ( quiz ) {
			if ( courseIds.includes( parseInt( quiz.course_id ) ) ) {
				filtered_quizzes.push( quiz );
			}
		} );
	} else if ( 'instructor' == user_type ) {
		filtered_quizzes = quizzes;
	} else {
		filtered_quizzes = quizzes;
	}
	return filtered_quizzes;
}

/**
 * Based on the current user roles array this function decides wether a user is a group
 * leader or an Administrator and returns the same.
 */
function wisdmLdReportsGetUserType() {
	let userRoles = wisdm_ld_reports_common_script_data.user_roles;
	if ( 'object' === typeof userRoles ) {
		userRoles = Object.keys( userRoles ).map( ( key ) => userRoles[ key ] );
	}
	if ( undefined == userRoles || userRoles.length == 0 ) {
		return null;
	}
	if ( userRoles.includes( 'administrator' ) ) {
		return 'administrator';
	} else if ( userRoles.includes( 'group_leader' ) ) {
		return 'group_leader';
	} else if ( userRoles.includes( 'wdm_instructor' ) ) {
		return 'instructor';
	}
	return null;
}

class StudentFilters extends React.Component {
	constructor( props ) {
		super( props );

		const courses_disabled = false;
		let error = null;
		if ( this.getUserType() ) {
			error = {
				message: __(
					'Sorry you are not allowed to access this block, please check if you have proper access permissions',
					'learndash-reports-pro'
				),
			};
		}

		this.state = {
			isLoaded: false,
			isLoggedIn: false,
			error,
			loading_courses: false,
			loading_quizzes: false,
			selected_courses: {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			},
			selected_quiz: {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			},
			show_quiz_filter_modal: false,
			courses_disabled,
			courses: [],
			default_courses: [],
			quizzes: [],
			default_quizzes: [],
			quiz_disabled: courses_disabled,
			start_date: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			).unix(),
			end_date: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			).unix(),
			selectedFields:
				wisdm_ld_reports_common_script_data.report_preferences
					?.settings,
			user_id: wisdm_ld_reports_common_script_data.current_user.ID,
		};
		if ( typeof props.parent.course_label !== 'undefined' ) {
			this.state.selected_courses = {
				value: props.parent.course,
				label: props.parent.course_label,
			};
			this.state.selected_quiz = {
				value: props.parent.quiz,
				label: props.parent.quiz_label,
			};
			this.state.courses = props.parent.courses;
			this.state.quizzes = props.parent.quizzes;
			this.state.start_date = props.parent.start_date;
			this.state.end_date = props.parent.end_date;
		}

		let localized_data_url = '/rp/v1/report-filters-data';
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			localized_data_url +=
				'/?wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		wp.apiFetch( {
			path: localized_data_url, //Replace with the correct API
		} ).then( ( response ) => {
			if ( response.wisdm_ld_reports_common_script_data ) {
				window.wisdm_ld_reports_common_script_data = {
					...window.wisdm_ld_reports_common_script_data,
					...response.wisdm_ld_reports_common_script_data,
				};
			}
			window.ld_api_settings =
				wisdm_ld_reports_common_script_data.ld_api_settings;
		} );
		this.applyStudentFilters = this.applyStudentFilters.bind( this );
		this.previewCustomReport = this.previewCustomReport.bind( this );
		this.previewReport = this.previewReport.bind( this );
		this.openCustomizePreviewModal =
			this.openCustomizePreviewModal.bind( this );
		this.closeCustomizePreviewModal =
			this.closeCustomizePreviewModal.bind( this );
		this.dateUpdated = this.dateUpdated.bind( this );
	}
	getUserType() {
		if ( wisdm_ld_reports_common_script_data.current_user.ID == 0 ) {
			return true;
		}
		return false;
	}

	dateUpdated( event ) {
		this.setState( {
			start_date: event.detail.startDate,
			end_date: event.detail.endDate,
		} );
	}

	applyStudentFilters() {
		const applyFilters = new CustomEvent(
			'wisdm-ld-reports-student-filters-applied',
			{
				detail: {
					start_date: this.state.start_date,
					end_date: this.state.end_date,
					selected_quiz: this.state.selected_quiz,
					selected_courses: this.state.selected_courses,
					user_id: this.state.user_id,
					courses: this.state.courses,
					quizzes: this.state.quizzes,
				},
			}
		);

		document.dispatchEvent( applyFilters );
	}
	openCustomizePreviewModal() {
		document.body.classList.add( 'wrld-open' );
		this.setState( {
			show_quiz_filter_modal: true,
		} );
	}

	closeCustomizePreviewModal() {
		document.body.classList.remove( 'wrld-open' );
		this.setState( {
			show_quiz_filter_modal: false,
		} );
	}
	previewReport() {
		this.previewCustomReport();
	}

	previewCustomReport() {
		// let fields_selected = {};
		// const customQuizReport = new CustomEvent("wisdm-ld-reports-custom-quiz-report-filters-applied", {
		//     "detail": {
		//                 'start_date':this.state.start_date,
		//                 'end_date':this.state.end_date,
		//                 'course_completion_dates_from':this.state.start_date,
		//                 'course_completion_dates_to':this.state.end_date,
		//                 'fields_selected':fields_selected,
		//                 'selected_courses': this.state.selected_courses.value,
		//                 'selected_quizes': this.state.selected_quiz.value, // cspell:disable-line
		//             }});
		// document.dispatchEvent(customQuizReport);
	}

	getCourseListFromJson( response ) {
		const courseList = [];
		if ( response.length == 0 ) {
			return courseList; //no courses found
		}

		for ( let i = 0; i < response.length; i++ ) {
			courseList.push( {
				value: response[ i ].id,
				label: response[ i ].title.rendered,
			} );
		}
		//courseList = getCoursesByGroups(courseList);
		return courseList;
	}

	componentDidMount() {
		document.addEventListener( 'date_updated', this.dateUpdated );
		if ( wisdm_ld_reports_common_script_data.courses_enrolled.length > 0 ) {
			let url =
				'/ldlms/v1/' +
				ld_api_settings[ 'sfwd-courses' ] +
				'?per_page=-1';
			if (
				wisdm_ld_reports_common_script_data.courses_enrolled.length >
					0 &&
				false !=
					wisdm_ld_reports_common_script_data.is_pro_version_active
			) {
				for (
					let i = 0;
					i <
					wisdm_ld_reports_common_script_data.courses_enrolled.length;
					i++
				) {
					url +=
						'&include[]=' +
						wisdm_ld_reports_common_script_data.courses_enrolled[
							i
						];
				}
			}
			if ( this.state.courses.length === 0 ) {
				if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
					url +=
						'&wpml_lang=' +
						wisdm_ld_reports_common_script_data.wpml_lang;
				}
				wp.apiFetch( {
					path: url, //Replace with the correct API
				} ).then( ( response ) => {
					let lock_icon = '';
					let quiz_section_disabled = '';
					if (
						false ==
						wisdm_ld_reports_common_script_data.is_pro_version_active
					) {
						lock_icon = (
							<span
								title={ __(
									'Please upgrade the plugin to access this feature',
									'learndash-reports-pro'
								) }
								className="dashicons dashicons-lock ld-reports"
							></span>
						);
						quiz_section_disabled = 'disabled';
					}
					const courses = this.getCourseListFromJson( response );
					if ( this.state.quizzes.length === 0 ) {
						// cspell:disable-next-line
						const quizzes = getQuizesByCoursesAccessible(
							courses,
							wisdm_ld_reports_common_script_data.quizes // cspell:disable-line
						);
						this.setState( { quizzes } );
					}
					this.setState( {
						isLoaded: true,
						courses,
						default_courses: courses,
						courses_disabled: false,
						lessons: [],
						topics: [],
						learners: [],
					} );
				} );
			} else {
				this.setState( {
					isLoaded: true,
					courses_disabled: false,
				} );
			}
		} else {
			this.setState( {
				isLoaded: true,
				courses_disabled: false,
			} );
		}
	}

	getAllCourses() {
		callback_path =
			callback_path +
			'?ld_course_category[]=' +
			selection +
			'&per_page=-1';
		let url = '';
		if (
			wisdm_ld_reports_common_script_data.exclude_courses.length > 0 &&
			false != wisdm_ld_reports_common_script_data.is_pro_version_active
		) {
			for (
				let i = 0;
				i < wisdm_ld_reports_common_script_data.exclude_courses.length;
				i++
			) {
				url +=
					'&exclude[]=' +
					wisdm_ld_reports_common_script_data.exclude_courses[ i ];
			}
		}
		callback_path += url;
		if ( null == selection ) {
			this.setState( {
				courses: [],
				lessons: [],
				topics: [],
				selected_courses: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
				selected_lessons: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
				selected_topics: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
				lessons_disabled: true,
				topics_disabled: true,
			} );
		} else {
			this.setState( { loading_courses: true } );
			if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
				callback_path +=
					'&wpml_lang=' +
					wisdm_ld_reports_common_script_data.wpml_lang;
			}
			wp.apiFetch( {
				path: callback_path, //Replace with the correct API
			} )
				.then( ( response ) => {
					const courses = this.getCourseListFromJson( response );
					if ( false != courses && courses.length > 0 ) {
						//if selected course is not in the list then clear the field
						let course_in_the_list = false;
						const selected_course_id =
							this.state.selected_courses.value;
						courses.forEach( function ( course ) {
							if (
								null != selected_course_id &&
								course.value == selected_course_id
							) {
								course_in_the_list = true;
							}
						} );
						if ( ! course_in_the_list ) {
							this.setState( {
								selected_courses: {
									value: null,
									label: __( 'All', 'learndash-reports-pro' ),
								},
								selected_lessons: {
									value: null,
									label: __( 'All', 'learndash-reports-pro' ),
								},
								selected_topics: {
									value: null,
									label: __( 'All', 'learndash-reports-pro' ),
								},
								lessons_disabled: true,
								topics_disabled: true,
							} );
						}
						this.setState( {
							courses,
							courses_disabled: false,
							loading_courses: false,
						} );
					}
				} )
				.catch( ( error ) => {
					this.setState( {
						selected_courses: {
							value: null,
							label: __( 'All', 'learndash-reports-pro' ),
						},
						selected_lessons: {
							value: null,
							label: __( 'All', 'learndash-reports-pro' ),
						},
						selected_topics: {
							value: null,
							label: __( 'All', 'learndash-reports-pro' ),
						},
						lessons_disabled: true,
						topics_disabled: true,
					} );
				} );
		}
	}

	// cspell:disable-next-line
	getCourseQuizes( course_id, quiz_list ) {
		const course_quizzes = [];
		quiz_list.forEach( function ( quiz ) {
			if ( quiz.course_id == course_id ) {
				course_quizzes.push( quiz );
			}
		} );
		return course_quizzes;
	}
	//update dropdowns
	updateSelectorsFor(
		element,
		selection,
		callback_path = '/wp/v2/categories'
	) {
		callback_path = callback_path + '?course=' + selection + '&per_page=-1';
		if ( null == selection ) {
			this.setState( {
				quizzes: wisdm_ld_reports_common_script_data.quizes, // cspell:disable-line
				selected_lessons: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
				selected_topics: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
		} else {
			this.setState( { loading_quizzes: true } );

			const course_quizzes = [];
			const quiz_list = wisdm_ld_reports_common_script_data.quizes; // cspell:disable-line
			quiz_list.forEach( function ( quiz ) {
				if ( quiz.course_id == selection ) {
					course_quizzes.push( quiz );
				}
			} );

			if ( false != course_quizzes && course_quizzes.length > 0 ) {
				this.setState( {
					selected_quiz: {
						value: null,
						label: __( 'All', 'learndash-reports-pro' ),
					},
					quizzes: course_quizzes,
					loading_quizzes: false,
					quiz_disabled: false,
				} );
			} else {
				this.setState( {
					selected_quiz: {
						value: null,
						label: __( 'All', 'learndash-reports-pro' ),
					},
					quizzes: course_quizzes,
					loading_quizzes: false,
				} );
			}
		}
	}

	//handle course change
	handleCourseChange = ( selectedCourse ) => {
		if ( null == selectedCourse ) {
			this.setState( {
				selected_courses: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
			this.updateSelectorsFor( 'course', null );
		} else {
			this.setState( { selected_courses: selectedCourse } );
			this.updateSelectorsFor(
				'course',
				selectedCourse.value,
				'/ldlms/v1/' + ld_api_settings[ 'sfwd-quiz' ] + '/'
			);
		}
	};

	handleQuizChange = ( selectedCourse ) => {
		if ( null == selectedCourse ) {
			this.setState( {
				selected_quiz: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
		} else {
			this.setState( {
				selected_quiz: {
					value: selectedCourse.value,
					label: __( selectedCourse.label, 'learndash-reports-pro' ),
				},
			} );
		}
	};

	render() {
		let body = <div></div>;
		if ( this.state.error ) {
			// error
			body = '';
		} else if ( ! this.state.isLoaded ) {
			// yet loading
			body = <WisdmLoader />;
		} else {
			body = (
				<div className="user-filter-section">
					<div className="user-filter-selectors">
						<div className="selector">
							<div className="selector-label">
								{ /* cspell:disable-next-line */ }
								{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Courses'
								) }
							</div>
							<div className="select-control">
								<Select
									isDisabled={ this.state.courses_disabled }
									isLoading={ this.state.loading_courses }
									onChange={ this.handleCourseChange }
									options={ this.state.courses }
									value={ this.state.selected_courses }
									isClearable="true"
								/>
							</div>
						</div>
						<div className="selector">
							<div className="selector-label">
								{ /* cspell:disable-next-line */ }
								{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Quizzes'
								) }
							</div>
							<div className="select-control">
								<Select
									isDisabled={ this.state.quiz_disabled }
									isLoading={ this.state.loading_quizzes }
									onChange={ this.handleQuizChange }
									options={ this.state.quizzes }
									value={ this.state.selected_quiz }
									isClearable="true"
								/>
							</div>
						</div>

						<div className="selector">
							<div className="selector-label">
								{ __(
									'DATE OF ATTEMPT',
									'learndash-reports-pro'
								) }
							</div>
							<div className="select-control">
								<ComponentDatepicker
									start={ this.state.start_date }
									end={ this.state.end_date }
								></ComponentDatepicker>
							</div>
						</div>
					</div>

					<div className="filter-buttons">
						<div className="filter-button-container">
							{ /* <Modal  show={this.state.show_quiz_filter_modal}
                            onClose={this.closeCustomizePreviewModal}
                            containerStyle={{width:'80%'}}
                            >
                        <div className="quiz-filter-modal">
                            <div className="header">
                                <h2>{__('Customize Report', 'learndash-reports-pro')}</h2>
                            </div>
                            <div className="quiz-reporting-custom-filters lr-dropdowns">
                                <div className="selector">
                                    <div className="selector-label">{__('All Attempts Report Fields','learndash-reports-pro')}{this.state.lock_icon}</div>
                                    <div className="select-control">
                                        <Checkbox isChecked="yes" always_checked="yes" name="user_name" label={__('Username',   'learndash-reports-pro')}/>
                                        <Checkbox isChecked="yes" always_checked="yes" name="quiz_title" label={__('Quiz',      'learndash-reports-pro')}/>
                                        <Checkbox isChecked="yes" always_checked="yes" name="course_title" label={__('Course', 'learndash-reports-pro')}/>
                                        <Checkbox isChecked={this.state.selectedFields.course_category} name="course_category" label={__('Course Category','learndash-reports-pro')}/>
                                        <Checkbox isChecked={this.state.selectedFields.group_name} name="group_name" label={__('Group',   'learndash-reports-pro')}/>
                                        <Checkbox isChecked={this.state.selectedFields.user_email} name="user_email" label={__('User Email',   'learndash-reports-pro')}/>
                                        <Checkbox isChecked={this.state.selectedFields.quiz_status} name="quiz_status" label={__('Quiz Status',      'learndash-reports-pro')}/>
                                        <Checkbox isChecked="yes" always_checked="yes" name="quiz_category" label={__('Quiz Category',      'learndash-reports-pro')}/>
                                        <Checkbox isChecked="yes" always_checked="yes" name="quiz_points_earned" label={__('Points Earned',      'learndash-reports-pro')}/>
                                        <Checkbox isChecked={this.state.selectedFields.quiz_score_percent} name="quiz_score_percent" label={__('Score (in%)',      'learndash-reports-pro')}/>
                                        <Checkbox isChecked="yes" always_checked="yes" name="date_of_attempt" label={__('Date of attempt',      'learndash-reports-pro')}/>
                                        <Checkbox isChecked="yes" always_checked="yes" name="time_taken" label={__('Time Taken',      'learndash-reports-pro')}/>
                                    </div>
                                </div>
                                <div className="selector">
                                    <div className="selector-label">{__('Question Response Report Fields','learndash-reports-pro')}{this.state.lock_icon}
                                    </div>
                                    <div className="select-control">
                                        <Checkbox isChecked={this.state.selectedFields.question_type} name="question_type" label={__('Question Type',      'learndash-reports-pro')}/>
                                        <Checkbox isChecked={this.state.selectedFields.user_first_name} name="user_first_name" label={__('First Name',   'learndash-reports-pro')}/>
                                        <Checkbox isChecked={this.state.selectedFields.user_last_name} name="user_last_name" label={__('Last Name',   'learndash-reports-pro')}/>
                                    </div>
                                </div>
                            </div>
                            <div className="modal-action-buttons">
                                <button className="button-customize-preview cancel" onClick={this.closeCustomizePreviewModal}>{__('Cancel', 'learndash-reports-pro')}</button>
                                <button className="button-quiz-preview" onClick={this.previewCustomReport}>{__('Apply', 'learndash-reports-pro')}</button>
                            </div>
                        </div>
                    </Modal> */ }
							{ /* <button className="button-customize-preview" onClick={this.openCustomizePreviewModal}>{__('CUSTOMIZE REPORT', 'learndash-reports-pro')}</button> */ }
							<button
								className="button-quiz-preview"
								onClick={ this.applyStudentFilters }
							>
								{ __(
									'APPLY FILTERS',
									'learndash-reports-pro'
								) }
							</button>
						</div>
					</div>
				</div>
			);
		}

		return body;
	}
}

export default StudentFilters;

/**
 * Based on the current user roles array this function decides wether a user is a group
 * leader or an Administrator and returns the same.
 */

// document.addEventListener("DOMContentLoaded", function (event) {
//   let elem = document.getElementsByClassName(
//     "wisdm-learndash-reports-student-filters front"
//   );
//   if (elem.length > 0) {
//     ReactDOM.render(React.createElement(StudentFilters), elem[0]);
//   }
// });
