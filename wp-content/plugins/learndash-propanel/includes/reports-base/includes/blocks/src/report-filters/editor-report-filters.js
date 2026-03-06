/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/#registering-a-block
 *
 * cspell:ignore globalfilters eporting
 */
import { registerBlockType, RichText, source } from '@wordpress/blocks';
import './editor.scss';
import Select, {
	AsyncSelect,
} from '../commons/emotion-styles-provider/select.js';
const moment = require( 'moment' );
import { Tab, Tabs, TabList, TabPanel } from 'react-tabs';
import DummyFilters from '../dummy-quiz-reports/index.js';
import 'react-tabs/style/react-tabs.css';

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __, sprintf } from '@wordpress/i18n';
import { createElement } from '@wordpress/element';
import React, { Component, CSSProperties } from 'react';
import WisdmLoader from '../commons/loader/index.js';
import { array } from 'prop-types';
import { Modal } from '@wordpress/components';
import { useBlockProps } from '@wordpress/block-editor';
import { createRoot } from '@wordpress/element';

window.ld_api_settings = {
	'sfwd-courses': 'sfwd-courses',
	'sfwd-lessons': 'sfwd-lessons',
	'sfwd-topic': 'sfwd-topic',
	'sfwd-quiz': 'sfwd-quiz',
	'sfwd-question': 'sfwd-question',
	users: 'users',
	groups: 'groups',
};
const icon = (
	<svg
		version="1.0"
		xmlns="http://www.w3.org/2000/svg"
		width="24.000000pt"
		height="24.000000pt"
		viewBox="0 0 24.000000 24.000000"
		preserveAspectRatio="xMidYMid meet"
	>
		<path
			d="M2.9,21.6c0,1.1,0.9,2,1.9,2h14.4c1.1,0,1.9-0.9,1.9-2V3.7c0-1.1-0.9-2-1.9-2h-2.6l-0.1-0.1c-0.7-0.8-1.7-1.2-2.7-1.2h-3.7
                    c-1,0-1.9,0.4-2.6,1.2L7.4,1.7H4.8c-1.1,0-2,0.9-2,2V21.6z M7.8,3.2c0.4-1,1.4-1.7,2.3-1.7h3.7c1,0,2,0.7,2.3,1.7l0.1,0.2H7.7
                    L7.8,3.2z M4.1,3.7c0-0.5,0.4-0.9,0.8-0.9h2L6.7,3C6.7,3.3,6.5,3.7,6.5,4c0,0.4,0.2,0.6,0.6,0.6h9.7c0.3,0,0.5-0.3,0.5-0.6
                    c0-0.3,0-0.6-0.1-0.9l-0.1-0.2h2c0.5,0,0.8,0.4,0.8,0.9v17.9c0,0.5-0.4,0.9-0.8,0.9H4.8c-0.5,0-0.8-0.4-0.8-0.9V3.7z"
		/>
		<path
			d="M8.8,14l-1.1,1.1l-0.4-0.4c-0.1-0.1-0.3-0.1-0.4-0.1c-0.1,0-0.2,0-0.4,0.1c-0.1,0.1-0.2,0.3-0.2,0.5c0,0.1,0.1,0.2,0.2,0.3
                    l0,0l0.6,0.7c0.1,0.1,0.3,0.2,0.5,0.2c0.1,0,0.2-0.1,0.3-0.2l1.4-1.4c0.2-0.1,0.3-0.3,0.3-0.4c0-0.1,0-0.3-0.2-0.4
                    c-0.1-0.1-0.3-0.2-0.5-0.2C8.9,13.9,8.8,13.9,8.8,14z"
		/>
		<path d="M11.6,14.6c-0.3,0-0.5,0.3-0.5,0.6s0.2,0.6,0.5,0.6h5.5c0.3,0,0.5-0.3,0.5-0.6c0-0.4-0.2-0.6-0.5-0.6H11.6z" />
		<path
			d="M8.8,17.9L7.7,19l-0.4-0.4c-0.1-0.1-0.3-0.1-0.4-0.1s-0.2,0-0.4,0.1c-0.1,0.1-0.2,0.3-0.2,0.5c0,0.1,0.1,0.2,0.2,0.3
                    l0.7,0.7c0.1,0.1,0.3,0.2,0.5,0.2c0.1,0,0.2-0.1,0.3-0.2l1.4-1.5c0.1-0.1,0.2-0.2,0.2-0.3c0-0.2,0-0.4-0.2-0.5S9.2,17.7,9,17.8
                    C8.9,17.8,8.8,17.8,8.8,17.9z"
		/>
		<path d="M11.6,18.5c-0.3,0-0.5,0.3-0.5,0.6s0.2,0.6,0.5,0.6h5.5c0.3,0,0.5-0.3,0.5-0.6c0-0.4-0.2-0.6-0.5-0.6H11.6z" />
		<path
			d="M16,9.8c0.3,0,0.5-0.3,0.5-0.6l0-1.7c0-0.1,0-0.2,0-0.3C16.4,7,16.3,7,16.1,7h-1.7c-0.3,0-0.5,0.3-0.5,0.6
                    c0,0.3,0.2,0.6,0.5,0.6l0.4,0L13,10.1l-2.2-2.2c-0.3-0.2-0.6-0.2-0.9-0.1l-2.5,2.5c-0.1,0.1-0.2,0.3-0.2,0.5c0,0.1,0.1,0.2,0.2,0.3
                    l0,0c0.1,0.1,0.3,0.2,0.5,0.2c0.1,0,0.2-0.1,0.3-0.2l2-2.1l2.2,2.1c0.1,0.1,0.3,0.1,0.4,0.1s0.2,0,0.4-0.1l2.4-2.4l0,0.4
                    C15.5,9.4,15.7,9.8,16,9.8z"
		/>
	</svg>
);

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

class QuizFilters extends Component {
	constructor( props ) {
		super( props );

		let quiz_section_disabled = 'disabled';
		let report_type_selected = 'default-quiz-reports';
		if (
			false != wisdm_ld_reports_common_script_data.is_pro_version_active
		) {
			quiz_section_disabled = 'enabled';
		}

		if (
			undefined !=
				wisdm_ld_reports_common_script_data.qre_request_params &&
			wisdm_ld_reports_common_script_data.qre_request_params.report ==
				'custom'
		) {
			report_type_selected = 'custom-quiz-reports';
		}

		const userType = wisdmLdReportsGetUserType();
		let groups_disabled = false;
		const quizzes_disabled = false;
		let categories_disabled = false;
		const courses = getCoursesByGroups(
			wisdm_ld_reports_common_script_data.courses
		);
		// cspell:disable-next-line
		const quizzes = getQuizesByCoursesAccessible(
			courses,
			wisdm_ld_reports_common_script_data.quizes // cspell:disable-line
		);
		// cspell:disable-next-line
		this.default_quizes = quizzes;
		this.default_groups = wisdm_ld_reports_common_script_data.course_groups;
		let selected_course = {
			value: -1,
			label: __( 'All', 'learndash-reports-pro' ),
		};
		let selected_group = {
			value: -1,
			label: __( 'All', 'learndash-reports-pro' ),
		};
		let selected_quiz = {
			value: -1,
			label: __( 'All', 'learndash-reports-pro' ),
		};
		if ( undefined != wisdm_ld_reports_common_script_data.qre_filters ) {
			const qre_filters = wisdm_ld_reports_common_script_data.qre_filters;
			const selected_course_id =
				undefined != qre_filters.course_filter &&
				qre_filters.course_filter > 0
					? parseInt( qre_filters.course_filter )
					: -1;
			selected_course = getSelectionByValueId(
				selected_course_id,
				courses
			);
			const selected_group_id =
				undefined != qre_filters.group_filter &&
				qre_filters.group_filter > 0
					? parseInt( qre_filters.group_filter )
					: -1;
			selected_group = getSelectionByValueId(
				selected_group_id,
				this.default_groups
			);
			const selected_quiz_id =
				undefined != qre_filters.quiz_filter &&
				qre_filters.quiz_filter > 0
					? parseInt( qre_filters.quiz_filter )
					: -1;
			selected_quiz = getSelectionByValueId(
				selected_quiz_id,
				this.default_quizes // cspell:disable-line
			);
		}

		if ( 'administrator' == userType ) {
		} else if ( 'group_leader' == userType ) {
			categories_disabled = true;
			groups_disabled = false;
		}

		this.state = {
			isLoaded: false,
			error: null,
			report_type_selected,
			courses_disabled: '',
			groups_disabled,
			// cspell:disable-next-line
			quizes_disabled: quizzes_disabled,
			categories: wisdm_ld_reports_common_script_data.course_categories,
			courses,
			groups: this.default_groups,
			// cspell:disable-next-line
			quizes: this.default_quizes,
			show_quiz_filter_modal: false,
			custom_report_fields: [],
			selected_courses: selected_course,
			selected_groups: selected_group,
			// cspell:disable-next-line
			selected_quizes: selected_quiz,
			selectedElementsInDefaultFilter: null,
			start_date: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			).unix(),
			end_date: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			).unix(),
			selectedValue: wisdm_ld_reports_common_script_data.report_preferences?.settings,
			selectedFields: wisdm_ld_reports_common_script_data.report_preferences?.settings,
			selectedCourseTitle: wisdm_ld_reports_common_script_data.report_preferences?.selected_course_title,
			selectedGroupTitle: wisdm_ld_reports_common_script_data.report_preferences?.selected_group_title,
			selectedQuizTitle: wisdm_ld_reports_common_script_data.report_preferences?.selected_quiz_title,
		};

		this.durationUpdated = this.durationUpdated.bind( this );
		this.onQuizReportViewChange = this.onQuizReportViewChange.bind( this );
		this.handleQuizFilterDefaultSearch =
			this.handleQuizFilterDefaultSearch.bind( this );
		this.openCustomizePreviewModal =
			this.openCustomizePreviewModal.bind( this );
		this.closeCustomizePreviewModal =
			this.closeCustomizePreviewModal.bind( this );
		this.handleQuizSearch = this.handleQuizSearch.bind( this );
		this.handleCourseSearch = this.handleCourseSearch.bind( this );
		this.handleGroupSearch = this.handleGroupSearch.bind( this );
		this.handleDefaultQuizFilterChange =
			this.handleDefaultQuizFilterChange.bind( this );
		this.handleQuizCourseChange = this.handleQuizCourseChange.bind( this );
		this.handleQuizGroupChange = this.handleQuizGroupChange.bind( this );
		this.handleQuizChange = this.handleQuizChange.bind( this );
		this.applyQuizFilters = this.applyQuizFilters.bind( this );
		this.previewCustomReport = this.previewCustomReport.bind( this );
		this.previewReport = this.previewReport.bind( this );
	}

	componentDidMount() {
		document.addEventListener( 'duration_updated', this.durationUpdated );
	}

	durationUpdated( event ) {
		this.setState( {
			start_date: event.detail.startDate,
			end_date: event.detail.endDate,
		} );
	}

	handleQuizSearch = ( inputString, callback ) => {
		// perform a request
		let callback_path = '/ldlms/v1/' + ld_api_settings[ 'sfwd-quiz' ] + '/';
		const requestResults = [];
		if ( 2 < inputString.length ) {
			callback_path = callback_path + '?search=' + inputString;
			if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
				callback_path +=
					'&wpml_lang=' +
					wisdm_ld_reports_common_script_data.wpml_lang;
			}
			wp.apiFetch( {
				path: callback_path, //Replace with the correct API
			} )
				.then( ( response ) => {
					if ( false != response && response.length > 0 ) {
						response.forEach( ( element ) => {
							requestResults.push( {
								value: element.id,
								label: element.title.rendered,
							} );
						} );
					}
					callback( requestResults );
				} )
				.catch( ( error ) => {
					callback( requestResults );
				} );
		}
	};

	handleQuizChange( selected_quizzes ) {
		if ( null == selected_quizzes ) {
			this.setState( {
				selected_quizes: { value: -1 }, // cspell:disable-line
				selectedValue: { quiz_filter: -1 },
				selectedQuizTitle: __( 'All', 'learndash-reports-pro' ),
			} );
		} else {
			this.setState( {
				selected_quizes: selected_quizzes, // cspell:disable-line
				selectedValue: { quiz_filter: selected_quizzes },
				selectedQuizTitle: selected_quizzes.label,
			} );
		}
	}

	handleQuizFilterDefaultSearch( inputString, callback ) {
		// perform a request
		let callback_path = '/rp/v1/qre-live-search/?search_term=';
		let requestResults = [];
		if ( 2 < inputString.length ) {
			callback_path = callback_path + inputString;
			if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
				callback_path +=
					'&wpml_lang=' +
					wisdm_ld_reports_common_script_data.wpml_lang;
			}
			wp.apiFetch( {
				path: callback_path, //Replace with the correct API
			} )
				.then( ( response ) => {
					const userResults = [];
					const quizResults = [];
					const courseResults = [];
					if (
						false != response &&
						response.search_results.length > 0
					) {
						response.search_results.forEach( ( element ) => {
							if ( 'user' == element.type ) {
								userResults.push( {
									value: element.ID,
									label: element.title,
									type: element.type,
								} );
							} else if ( 'quiz' == element.type ) {
								quizResults.push( {
									value: element.ID,
									label: element.title,
									type: element.type,
								} );
							} else if ( 'post' == element.type ) {
								courseResults.push( {
									value: element.ID,
									label: element.title,
									type: element.type,
								} );
							}
						} );
						requestResults = [
							{
								label: __( 'Users', 'learndash-reports-pro' ),
								options: userResults,
							},
							{
								label: wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Quizzes'
								),
								options: quizResults,
							},
							{
								label: wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Courses'
								),
								options: courseResults,
							},
						];
					}
					callback( requestResults );
				} )
				.catch( ( error ) => {
					callback( requestResults );
				} );
		} else {
			callback( requestResults );
		}
	}

	handleDefaultQuizFilterChange( selectedElements ) {
		this.setState( { selectedElementsInDefaultFilter: selectedElements } );
	}

	onQuizReportViewChange( event ) {
		this.setState( { report_type_selected: event.target.value } );
		let custom_report_type = '';
		if ( 'default-quiz-reports' == event.target.value ) {
			custom_report_type = '';
		} else if ( 'custom-quiz-reports' == event.target.value ) {
			custom_report_type = 'custom';
		}
		document.dispatchEvent(
			new CustomEvent( 'wisdm-ld-custom-report-type-select', {
				detail: { report_selector: custom_report_type },
			} )
		);
	}

	handleQuizCourseChange( selected_course ) {
		if ( null == selected_course ) {
			this.setState( {
				selected_courses: { value: -1 },
				selectedValue: { course_filter: -1 },
				selectedCourseTitle: 'All',
				quizes: this.default_quizes, // cspell:disable-line
				groups: this.default_groups,
			} );
		} else {
			// cspell:disable-next-line
			const course_quizes = this.getCourseQuizes(
				selected_course.value,
				this.default_quizes // cspell:disable-line
			);
			const course_groups = this.getCourseGroups(
				selected_course.value,
				this.default_groups
			);
			this.setState( {
				selected_courses: selected_course,
				selectedValue: { course_filter: selected_course },
				selectedCourseTitle: selected_course.label,
				quizes: course_quizes, // cspell:disable-line
				selectedValue: { quiz_filter: -1 },
				selectedQuizTitle: __( 'All', 'learndash-reports-pro' ),
				groups: course_groups,
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

	getCourseGroups( course_id, group_list = [] ) {
		const course_groups = [];
		if ( group_list.length > 0 ) {
			group_list.forEach( function ( group ) {
				if ( group.courses_enrolled.includes( course_id ) ) {
					course_groups.push( group );
				}
			} );
		}
		return course_groups;
	}

	handleQuizGroupChange( groups_selected ) {
		if ( null == groups_selected ) {
			this.setState( {
				selected_groups: {
					value: -1,
					label: __( 'All', 'learndash-reports-pro' ),
				},
				selectedValue: { group_filter: -1 },
			} );
		} else {
			this.setState( {
				selected_groups: groups_selected,
				selectedValue: { group_filter: groups_selected },
				selectedGroupTitle: groups_selected.label,
			} );
		}
	}

	handleCourseSearch( inputString, callback ) {
		// perform a request
		let callback_path = '/ldlms/v1/sfwd-courses/?search=';
		const requestResults = [];
		if ( 2 < inputString.length ) {
			callback_path = callback_path + inputString;
			if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
				callback_path +=
					'&wpml_lang=' +
					wisdm_ld_reports_common_script_data.wpml_lang;
			}
			wp.apiFetch( {
				path: callback_path, //Replace with the correct API
			} )
				.then( ( response ) => {
					if ( false != response && response.length > 0 ) {
						response.forEach( ( element ) => {
							requestResults.push( {
								value: element.id,
								label: element.title.rendered,
							} );
						} );
					}
					callback( requestResults );
				} )
				.catch( ( error ) => {
					callback( requestResults );
				} );
		}
	}

	handleGroupSearch( inputString, callback ) {
		// perform a request
		let callback_path = '/ldlms/v1/groups/?search=';
		const requestResults = [];
		if ( 2 < inputString.length ) {
			callback_path = callback_path + inputString;
			if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
				callback_path +=
					'&wpml_lang=' +
					wisdm_ld_reports_common_script_data.wpml_lang;
			}
			wp.apiFetch( {
				path: callback_path, //Replace with the correct API
			} )
				.then( ( response ) => {
					if ( false != response && response.length > 0 ) {
						response.forEach( ( element ) => {
							requestResults.push( {
								value: element.id,
								label: element.title.rendered,
							} );
						} );
					}
					callback( requestResults );
				} )
				.catch( ( error ) => {
					callback( requestResults );
				} );
		}
	}

	handleUserSearch( inputString, callback ) {
		// perform a request
		const requestResults = [];
		if ( 3 > inputString.length ) {
			return callback( requestResults );
		}
		if ( 'group_leader' == wisdmLdReportsGetUserType() ) {
			const groupUsers = wrldGetGroupAdminUsers();
			groupUsers.forEach( ( user ) => {
				if (
					user.display_name
						.toLowerCase()
						.includes( inputString.toLowerCase() ) ||
					user.user_nicename
						.toLowerCase()
						.includes( inputString.toLowerCase() )
				) {
					requestResults.push( {
						value: user.id,
						label: user.display_name,
					} );
				}
			} );
			callback( requestResults );
		} else {
			let callback_path = '/rp/v1/learners?search=' + inputString;
			if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
				callback_path +=
					'&wpml_lang=' +
					wisdm_ld_reports_common_script_data.wpml_lang;
			}
			wp.apiFetch( {
				path: callback_path,
			} )
				.then( ( response ) => {
					if ( false != response && response.posts.length > 0 ) {
						response.posts.forEach( ( element ) => {
							requestResults.push( {
								value: element.ID,
								label: element.name,
							} );
						} );
					}
					callback( requestResults );
				} )
				.catch( ( error ) => {
					callback( requestResults );
				} );
		}
	}

	openCustomizePreviewModal() {
		this.setState( {
			show_quiz_filter_modal: true,
		} );
	}

	closeCustomizePreviewModal() {
		this.setState( {
			show_quiz_filter_modal: false,
		} );
	}

	applyQuizFilters() {
		if ( null != this.state.selectedElementsInDefaultFilter ) {
			const selection_label =
				this.state.selectedElementsInDefaultFilter.label;
			const selection_type =
				this.state.selectedElementsInDefaultFilter.type;
			const selection_id =
				this.state.selectedElementsInDefaultFilter.value;
			const defaultQuizReport = new CustomEvent(
				'wisdm-ld-reports-default-quiz-report-filters-applied',
				{
					detail: {
						start_date: this.state.start_date,
						end_date: this.state.end_date,
						selection_label,
						selection_type,
						selection_id,
					},
				}
			);
			document.dispatchEvent( defaultQuizReport );
		}
	}

	previewReport() {
		// const defaultCustomQuizReport = new CustomEvent("wisdm-ld-reports-default-custom-quiz-report-filters-applied", {
		//     "detail": {
		//                'start_date':this.state.start_date,
		//                'end_date':this.state.end_date,
		//                'selected_courses': this.state.selected_courses.value,
		//                'selected_groups': this.state.selected_groups.value,
		//                'selected_quizes': this.state.selected_quizes.value, // cspell:disable-line
		//             }});
		// document.dispatchEvent(defaultCustomQuizReport);
		this.previewCustomReport();
	}

	previewCustomReport() {
		const fields_selected = {};
		const course_completion_dates_from = jQuery(
			'#course-completion-from-date'
		).val();
		const course_completion_dates_to = jQuery(
			'#course-completion-to-date'
		).val();
		jQuery( '.quiz-filter-modal' )
			.find( 'input[type=checkbox]' )
			.each( function ( ind, el ) {
				if ( jQuery( el ).is( ':checked' ) ) {
					const index = jQuery( el ).attr( 'name' );
					fields_selected[ index ] = jQuery( el ).val();
				}
			} );
		jQuery( '.quiz-filter-modal' )
			.find( 'select, input[type=text]' )
			.each( function ( ind, el ) {
				const index = jQuery( el ).attr( 'name' );
				fields_selected[ index ] = jQuery( el ).val();
			} );

		// fields_selected['category_filter'] = this.state.selected_categories.value;
		fields_selected.course_filter = this.state.selected_courses.value;
		fields_selected.group_filter = this.state.selected_groups.value;
		fields_selected.quiz_filter = this.state.selected_quizes.value; // cspell:disable-line

		this.setState( { selectedFields: fields_selected } );

		const customQuizReport = new CustomEvent(
			'wisdm-ld-reports-custom-quiz-report-filters-applied',
			{
				detail: {
					start_date: this.state.start_date,
					end_date: this.state.end_date,
					course_completion_dates_from,
					course_completion_dates_to,
					fields_selected,
					selected_courses: this.state.selected_courses.value,
					selected_groups: this.state.selected_groups.value,
					selected_quizes: this.state.selected_quizes.value, // cspell:disable-line
				},
			}
		);
		document.dispatchEvent( customQuizReport );
		this.closeCustomizePreviewModal();
	}

	render() {
		let body = '';
		//Default Filers
		let filterSection = (
			<div className="quiz-eporting-filter-section default-filters">
				<div className="selector search-input">
					<div className="selector-label">
						{ __( 'Search', 'learndash-reports-pro' ) }
						{ this.state.lock_icon }
					</div>
					<div className="select-control">
						<AsyncSelect
							components={ {
								DropdownIndicator: () => null,
								IndicatorSeparator: () => null,
								NoOptionsMessage: ( element ) => {
									return element.selectProps.inputValue
										.length > 2
										? sprintf(
												// Translators: %1$s: quizzes label, %2$s: courses label.
												__(
													" No learners/%1$s/%2$s found for the search string '" +
														element.selectProps
															.inputValue +
														"'",
													'learndash-reports-pro'
												),
												wisdm_reports_get_ld_custom_lebel_if_avaiable(
													'Quizzes',
													'lower'
												),
												wisdm_reports_get_ld_custom_lebel_if_avaiable(
													'Courses',
													'lower'
												)
										  )
										: __(
												' Type 3 or more letters to search',
												'learndash-reports-pro'
										  );
								},
							} }
							closeMenuOnSelect={ false }
							placeholder={ sprintf(
								// Translators: %1$s: quiz label, %2$s: course label.
								__(
									'Search any user, %1$s or %2$s',
									'learndash-reports-pro'
								),
								wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'quiz',
									'lower'
								),
								wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'course',
									'lower'
								)
							) }
							loadOptions={ this.handleQuizFilterDefaultSearch }
							onChange={ this.handleDefaultQuizFilterChange }
							isClearable="true"
						/>
					</div>
				</div>
				<div className="selector button-filter">
					<div className="apply-filters">
						<button onClick={ this.applyQuizFilters }>
							{ __( 'Show Reports', 'learndash-reports-pro' ) }
						</button>
					</div>
				</div>
			</div>
		);
		//Custom Filers
		if ( 'custom-quiz-reports' === this.state.report_type_selected ) {
			const customFilterDropDowns = (
				<div className="quiz-reporting-custom-filters">
					<div className="selector">
						<div className="selector-label">
							{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
								'Courses'
							) }
							{ this.state.lock_icon }
						</div>
						<div className="select-control">
							<Select
								isDisabled={ this.state.courses_disabled }
								// loadOptions={this.handleCourseSearch}
								options={ this.state.courses }
								placeholder={ __(
									'All',
									'learndash-reports-pro'
								) }
								onChange={ this.handleQuizCourseChange }
								isClearable="true"
								value={ {
									value: this.state.selectedValue
										.course_filter,
									label: this.state.selectedCourseTitle,
								} }
							/>
						</div>
					</div>
					<div className="selector">
						<div className="selector-label">
							{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
								'Groups'
							) }
							{ this.state.lock_icon }
						</div>
						<div className="select-control">
							<Select
								onChange={ this.handleQuizGroupChange }
								options={ this.state.groups }
								placeholder={ __(
									'All',
									'learndash-reports-pro'
								) }
								isClearable="true"
								value={ this.state.selected_groups }
							/>
						</div>
					</div>
					<div className="selector">
						<div className="selector-label">
							{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
								'Quizzes'
							) }
							{ this.state.lock_icon }
						</div>
						<div className="select-control">
							<Select
								// loadOptions={this.handleQuizSearch}
								onChange={ this.handleQuizChange }
								// cspell:disable-next-line
								options={ this.state.quizes }
								placeholder={ __(
									'All',
									'learndash-reports-pro'
								) }
								isClearable="true"
								value={ {
									value: this.state.selectedValue.quiz_filter,
									label: this.state.selectedQuizTitle,
								} }
							/>
						</div>
					</div>
				</div>
			);
			filterSection = (
				<div className="quiz-eporting-filter-section custom-filters">
					<div className="help-section">
						<p>
							{ sprintf(
								// Translators: %s: quiz label.
								__(
									'Customize %s reports view helps you analyze exam results in more detailed manner, please select the filters you need from th form below, and click the "Preview Report" to display the reports here.',
									'learndash-reports-pro'
								),
								wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Quiz',
									'lower'
								)
							) }
						</p>
						<p className="note">
							<b>{ __( 'Note:', 'learndash-reports-pro' ) }</b>
							{ __(
								' It may take a while for a report to be generated depending of the amount of the data selected.',
								'learndash-reports-pro'
							) }
						</p>
					</div>
					{ customFilterDropDowns }
					<div className="filter-buttons">
						<div className="filter-button-container">
							{ this.state.show_quiz_filter_modal && (
								<Modal
									onRequestClose={
										this.closeCustomizePreviewModal
									}
									className={
										'learndash-propanel-modal customize-preview-modal'
									}
								>
									<div className="quiz-filter-modal">
										<div className="header">
											<h2>
												{ __(
													'Customize Report',
													'learndash-reports-pro'
												) }
											</h2>
										</div>
										<div className="quiz-reporting-custom-filters lr-dropdowns">
											<div className="selector">
												<div className="selector-label">
													{ __(
														'Custom Report Fields',
														'learndash-reports-pro'
													) }
													{ this.state.lock_icon }
												</div>
												<div className="select-control">
													<Checkbox
														isChecked="yes"
														always_checked="yes"
														name="user_name"
														label={ __(
															'User Name',
															'learndash-reports-pro'
														) }
													/>
													<Checkbox
														isChecked="yes"
														always_checked="yes"
														name="quiz_title"
														label={ __(
															'Exam Title',
															'learndash-reports-pro'
														) }
													/>
													<Checkbox
														isChecked="yes"
														always_checked="yes"
														name="course_title"
														label={ wisdm_reports_get_ld_custom_lebel_if_avaiable(
															'Course'
														) }
													/>
													<Checkbox
														isChecked={
															this.state
																.selectedFields
																.course_category
														}
														name="course_category"
														label={ sprintf(
															// Translators: %s: Course label.
															__(
																'%s Category',
																'learndash-reports-pro'
															),
															wisdm_reports_get_ld_custom_lebel_if_avaiable(
																'Course'
															)
														) }
													/>
													<Checkbox
														isChecked={
															this.state
																.selectedFields
																.group_name
														}
														name="group_name"
														label={ wisdm_reports_get_ld_custom_lebel_if_avaiable(
															'Group'
														) }
													/>
													<Checkbox
														isChecked={
															this.state
																.selectedFields
																.user_email
														}
														name="user_email"
														label={ __(
															'User Email',
															'learndash-reports-pro'
														) }
													/>
													<Checkbox
														isChecked={
															this.state
																.selectedFields
																.quiz_status
														}
														name="quiz_status"
														label={ __(
															'Exam Status',
															'learndash-reports-pro'
														) }
													/>
													<Checkbox
														isChecked="yes"
														always_checked="yes"
														name="quiz_category"
														label={ __(
															'Exam Category',
															'learndash-reports-pro'
														) }
													/>
													<Checkbox
														isChecked="yes"
														always_checked="yes"
														name="quiz_points_earned"
														label={ __(
															'Points Earned',
															'learndash-reports-pro'
														) }
													/>
													<Checkbox
														isChecked={
															this.state
																.selectedFields
																.quiz_score_percent
														}
														name="quiz_score_percent"
														label={ __(
															'Score (in%)',
															'learndash-reports-pro'
														) }
													/>
													<Checkbox
														isChecked="yes"
														always_checked="yes"
														name="date_of_attempt"
														label={ __(
															'Date of attempt',
															'learndash-reports-pro'
														) }
													/>
													<Checkbox
														isChecked="yes"
														always_checked="yes"
														name="time_taken"
														label={ __(
															'Time Taken',
															'learndash-reports-pro'
														) }
													/>
												</div>
											</div>
											<div className="selector">
												<div className="selector-label">
													{ sprintf(
														// Translators: Question label.
														__(
															'%s Response Report Fields',
															'learndash-reports-pro'
														),
														wisdm_reports_get_ld_custom_lebel_if_avaiable(
															'Question'
														)
													) }
													{ this.state.lock_icon }
												</div>
												<div className="select-control">
													<Checkbox
														isChecked={
															this.state
																.selectedFields
																.question_type
														}
														name="question_type"
														label={ sprintf(
															// Translators: Question label.
															__(
																'%s Type',
																'learndash-reports-pro'
															),
															wisdm_reports_get_ld_custom_lebel_if_avaiable(
																'Question'
															)
														) }
													/>
													<Checkbox
														isChecked={
															this.state
																.selectedFields
																.user_first_name
														}
														name="user_first_name"
														label={ __(
															'First Name',
															'learndash-reports-pro'
														) }
													/>
													<Checkbox
														isChecked={
															this.state
																.selectedFields
																.user_last_name
														}
														name="user_last_name"
														label={ __(
															'Last Name',
															'learndash-reports-pro'
														) }
													/>
												</div>
											</div>
										</div>
										<div className="modal-action-buttons">
											<button
												className="button-customize-preview cancel"
												onClick={
													this
														.closeCustomizePreviewModal
												}
											>
												{ __(
													'Cancel',
													'learndash-reports-pro'
												) }
											</button>
											<button
												className="button-quiz-preview"
												onClick={
													this.previewCustomReport
												}
											>
												{ __(
													'Apply',
													'learndash-reports-pro'
												) }
											</button>
										</div>
									</div>
								</Modal>
							) }
							<button
								className="button-customize-preview"
								onClick={ this.openCustomizePreviewModal }
							>
								{ __(
									'CUSTOMIZE REPORT',
									'learndash-reports-pro'
								) }
							</button>
							<button
								className="button-quiz-preview"
								onClick={ this.previewReport }
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
		if ( 'disabled' == this.quiz_section_disabled ) {
			body = '';
		} else {
			// cspell:disable-next-line
			const default_quizz_reports_label =
				__( 'Default', 'learndash-reports-pro' ) +
				' ' +
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) +
				' ' +
				__( 'Report View', 'learndash-reports-pro' );
			// cspell:disable-next-line
			const custom_quizz_reports_label =
				__( 'Customized', 'learndash-reports-pro' ) +
				' ' +
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) +
				' ' +
				__( 'Report View', 'learndash-reports-pro' );
			body = (
				<div className="quiz-report-filters-wrapper">
					<div className="select-view">
						<span>
							{ __( 'Select View', 'learndash-reports-pro' ) }
						</span>
					</div>
					<div className="quiz-report-types">
						<input
							id="dfr"
							type="radio"
							value="default-quiz-reports"
							name="quiz-report-types"
							checked={
								'default-quiz-reports' ===
								this.state.report_type_selected
							}
							onChange={ this.onQuizReportViewChange }
						/>
						{ /* cspell:disable-next-line */ }
						<label
							htmlFor="dfr"
							className={
								'default-quiz-reports' ===
								this.state.report_type_selected
									? 'checked'
									: ''
							}
						>
							{ /* cspell:disable-next-line */ }
							{ default_quizz_reports_label }
						</label>
						<input
							id="cqr"
							type="radio"
							value="custom-quiz-reports"
							name="quiz-report-types"
							checked={
								'custom-quiz-reports' ===
								this.state.report_type_selected
							}
							onChange={ this.onQuizReportViewChange }
						/>
						<label
							htmlFor="cqr"
							className={
								'custom-quiz-reports' ===
								this.state.report_type_selected
									? 'checked'
									: ''
							}
						>
							{ ' ' }
							{ /* cspell:disable-next-line */ }
							{ custom_quizz_reports_label }
						</label>
					</div>
					<div>{ filterSection }</div>
				</div>
			);
		}
		return body;
	}
}

class ReportFilters extends Component {
	constructor( props ) {
		super( props );
		window.callStack = [];
		const learners_disabled = true;
		const categories_disabled = true;
		const groups_disabled = true;
		const courses_disabled = false;
		let localized_data_url = '/rp/v1/report-filters-data';
		this.state = {
			isLoaded: false,
			error: null,
			loading_categories: false,
			loading_groups: false,
			loading_courses: false,
			loading_lessons: false,
			loading_topics: false,
			loading_learners: false,
			selected_categories: {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			},
			selected_groups: {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			},
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
			selected_learners: null,
			categories_disabled,
			groups_disabled,
			courses_disabled,
			lessons_disabled: true,
			topics_disabled: true,
			courses: [],
			default_courses: [],
			learners_disabled,
			// active_tab:tab_selected,
			start_date: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			).unix(),
			end_date: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			).unix(),
			report_type_selected: 'default-course-reports',
			isPro: wisdm_ld_reports_common_script_data.is_pro_version_active,
		};
		window.globalfilters = {
			detail: {
				start_date: this.state.start_date,
				end_date: this.state.end_date,
				selected_categories: this.state.selected_categories.value,
				selected_groups: this.state.selected_groups.value,
				selected_courses: this.state.selected_courses.value,
				selected_lessons: this.state.selected_lessons.value,
				selected_topics: this.state.selected_topics.value,
				selected_learners:
					null != this.state.selected_learners
						? this.state.selected_learners.value
						: null,
			},
		};
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			localized_data_url +=
				'/?wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		this.localized_data_url = localized_data_url;

		this.durationUpdated = this.durationUpdated.bind( this );
		this.applyFilters = this.applyFilters.bind( this );
		this.handleTabSelection = this.handleTabSelection.bind( this );
		this.changeCourseReportType = this.changeCourseReportType.bind( this );
	}

	/**
	 * Initializes the filters.
	 *
	 * @since 3.0.4
	 *
	 * @return {void}
	 */
	initializeFilters() {
		wp.apiFetch( {
			path: this.localized_data_url, //Replace with the correct API
		} ).then( ( response ) => {
			if ( response.wisdm_ld_reports_common_script_data ) {
				window.wisdm_ld_reports_common_script_data = {
					...window.wisdm_ld_reports_common_script_data,
					...response.wisdm_ld_reports_common_script_data,
				};
			}
			if (
				false !=
				wisdm_ld_reports_common_script_data.is_pro_version_active
			) {
				this.state.learners_disabled = false;
				this.state.categories_disabled = false;
				this.state.groups_disabled = false;
			}
			window.ld_api_settings =
				wisdm_ld_reports_common_script_data.ld_api_settings;
			const tab_selected =
				'quiz-reports' ===
				wisdm_ld_reports_common_script_data.report_type
					? 1
					: 0;
			this.setState( { active_tab: tab_selected } );
			this.getDefaultOptions();
			let url =
				'/ldlms/v1/' +
				ld_api_settings[ 'sfwd-courses' ] +
				'?per_page=-1';
			if (
				wisdm_ld_reports_common_script_data.exclude_courses.length >
					0 &&
				false !=
					wisdm_ld_reports_common_script_data.is_pro_version_active
			) {
				for (
					let i = 0;
					i <
					wisdm_ld_reports_common_script_data.exclude_courses.length;
					i++
				) {
					url +=
						'&exclude[]=' +
						wisdm_ld_reports_common_script_data.exclude_courses[
							i
						];
				}
			}
			// wp.apiFetch({
			//     path: url  //Replace with the correct API
			// }).then(response => {
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
			// let courses     = this.getCourseListFromJson(response);
			const courses = window.wisdm_ld_reports_common_script_data.courses;
			this.setState( {
				isLoaded: true,
				lock_icon,
				quiz_section_disabled,
				categories:
					wisdm_ld_reports_common_script_data.course_categories,
				groups: wisdm_ld_reports_common_script_data.course_groups,
				courses,
				default_courses: courses,
				courses_disabled: false,
				lessons: [],
				topics: [],
				learners: [],
				isPro: wisdm_ld_reports_common_script_data.is_pro_version_active,
			} );
			// });
			const defaultOptionsLoaded = new CustomEvent(
				'wrld-default-filters-loaded'
			);
			document.dispatchEvent( defaultOptionsLoaded );
		} );
	}

	durationUpdated( event ) {
		this.setState( {
			start_date: event.detail.startDate,
			end_date: event.detail.endDate,
		} );
	}

	getCourseListFromJson( response ) {
		let courseList = [];
		if ( response.length == 0 ) {
			return courseList; //no courses found
		}

		for ( let i = 0; i < response.length; i++ ) {
			courseList.push( {
				value: response[ i ].id,
				label: response[ i ].title.rendered,
			} );
		}
		courseList = getCoursesByGroups( courseList );
		return courseList;
	}

	getLessonListFromJson( response ) {
		const lessonList = [];
		if ( response.length == 0 ) {
			return false; //no courses found
		}

		for ( let i = 0; i < response.length; i++ ) {
			lessonList.push( {
				value: response[ i ].id,
				label: response[ i ].title.rendered,
			} );
		}
		return lessonList;
	}

	getTopicListFromJson( response ) {
		const topicList = [];
		if ( response.length == 0 ) {
			return false; //no courses found
		}

		for ( let i = 0; i < response.length; i++ ) {
			topicList.push( {
				value: response[ i ].id,
				label: response[ i ].title.rendered,
			} );
		}
		return topicList;
	}

	componentDidMount() {
		// Expose component instance globally so other blocks can access its state.
		window.wisdmReportToolsBlockInstance = this;

		document.addEventListener( 'duration_updated', this.durationUpdated );
		this.initializeFilters();
	}

	componentWillUnmount() {
		// Clean up global reference when component unmounts.
		if ( window.wisdmReportToolsBlockInstance === this ) {
			delete window.wisdmReportToolsBlockInstance;
		}
	}

	handleCategoryChange = ( selectedCategory ) => {
		if ( null == selectedCategory ) {
			this.setState( {
				selected_categories: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
			this.updateSelectorsFor( 'category', null );
			this.setState( { courses: this.state.default_courses } );
		} else {
			this.setState( { selected_categories: selectedCategory } );
			this.updateSelectorsFor(
				'category',
				selectedCategory.value,
				'/ldlms/v1/' + ld_api_settings[ 'sfwd-courses' ]
			);
		}
	};

	handleGroupChange = ( selectedGroup ) => {
		if ( null == selectedGroup ) {
			this.setState( {
				selected_groups: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
			this.updateSelectorsFor( 'group', null );
			this.setState( { courses: this.state.default_courses } );
		} else {
			this.setState( { selected_groups: selectedGroup } );
			this.updateSelectorsFor(
				'group',
				selectedGroup.value,
				'/ldlms/v1/' +
					ld_api_settings[ 'sfwd-courses' ] +
					'?include=' +
					selectedGroup.courses_enrolled
			);
		}
		//update courses/lessons/topics fetched
		this.setState( { courses_disabled: false } );
	};

	handleAdminGroupChange = ( selectedGroup ) => {
		const categorySelectedByAdmin = this.state.selected_categories.value;
		if ( null == selectedGroup ) {
			this.setState( {
				selected_groups: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
			this.updateSelectorsFor( 'group', null );
			this.setState( {
				courses: this.state.default_courses,
				categories_disabled: false,
			} );
		} else {
			this.setState( {
				selected_groups: selectedGroup,
				categories_disabled: true,
			} );
			let callback_url =
				'/ldlms/v1/' +
				ld_api_settings[ 'sfwd-courses' ] +
				'?include=' +
				selectedGroup.courses_enrolled;
			if ( categorySelectedByAdmin != null ) {
				//including category filter in url
				callback_url =
					callback_url +
					'&ld_course_category[]=' +
					categorySelectedByAdmin;
				let url = '';
				if (
					wisdm_ld_reports_common_script_data.exclude_courses.length >
						0 &&
					false !=
						wisdm_ld_reports_common_script_data.is_pro_version_active
				) {
					for (
						let i = 0;
						i <
						wisdm_ld_reports_common_script_data.exclude_courses
							.length;
						i++
					) {
						url +=
							'&exclude[]=' +
							wisdm_ld_reports_common_script_data.exclude_courses[
								i
							];
					}
				}
				callback_url += url;
			}
			this.updateSelectorsFor(
				'group',
				selectedGroup.value,
				callback_url
			);
		}
		//update courses/lessons/topics fetched
		this.setState( { courses_disabled: false } );
	};

	handleCourseChange = ( selectedCourse ) => {
		if ( null == selectedCourse ) {
			this.setState( {
				selected_courses: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
			this.updateSelectorsFor( 'course', null );
			// Update window.globalfilters immediately when course changes.
			if ( window.globalfilters ) {
				window.globalfilters.detail.selected_courses = null;
			}
		} else {
			this.setState( { selected_courses: selectedCourse } );
			this.updateSelectorsFor(
				'course',
				selectedCourse.value,
				'/ldlms/v1/' + ld_api_settings[ 'sfwd-lessons' ] + '/'
			);
			// Update window.globalfilters immediately when course changes.
			if ( window.globalfilters ) {
				window.globalfilters.detail.selected_courses =
					selectedCourse.value;
			}
		}
	};

	handleLessonChange = ( selectedLesson ) => {
		if ( null == selectedLesson ) {
			this.setState( {
				selected_lessons: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
			this.updateSelectorsFor( 'lesson', null );
		} else {
			this.setState( { selected_lessons: selectedLesson } );
			this.updateSelectorsFor(
				'lesson',
				selectedLesson.value,
				'ldlms/v1/' + ld_api_settings[ 'sfwd-topic' ] + '/'
			);
		}
	};

	handleTopicChange = ( selectedTopic ) => {
		if ( null == selectedTopic ) {
			this.setState( {
				selected_topics: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
			this.updateSelectorsFor( 'topic', null );
		} else {
			this.setState( { selected_topics: selectedTopic } );
			this.updateSelectorsFor( 'topic', selectedTopic.value );
		}
	};

	handleLearnerChange = ( selectedLearner ) => {
		if ( null == selectedLearner ) {
			this.setState( {
				selected_learners: null,
				courses_disabled: false,
				categories_disabled: false,
			} );
			// this.updateSelectorsFor('learner', null);
		} else {
			this.setState( { selected_learners: selectedLearner } );
			this.setState( {
				selected_categories: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
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
			} ); //Clear category, course , lesson, topics selected.
			// this.updateSelectorsFor('learner', selectedLearner.value);
		}
	};

	handleLearnerSearch = ( inputString, callback ) => {
		// perform a request
		const requestResults = [];
		if ( 3 > inputString.length ) {
			return callback( requestResults );
		}
		if ( 'group_leader' == wisdmLdReportsGetUserType() ) {
			const groupUsers = wrldGetGroupAdminUsers();
			groupUsers.forEach( ( user ) => {
				if (
					user.display_name
						.toLowerCase()
						.includes( inputString.toLowerCase() ) ||
					user.user_nicename
						.toLowerCase()
						.includes( inputString.toLowerCase() )
				) {
					requestResults.push( {
						value: user.id,
						label: user.display_name,
					} );
				}
			} );
			callback( requestResults );
		} else {
			let callback_path = '/rp/v1/learners?search=' + inputString;
			if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
				callback_path +=
					'&wpml_lang=' +
					wisdm_ld_reports_common_script_data.wpml_lang;
			}
			wp.apiFetch( {
				path: callback_path,
			} )
				.then( ( response ) => {
					if ( false != response && response.posts.length > 0 ) {
						response.posts.forEach( ( element ) => {
							requestResults.push( {
								value: element.ID,
								label: element.name,
							} );
						} );
					}
					callback( requestResults );
				} )
				.catch( ( error ) => {
					callback( requestResults );
				} );
		}
	};

	getDefaultOptions = () => {
		// perform a request
		const requestResults = [];
		// if (3>inputString.length) {
		//     return callback(requestResults);
		// }

		if ( 'group_leader' == wisdmLdReportsGetUserType() ) {
			const groupUsers = wrldGetGroupAdminUsers();
			groupUsers.forEach( ( user ) => {
				// if (user.display_name.toLowerCase().includes(inputString.toLowerCase()) || user.user_nicename.toLowerCase().includes(inputString.toLowerCase())) {
				//     requestResults.push({value:user.id, label:user.display_name});
				// }

				requestResults.push( {
					value: user.id,
					label: user.display_name,
				} );
			} );
			// return requestResults;
			this.setState( { default_options: requestResults } );
		} else {
			let callback_path = '/rp/v1/learners?page=1&per_page=5';
			if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
				callback_path +=
					'&wpml_lang=' +
					wisdm_ld_reports_common_script_data.wpml_lang;
			}
			wp.apiFetch( {
				path: callback_path,
			} )
				.then( ( response ) => {
					if ( false != response && response.posts.length > 0 ) {
						response.posts.forEach( ( element ) => {
							requestResults.push( {
								value: element.ID,
								label: element.name,
							} );
						} );
					}
					this.setState( { default_options: requestResults } );
				} )
				.catch( ( error ) => {
					return requestResults;
				} );
		}
	};

	updateSelectorsFor(
		element,
		selection,
		callback_path = '/wp/v2/categories'
	) {
		switch ( element ) {
			case 'category':
				callback_path =
					callback_path + '?ld_course_category[]=' + selection;
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
							const courses =
								this.getCourseListFromJson( response );
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
											label: __(
												'All',
												'learndash-reports-pro'
											),
										},
										selected_lessons: {
											value: null,
											label: __(
												'All',
												'learndash-reports-pro'
											),
										},
										selected_topics: {
											value: null,
											label: __(
												'All',
												'learndash-reports-pro'
											),
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
				break;
			case 'group':
				callback_path = callback_path;
				if ( null == selection ) {
					this.setState( {
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
					} ).then( ( response ) => {
						const courses = this.getCourseListFromJson( response );
						if ( false != courses && courses.length > 0 ) {
							this.setState( {
								courses,
								courses_disabled: false,
								loading_courses: false,
							} );
						}
					} );
				}
				break;
			case 'course':
				callback_path = callback_path + '?course=' + selection;
				if ( null == selection ) {
					this.setState( {
						lessons: [],
						topics: [],
						lessons_disabled: true,
						topics_disabled: true,
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
					this.setState( { loading_lessons: true } );
					if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
						callback_path +=
							'&wpml_lang=' +
							wisdm_ld_reports_common_script_data.wpml_lang;
					}
					wp.apiFetch( {
						path: callback_path, //Replace with the correct API
					} )
						.then( ( response ) => {
							const lessons =
								this.getLessonListFromJson( response );
							if ( false != lessons && lessons.length > 0 ) {
								this.setState( {
									selected_lessons: {
										value: null,
										label: __(
											'All',
											'learndash-reports-pro'
										),
									},
									selected_topics: {
										value: null,
										label: __(
											'All',
											'learndash-reports-pro'
										),
									},
									lessons,
									lessons_disabled: false,
									loading_lessons: false,
								} );
							} else {
								this.setState( {
									selected_lessons: {
										value: null,
										label: __(
											'All',
											'learndash-reports-pro'
										),
									},
									selected_topics: {
										value: null,
										label: __(
											'All',
											'learndash-reports-pro'
										),
									},
									lessons,
									lessons_disabled: true,
									loading_lessons: false,
								} );
							}
						} )
						.catch( ( error ) => {
							this.setState( {
								selected_lessons: {
									value: null,
									label: __( 'All', 'learndash-reports-pro' ),
								},
								selected_topics: {
									value: null,
									label: __( 'All', 'learndash-reports-pro' ),
								},
								lessons_disabled: false,
								loading_lessons: false,
							} );
						} );
				}
				break;
			case 'lesson':
				callback_path =
					callback_path +
					'?course=' +
					this.state.selected_courses.value +
					'&lesson=' +
					selection;
				if ( null == selection ) {
					this.setState( {
						topics: [],
						topics_disabled: true,
						selected_topics: {
							value: null,
							label: __( 'All', 'learndash-reports-pro' ),
						},
					} );
				} else {
					this.setState( { loading_topics: true } );
					if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
						callback_path +=
							'&wpml_lang=' +
							wisdm_ld_reports_common_script_data.wpml_lang;
					}
					wp.apiFetch( {
						path: callback_path, //Replace with the correct API
					} ).then( ( response ) => {
						const topics = this.getTopicListFromJson( response );
						if ( false != topics && topics.length > 0 ) {
							this.setState( {
								selected_topics: {
									value: null,
									label: __( 'All', 'learndash-reports-pro' ),
								},
								topics,
								topics_disabled: false,
								loading_topics: false,
							} );
						} else {
							this.setState( {
								selected_topics: {
									value: null,
									label: __( 'All', 'learndash-reports-pro' ),
								},
								topics,
								topics_disabled: true,
								loading_topics: false,
							} );
						}
					} );
				}
				break;
			case 'topic':
				callback_path = callback_path + '?course_topic=' + selection;
				//Callback & action if required.
				break;
			case 'learner':
				callback_path = callback_path + '?learner=' + selection;
				//Callback & action if required.
				break;
			default:
				break;
		}
	}

	/**
	 * Triggers the apply filters event with the
	 */
	applyFilters() {
		const applyFilters = new CustomEvent(
			'wisdm-ld-reports-filters-applied',
			{
				detail: {
					start_date: this.state.start_date,
					end_date: this.state.end_date,
					selected_categories: this.state.selected_categories.value,
					selected_groups: this.state.selected_groups.value,
					selected_courses: this.state.selected_courses.value,
					selected_lessons: this.state.selected_lessons.value,
					selected_topics: this.state.selected_topics.value,
					selected_learners:
						null != this.state.selected_learners
							? this.state.selected_learners.value
							: '',
				},
			}
		);

		if (
			null == this.state.selected_learners &&
			'learner-specific-course-reports' == this.state.report_type_selected
		) {
			alert( 'Please select a learner from the dropdown' );
			return;
		}
		document.dispatchEvent( applyFilters );
	}

	handleTabSelection( tab_key ) {
		this.setState( { active_tab: tab_key } );
		let tabSwitchEvent = new CustomEvent(
			'wisdm-ld-reports-report-type-selected',
			{
				detail: { active_reports_tab: 'default-ld-reports' },
			}
		);
		if ( 1 == tab_key ) {
			tabSwitchEvent = new CustomEvent(
				'wisdm-ld-reports-report-type-selected',
				{
					detail: { active_reports_tab: 'quiz-reports' },
				}
			);
		}
		document.dispatchEvent( tabSwitchEvent );
		if ( 1 == tab_key ) {
			jQuery( '.ld-course-field' ).hide();
		} else {
			jQuery( '.ld-course-field' ).css( 'display', 'flex' );
		}
	}

	changeCourseReportType( event ) {
		this.setState( { report_type_selected: event.target.value } );
		let report_type = '';
		if ( 'default-course-reports' == event.target.value ) {
			report_type = 'default-course-reports';
			this.setState( {
				selected_learners: null,
				lessons_disabled: true,
				topics_disabled: true,
			} );
		} else if ( 'learner-specific-course-reports' == event.target.value ) {
			report_type = 'learner-specific-course-reports';
			this.setState( {
				selected_groups: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
				selected_categories: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
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
		document.dispatchEvent(
			new CustomEvent( 'wisdm-ldrp-course-report-type-changed', {
				detail: { report_type },
			} )
		);
	}

	render() {
		let user_selector_for_demo = '';
		if ( wisdm_ld_reports_common_script_data.is_demo ) {
			user_selector_for_demo = (
				<div className="demo-pre-selection-options">
					<span className="try-searching">(Try Searching)</span>
					<span
						className="sample-name"
						onClick={ () => {
							this.setState( {
								selected_learners: {
									value: 18,
									label: 'Paul John',
								},
							} );
						} }
					>
						Paul John
					</span>
					<span>Or</span>
					<span
						className="sample-name"
						onClick={ () => {
							this.setState( {
								selected_learners: {
									value: 7,
									// cspell:disable-next-line
									label: 'Michelle Schowalter',
								},
							} );
						} }
					>
						{ /* cspell:disable-next-line */ }
						Michelle Schowalter
					</span>
					<span>)</span>
				</div>
			);
		}
		let upgrade_section = '';
		let proClass = 'select-control';
		let wrldPlaceholder = __( 'Search', 'learndash-reports-pro' );
		let quiz_section = <DummyFilters></DummyFilters>;
		let gl_class = '';
		const userType = wisdmLdReportsGetUserType();
		if ( this.state.isPro ) {
			quiz_section = <QuizFilters></QuizFilters>;
		}
		if ( true != this.state.isPro ) {
			upgrade_section = (
				<div className="wrld-pro-note">
					<div className="wrld-pro-note-content">
						<span>
							<b>{ __( 'Note: ', 'learndash-reports-pro' ) }</b>
							{ __(
								'Below is the dummy representation of the Learner Reports available in ProPanel.',
								'learndash-reports-pro'
							) }
						</span>
					</div>
				</div>
			);
			proClass = 'ldr-pro';
			wrldPlaceholder = __( 'PAUL JOHN', 'learndash-reports-pro' );
			if ( 'group_leader' == userType || 'instructor' == userType ) {
				gl_class = 'wrld-gl';
			}
		}

		let bodyContent = null;
		if ( ! this.state.isLoaded ) {
			// yet loading
			bodyContent = <WisdmLoader key="wisdm-loader-report-filters-editor" />;
		} else if ( this.state.error ) {
			// error
			bodyContent = (
				<div className="wisdm-learndash-reports-chart-block error">
					<div>{ this.state.error.message }</div>
				</div>
			);
		} else {
			let conditionalCategoryGroupSelector = '';
			let conditionalAdminGroup = '';
			const userType = wisdmLdReportsGetUserType();
			if ( 'administrator' == userType ) {
				conditionalCategoryGroupSelector = null;
				conditionalAdminGroup = (
					<div
						className={
							'wisdm-learndash-reports-report-filters admin-group-category-container ' +
							this.state.report_type_selected
						}
					>
						<div className="selector admin-cg-selector">
							<div className="selector-label">
								{ ' ' }
								{ __( 'Categories', 'learndash-reports-pro' ) }
								{ this.state.lock_icon }
							</div>
							<div className={ proClass }>
								<Select
									isDisabled={
										this.state.categories_disabled
									}
									isLoading={ this.state.loading_categories }
									onChange={ this.handleCategoryChange }
									options={ this.state.categories }
									value={ this.state.selected_categories }
									isClearable="true"
								/>
							</div>
						</div>
						<div className="selector admin-cg-selector">
							<div className="selector-label">
								{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Groups'
								) }
								{ this.state.lock_icon }
							</div>
							<div className="select-control">
								<Select
									isDisabled={ this.state.groups_disabled }
									isLoading={ this.state.loading_groups }
									onChange={ this.handleAdminGroupChange }
									options={ this.state.groups }
									value={ this.state.selected_groups }
									isClearable="true"
								/>
							</div>
						</div>
						<div className="selector admin-cg-selector d-none"></div>
					</div>
				);
			} else if ( 'group_leader' == userType ) {
				conditionalCategoryGroupSelector = (
					<div className="selector">
						<div className="selector-label">
							{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
								'Groups'
							) }
							{ this.state.lock_icon }
						</div>
						<div className="select-control">
							<Select
								isDisabled={ this.state.groups_disabled }
								isLoading={ this.state.loading_groups }
								onChange={ this.handleGroupChange }
								options={ this.state.groups }
								value={ this.state.selected_groups }
								isClearable="true"
							/>
						</div>
					</div>
				);
			}
			let tabQR = (
				<Tab>
					{ this.state.lock_icon }{ ' ' }
					<span className="wrld-labels">
						{ /* cspell:disable-next-line */ }
						{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'Quiz'
						) +
							' ' +
							__( 'Reports ', 'learndash-reports-pro' ) }
					</span>
				</Tab>
			);

			if ( this.state.quiz_section_disabled == 'disabled' ) {
				if ( 'group_leader' == userType || 'instructor' == userType ) {
					tabQR = (
						<Tab disabled>
							{ this.state.lock_icon }{ ' ' }
							<span className="wrld-labels">
								{ /* cspell:disable-next-line */ }
								{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Quiz'
								) +
									' ' +
									__( 'Reports ', 'learndash-reports-pro' ) }
							</span>
						</Tab>
					);
				} else {
					tabQR = (
						<Tab>
							<span className="wrld-labels">
								{ /* cspell:disable-next-line */ }
								{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Quiz'
								) +
									' ' +
									__( 'Reports ', 'learndash-reports-pro' ) }
							</span>
						</Tab>
					);
				}
			}
			bodyContent = (
				<Tabs
					selectedIndex={ this.state.active_tab }
					onSelect={ this.handleTabSelection }
					key="report-filters-content-editor"
				>
					<TabList>
						<Tab>
							<span className="wrld-labels">
								{ /* cspell:disable-next-line */ }
								{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Course'
								) + __( ' Reports', 'learndash-reports-pro' ) }
							</span>
						</Tab>
						{ tabQR }
					</TabList>
					<TabPanel>
						<div className="wisdm-learndash-reports-course-report-tools-wrap">
							<div className="course-report-by">
								<input
									id="csr"
									type="radio"
									value="default-course-reports"
									name="course-report-types"
									checked={
										'default-course-reports' ===
										this.state.report_type_selected
									}
									onChange={ this.changeCourseReportType }
								/>
								<label
									htmlFor="csr"
									className={
										'default-course-reports' ===
										this.state.report_type_selected
											? 'checked'
											: ''
									}
								>
									<span className="wrld-labels">
										{ /* cspell:disable-next-line */ }
										{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'Course'
										) +
											__(
												' Specific Reports',
												'learndash-reports-pro'
											) }
									</span>
								</label>
								<input
									id="lsr"
									className={ gl_class }
									type="radio"
									value="learner-specific-course-reports"
									name="course-report-types"
									checked={
										'learner-specific-course-reports' ===
										this.state.report_type_selected
									}
									onChange={ this.changeCourseReportType }
								/>
								<label
									id={ gl_class }
									htmlFor="lsr"
									className={
										'learner-specific-course-reports' ===
										this.state.report_type_selected
											? 'checked'
											: ''
									}
								>
									{ ' ' }
									{ __(
										'Learner Specific Reports',
										'learndash-reports-pro'
									) }
								</label>
							</div>
							{ 'learner-specific-course-reports' ===
							this.state.report_type_selected
								? ''
								: conditionalAdminGroup }
							<div
								className={
									'wisdm-learndash-reports-report-filters ' +
									this.state.report_type_selected
								}
							>
								{ conditionalCategoryGroupSelector }
								<div className="selector">
									<div className="selector-label">
										{ /* cspell:disable-next-line */ }
										{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'Courses'
										) }
									</div>
									<div className="select-control">
										<Select
											isDisabled={
												this.state.courses_disabled
											}
											isLoading={
												this.state.loading_courses
											}
											onChange={ this.handleCourseChange }
											options={ this.state.courses }
											value={
												this.state.selected_courses
											}
											isClearable="true"
										/>
									</div>
								</div>
								<div className="selector">
									<div className="selector-label">
										{ /* cspell:disable-next-line */ }
										{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'Lessons'
										) }
									</div>
									<div className="select-control">
										<Select
											isDisabled={
												this.state.lessons_disabled
											}
											isLoading={
												this.state.loading_lessons
											}
											onChange={ this.handleLessonChange }
											options={ this.state.lessons }
											value={
												this.state.selected_lessons
											}
											isClearable="true"
										/>
									</div>
								</div>
								<div className="selector">
									<div className="selector-label">
										{ /* cspell:disable-next-line */ }
										{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'Topics'
										) }
									</div>
									<div className="select-control">
										<Select
											isDisabled={
												this.state.topics_disabled
											}
											isLoading={
												this.state.loading_topics
											}
											onChange={ this.handleTopicChange }
											options={ this.state.topics }
											value={ this.state.selected_topics }
											isClearable="true"
										/>
									</div>
								</div>
								<div className="selector lr-apply">
									<div className="apply-filters">
										<button onClick={ this.applyFilters }>
											{ __(
												'Apply',
												'learndash-reports-pro'
											) }
										</button>
									</div>
								</div>
							</div>
							<div
								className={
									'wisdm-learndash-reports-report-filters-for-users ' +
									this.state.report_type_selected
								}
							>
								{ upgrade_section }
								<div className="selector lr-learner">
									<div className="selector-label">
										{ __(
											'Learners',
											'learndash-reports-pro'
										) }
										{ this.state.lock_icon }
										{ user_selector_for_demo }
									</div>
									<div className={ proClass }>
										<AsyncSelect
											components={ {
												DropdownIndicator: () => null,
												IndicatorSeparator: () => null,
												NoOptionsMessage: (
													element
												) => {
													return element.selectProps
														.inputValue.length > 2
														? __(
																" No learners found for the search string '" +
																	element
																		.selectProps
																		.inputValue +
																	"'",
																'learndash-reports-pro'
														  )
														: __(
																' Type 3 or more letters to search',
																'learndash-reports-pro'
														  );
												},
											} }
											placeholder={ wrldPlaceholder }
											isDisabled={
												this.state.learners_disabled
											}
											value={
												this.state.selected_learners
											}
											loadOptions={
												this.handleLearnerSearch
											}
											onChange={
												this.handleLearnerChange
											}
											isClearable="true"
											defaultOptions={
												this.state.default_options
											}
										/>
									</div>
								</div>
								<div className="selector">
									<div className="apply-filters">
										<button onClick={ this.applyFilters }>
											{ __(
												'Apply',
												'learndash-reports-pro'
											) }
										</button>
										<span className="wrld-applied">
											<i className="dashicons dashicons-saved"></i>
											{ __(
												'Applied',
												'learndash-reports-pro'
											) }
										</span>
									</div>
								</div>
							</div>
						</div>
					</TabPanel>
					<TabPanel>{ quiz_section }</TabPanel>
				</Tabs>
			);
		}

		// Always return a stable wrapper to prevent React DOM errors during unmounting.
		return (
			<div
				className="wisdm-learndash-reports-chart-block"
				id="wisdm-learndash-report-filters-container"
				key="report-filters-content-editor"
			>
				{ bodyContent }
			</div>
		);
	}
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

function getCoursesByGroups( courseList ) {
	const user_type = wisdmLdReportsGetUserType();
	let filtered_courses = [];
	if ( 'group_leader' == user_type ) {
		const course_groups = wisdm_ld_reports_common_script_data.course_groups;
		const group_course_list = [];
		if ( course_groups.length > 0 ) {
			course_groups.forEach( function ( course_group ) {
				const courses = course_group.courses_enrolled;
				courses.forEach( function ( course_id ) {
					if ( ! group_course_list.includes( course_id ) ) {
						group_course_list.push( course_id );
					}
				} );
			} );
		}

		if ( group_course_list.length > 0 ) {
			courseList.forEach( function ( course ) {
				if ( group_course_list.includes( course.value ) ) {
					filtered_courses.push( course );
				}
			} );
		}
	} else if ( 'instructor' == user_type ) {
		filtered_courses = wisdm_ld_reports_common_script_data.courses;
	} else {
		filtered_courses = courseList;
	}
	return filtered_courses;
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

function getSelectionByValueId( selectionId, list = [] ) {
	let selectedItem = {
		value: -1,
		label: __( 'All', 'learndash-reports-pro' ),
	};
	if ( -1 == selectionId ) {
		return selectedItem;
	}

	if ( list.length > 0 ) {
		list.forEach( function ( item ) {
			if ( selectionId == item.value ) {
				selectedItem = item;
			}
		} );
	}
	return selectedItem;
}

/**
 * If user is the group admin this function returns an array of unique
 * user ids which are enrolled in the groups accessible to the current user.
 */
function wrldGetGroupAdminUsers() {
	const user_accessible_groups =
		wisdm_ld_reports_common_script_data.course_groups;

	const allGroupUsers = Array();
	const includedUserIds = Array();
	if ( user_accessible_groups.length < 1 ) {
		return allGroupUsers;
	}

	user_accessible_groups.forEach( function ( group ) {
		const groupUsers = group.group_users;
		groupUsers.forEach( function ( user ) {
			if ( ! includedUserIds.includes( user.id ) ) {
				allGroupUsers.push( user );
				includedUserIds.push( user.id );
			}
		} );
	} );

	return allGroupUsers;
}

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-report-filters front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );

		root.render( React.createElement( ReportFilters ) );
	}
} );

registerBlockType( 'wisdm-learndash-reports/report-filters', {
	title: __( 'Report Tools', 'learndash-reports-pro' ),
	description: __(
		'A block with duration selectors for the ProPanel',
		'learndash-reports-pro'
	),
	category: 'wisdm-learndash-reports',
	className: 'learndash-reports-by-wisdmlabs-report-filters',
	icon,
	apiVersion: '3',
	attributes: {
		blockContent: {
			type: 'html',
			default: '',
		},
	},

	/**
	 * edit function
	 *
	 * Makes the markup for the editor interface.
	 *
	 * @param {Object} ObjectArgs {
	 *                            className - Automatic CSS class. Based on the block name: gutenberg-block-samples-block-simple
	 *                            }
	 *
	 * @param          props
	 * @return {JSX object} ECMAScript JSX Markup for the editor
	 */
	edit( props ) {
		return (
			<div { ...useBlockProps() }>
				<ReportFilters></ReportFilters>
			</div>
		);
	},

	/**
	 * save function
	 *
	 * Makes the markup that will be rendered on the site page
	 *
	 * @return {JSX object} ECMAScript JSX Markup for the site
	 */
	save() {
		return (
			<div { ...useBlockProps.save() }>
				<div className="wisdm-learndash-reports-report-filters front"></div>
			</div>
		);
	},
} );
