// cspell:ignore globalfilters eporting

import './index.scss';
import Select, {
	AsyncSelect,
} from '../commons/emotion-styles-provider/select.js';
const moment = require( 'moment' );

import { Tab, Tabs, TabList, TabPanel } from 'react-tabs';
import 'react-tabs/style/react-tabs.css';
import { __, sprintf } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
import React, { Component, CSSProperties } from 'react';
import WisdmLoader from '../commons/loader/index.js';
import DummyFilters from '../dummy-quiz-reports/index.js';
import { array } from 'prop-types';
import { Modal } from '@wordpress/components';
import ComponentDatepicker from './component-date-filter.js';

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

class QuizFilters extends Component {
	constructor( props ) {
		super( props );

		const selected_course = {
			value: -1,
			label: __( 'All', 'learndash-reports-pro' ),
		};
		const selected_group = {
			value: -1,
			label: __( 'All', 'learndash-reports-pro' ),
		};
		const selected_quiz = {
			value: -1,
			label: __( 'All', 'learndash-reports-pro' ),
		};
		const start = moment(
			new Date( wisdm_ld_reports_common_script_data.start_date )
		).unix();
		const end = moment(
			new Date( wisdm_ld_reports_common_script_data.end_date )
		).unix();
		this.state = {
			isLoaded: false,
			error: null,
			report_type_selected: 'default-quiz-reports',
			courses_disabled: '',
			groups_disabled: false,
			// cspell:disable-next-line
			quizes_disabled: false,
			show_quiz_filter_modal: false,
			show_bulk_export_modal: false,
			show_bulk_attempt_progress: 'wrld-hidden',
			show_bulk_learner_progress: 'wrld-hidden',
			show_bulk_attempt_download: 'wrld-hidden',
			show_bulk_learner_download: 'wrld-hidden',
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
			export_start_date: start,
			export_end_date: end,
			selectedValue: wisdm_ld_reports_common_script_data.report_preferences?.settings,
			selectedFields: wisdm_ld_reports_common_script_data.report_preferences?.settings,
			selectedCourseTitle: wisdm_ld_reports_common_script_data.report_preferences?.selected_course_title,
			selectedGroupTitle: wisdm_ld_reports_common_script_data.report_preferences?.selected_group_title,
			selectedQuizTitle: wisdm_ld_reports_common_script_data.report_preferences?.selected_quiz_title,
			disabled_button: true,
			isPro: wisdm_ld_reports_common_script_data.is_pro_version_active,
		};

		this.durationUpdated = this.durationUpdated.bind( this );
		this.dateUpdated = this.dateUpdated.bind( this );
		this.onQuizReportViewChange = this.onQuizReportViewChange.bind( this );
		this.handleQuizFilterDefaultSearch =
			this.handleQuizFilterDefaultSearch.bind( this );
		this.openCustomizePreviewModal =
			this.openCustomizePreviewModal.bind( this );
		this.openBulkExportModal = this.openBulkExportModal.bind( this );
		this.openBulkProgressModal = this.openBulkProgressModal.bind( this );
		this.closeCustomizePreviewModal =
			this.closeCustomizePreviewModal.bind( this );
		this.closeBulkExportModal = this.closeBulkExportModal.bind( this );
		// this.closeBulkProgressModal        = this.closeBulkProgressModal.bind(this);
		this.handleQuizSearch = this.handleQuizSearch.bind( this );
		this.handleCourseSearch = this.handleCourseSearch.bind( this );
		this.handleGroupSearch = this.handleGroupSearch.bind( this );
		this.handleDefaultQuizFilterChange =
			this.handleDefaultQuizFilterChange.bind( this );
		this.handleQuizCourseChange = this.handleQuizCourseChange.bind( this );
		this.handleQuizGroupChange = this.handleQuizGroupChange.bind( this );
		this.handleQuizChange = this.handleQuizChange.bind( this );
		this.applyQuizFilters = this.applyQuizFilters.bind( this );
		this.applyExportFilters = this.applyExportFilters.bind( this );
		this.previewCustomReport = this.previewCustomReport.bind( this );
		this.previewReport = this.previewReport.bind( this );
		this.defaultFiltersLoaded = this.defaultFiltersLoaded.bind( this );
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
			this.defaultFiltersLoaded();
		} );
	}

	defaultFiltersLoaded() {
		let quiz_section_disabled = 'disabled';
		let report_type_selected = 'default-quiz-reports';
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
		let start = moment(
			new Date( wisdm_ld_reports_common_script_data.start_date )
		).unix();
		let end = moment(
			new Date( wisdm_ld_reports_common_script_data.end_date )
		).unix();
		if (
			false != wisdm_ld_reports_common_script_data.is_pro_version_active
		) {
			quiz_section_disabled = 'enabled';
		}

		if (
			undefined !=
				wisdm_ld_reports_common_script_data.qre_request_params &&
			wisdm_ld_reports_common_script_data
				.qre_request_params.report == 'custom'
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
		this.default_groups =
			wisdm_ld_reports_common_script_data.course_groups; // cspell:disable-line
		if (
			undefined !=
			wisdm_ld_reports_common_script_data.qre_filters
		) {
			const qre_filters =
				wisdm_ld_reports_common_script_data.qre_filters;
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
			start =
				undefined != qre_filters.start_date
					? parseInt( qre_filters.start_date )
					: start;
			end =
				undefined != qre_filters.end_date
					? parseInt( qre_filters.end_date )
					: end;
		}
		if ( 'administrator' == userType ) {
		} else if ( 'group_leader' == userType ) {
			categories_disabled = true;
			groups_disabled = false;
		}
		this.setState( {
			report_type_selected,
			groups_disabled,
			// cspell:disable-next-line
			quizes_disabled: quizzes_disabled,
			categories:
				wisdm_ld_reports_common_script_data.course_categories,
			courses,
			groups: this.default_groups,
			// cspell:disable-next-line
			quizes: this.default_quizes,
			selected_courses: selected_course,
			selected_groups: selected_group,
			// cspell:disable-next-line
			selected_quizes: selected_quiz,
			export_start_date: start,
			export_end_date: end,
		} );
	}

	componentDidMount() {
		document.addEventListener( 'duration_updated', this.durationUpdated );
		document.addEventListener( 'date_updated', this.dateUpdated );
		// document.addEventListener('wrld-default-filters-loaded', this.defaultFiltersLoaded);

		if ( this.state.report_type_selected == 'default-quiz-reports' ) {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-time-spent-on-a-course',
				false
			);
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-completion-rate',
				false
			);
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-quiz-completion-rate-per-course',
				false
			);
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-quiz-completion-time-per-course',
				false
			);
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-learner-pass-fail-rate-per-course',
				false
			);
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-average-quiz-attempts',
				false
			);
		}
	}

	componentDidUpdate() {
		jQuery(
			'.export-attempt-results .dashicons-info-outline, .export-attempt-learner-answers .dashicons-info-outline'
		)
			.on( 'mouseenter', function () {
				const $div = jQuery( '<div/>' )
					.addClass( 'wdm-tooltip' )
					.css( {
						position: 'absolute',
						zIndex: 999,
						display: 'none',
					} )
					.appendTo( jQuery( this ) );
				$div.text( jQuery( this ).attr( 'data-title' ) );
				const $font = jQuery( this )
					.parents( '.report-label' )
					.css( 'font-family' );
				$div.css( 'font-family', $font );
				$div.show();
			} )
			.on( 'mouseleave', function () {
				jQuery( this ).find( '.wdm-tooltip' ).remove();
			} );
	}

	durationUpdated( event ) {
		this.setState( {
			start_date: event.detail.startDate,
			end_date: event.detail.endDate,
		} );
	}

	dateUpdated( event ) {
		this.setState( {
			export_start_date: event.detail.startDate,
			export_end_date: event.detail.endDate,
		} );
		// jQuery('.apply-bulk-filters').removeAttr('disabled');
		this.setState( { disabled_button: false } );
		/*const defaultEntryCounts = new CustomEvent("wrld-fetch-export-data-count", {
            "detail": {
                        'start_date':this.state.export_start_date,
                        'end_date':this.state.export_end_date,
                        'selected_courses': this.state.selected_courses.value,
                        'selected_groups': this.state.selected_groups.value,
                        'selected_quizes': this.state.selected_quizes.value, // cspell:disable-line
                    }});
        document.dispatchEvent(defaultEntryCounts);*/
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
		// jQuery('.apply-bulk-filters').removeAttr('disabled');
		this.setState( { disabled_button: false } );
		/*const defaultEntryCounts = new CustomEvent("wrld-fetch-export-data-count", {
            "detail": {
                        'start_date':this.state.export_start_date,
                        'end_date':this.state.export_end_date,
                        'selected_courses': this.state.selected_courses.value,
                        'selected_groups': this.state.selected_groups.value,
                        'selected_quizes': this.state.selected_quizes.value, // cspell:disable-line
                    }});
        document.dispatchEvent(defaultEntryCounts);*/
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
		this.setState( { disabled_button: false } );
		// jQuery('.apply-bulk-filters').removeAttr('disabled');
		/*const defaultEntryCounts = new CustomEvent("wrld-fetch-export-data-count", {
            "detail": {
                        'start_date':this.state.export_start_date,
                        'end_date':this.state.export_end_date,
                        'selected_courses': this.state.selected_courses.value,
                        'selected_groups': this.state.selected_groups.value,
                        'selected_quizes': this.state.selected_quizes.value, // cspell:disable-line
                    }});
        document.dispatchEvent(defaultEntryCounts);*/
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
				if ( ! ( 'courses_enrolled' in group ) ) {
					return;
				}
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
		this.setState( { disabled_button: false } );

		// jQuery('.apply-bulk-filters').removeAttr('disabled');
		/*const defaultEntryCounts = new CustomEvent("wrld-fetch-export-data-count", {
            "detail": {
                        'start_date':this.state.export_start_date,
                        'end_date':this.state.export_end_date,
                        'selected_courses': this.state.selected_courses.value,
                        'selected_groups': this.state.selected_groups.value,
                        'selected_quizes': this.state.selected_quizes.value, // cspell:disable-line
                    }});
        document.dispatchEvent(defaultEntryCounts);*/
	}

	applyExportFilters() {
		this.setState( {
			disabled_button: true,
			show_bulk_attempt_download: 'wrld-hidden',
			show_bulk_learner_download: 'wrld-hidden',
		} );
		jQuery( '.report-export-buttons button' ).removeAttr( 'disabled' );
		jQuery( '.bulk-export-download' ).addClass( 'wrld-hidden' ).html( '' );
		// event.currentTarget.disabled = true;
		const defaultEntryCounts = new CustomEvent(
			'wrld-fetch-export-data-count',
			{
				detail: {
					start_date: this.state.export_start_date,
					end_date: this.state.export_end_date,
					selected_courses: this.state.selected_courses.value,
					selected_groups: this.state.selected_groups.value,
					selected_quizes: this.state.selected_quizes.value, // cspell:disable-line
				},
			}
		);
		document.dispatchEvent( defaultEntryCounts );
	}

	handleCourseSearch( inputString, callback ) {
		// perform a request
		let callback_path = '/ldlms/v1/sfwd-courses/?search=';
		const requestResults = [];
		if ( 2 < inputString.length ) {
			callback_path = callback_path + inputString;
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

	openBulkExportModal() {
		document.body.classList.add( 'wrld-open' );
		this.setState( {
			show_bulk_export_modal: true,
		} );

		const defaultEntryCounts = new CustomEvent(
			'wrld-fetch-export-data-count',
			{
				detail: {
					start_date: this.state.export_start_date,
					end_date: this.state.export_end_date,
					selected_courses: this.state.selected_courses.value,
					selected_groups: this.state.selected_groups.value,
					selected_quizes: this.state.selected_quizes.value, // cspell:disable-line
				},
			}
		);
		document.dispatchEvent( defaultEntryCounts );
		// setTimeout(function(){
		//     console.log(jQuery('div[data-modal="true"] > div'));
		//     jQuery('div[data-modal="true"] > div').css({
		//         'padding-top': '0px !important',
		//         'padding-right': '0px !important',
		//         'padding-left': '0px !important'
		//     });
		// }, 8200);
	}

	closeBulkExportModal() {
		document.body.classList.remove( 'wrld-open' );
		this.setState( {
			show_bulk_export_modal: false,
			show_bulk_attempt_progress: 'wrld-hidden',
			show_bulk_learner_progress: 'wrld-hidden',
		} );
	}

	openBulkProgressModal( type ) {
		// document.body.classList.add('wrld-open');
		if ( 'attempt' === type ) {
			this.setState( {
				show_bulk_attempt_progress: '',
			} );
		} else {
			this.setState( {
				show_bulk_learner_progress: '',
			} );
		}
	}

	closeBulkProgressModal() {
		document.body.classList.remove( 'wrld-open' );
		this.setState( {
			show_bulk_progress_modal: false,
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

		fields_selected.course_filter = this.state.selected_courses.value;
		fields_selected.group_filter = this.state.selected_groups.value;
		fields_selected.quiz_filter = this.state.selected_quizes.value; // cspell:disable-line
		if ( jQuery( '.quiz-filter-modal' ).length === 0 ) {
			fields_selected.select_event = 1;
			const field_values = this.state.selectedFields;
			field_values.course_filter = this.state.selected_courses.value;
			field_values.group_filter = this.state.selected_groups.value;
			field_values.quiz_filter = this.state.selected_quizes.value; // cspell:disable-line
			this.setState( { selectedFields: field_values } );
		} else {
			this.setState( { selectedFields: fields_selected } );
		}

		// fields_selected['category_filter'] = this.state.selected_categories.value;

		const customQuizReport = new CustomEvent(
			'wisdm-ld-reports-custom-quiz-report-filters-applied',
			{
				detail: {
					start_date: this.state.export_start_date,
					end_date: this.state.export_end_date,
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

	exportAttemptCSV( event ) {
		jQuery( event.target ).attr( 'disabled', 'disabled' );
		this.exportAttemptResults( 'csv' );
	}

	exportAttemptXLSX( event ) {
		jQuery( event.target ).attr( 'disabled', 'disabled' );
		this.exportAttemptResults( 'xlsx' );
	}

	exportLearnerCSV( event ) {
		jQuery( event.target ).attr( 'disabled', 'disabled' );
		this.exportLearnerResults( 'csv' );
	}

	exportLearnerXLSX( event ) {
		jQuery( event.target ).attr( 'disabled', 'disabled' );
		this.exportLearnerResults( 'xlsx' );
	}

	exportAttemptResults( type ) {
		const attemptQuizReport = new CustomEvent(
			'wrld-bulk-export-attempt-results',
			{
				detail: {
					start_date: this.state.export_start_date,
					end_date: this.state.export_end_date,
					selected_courses: this.state.selected_courses.value,
					selected_groups: this.state.selected_groups.value,
					selected_quizes: this.state.selected_quizes.value, // cspell:disable-line
					type,
				},
			}
		);
		document.dispatchEvent( attemptQuizReport );
		this.openBulkProgressModal( 'attempt' );
	}

	exportLearnerResults( type ) {
		const learnerQuizReport = new CustomEvent(
			'wrld-bulk-export-learner-results',
			{
				detail: {
					start_date: this.state.export_start_date,
					end_date: this.state.export_end_date,
					selected_courses: this.state.selected_courses.value,
					selected_groups: this.state.selected_groups.value,
					selected_quizes: this.state.selected_quizes.value, // cspell:disable-line
					type,
				},
			}
		);
		document.dispatchEvent( learnerQuizReport );
		this.openBulkProgressModal( 'learner' );
	}

	render() {
		let body = '';
		let customFilterDropDowns = (
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
							placeholder={ __( 'All', 'learndash-reports-pro' ) }
							onChange={ this.handleQuizCourseChange }
							isClearable="true"
							value={ {
								value: this.state.selectedValue.course_filter,
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
							placeholder={ __( 'All', 'learndash-reports-pro' ) }
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
							placeholder={ __( 'All', 'learndash-reports-pro' ) }
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
													" No learners/%1$s/%2$s found for the search string '",
													'learndash-reports-pro'
												) +
													element.selectProps
														.inputValue +
													"'",
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
				{ this.state.show_bulk_export_modal && (
					<Modal
						onRequestClose={ this.closeBulkExportModal }
						className={
							'learndash-propanel-modal bulk_export_modal'
						}
					>
						<div className="header bulk-export-header wrld-hidden"></div>
						<div className="filter-section">
							{ customFilterDropDowns }
							<div className="date-container">
								<div className="calendar-label">
									<span>
										{ __(
											'DATE OF ATTEMPT',
											'learndash-reports-pro'
										) }
									</span>
								</div>
								<span className="export-date-range export-date-range--modal">
									<ComponentDatepicker
										start={ this.state.export_start_date }
										end={ this.state.export_end_date }
										parentEl={ '.export-date-range--modal' }
									></ComponentDatepicker>
									<div className="apply_filters">
										<button
											className="apply-bulk-filters"
											onClick={ this.applyExportFilters }
											disabled={
												this.state.disabled_button
											}
										>
											{ __(
												'APPLY FILTERS',
												'learndash-reports-pro'
											) }
										</button>
									</div>
								</span>
							</div>
						</div>
						<div className="bulk-export-heading">
							<h3>{ __( 'Export', 'learndash-reports-pro' ) }</h3>
							<div>
								Total quiz attempts - <span>???</span>
								<div> selected</div>
							</div>
						</div>
						<div className="export-attempt-results">
							<div className="report-label">
								<label>
									{ sprintf(
										// Translators: %s: quiz label.
										__(
											'Export all %s attempts result',
											'learndash-reports-pro'
										),
										wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'Quiz',
											'lower'
										)
									) }
								</label>
								<span
									className="dashicons dashicons-info-outline"
									data-title={ sprintf(
										// Translators: %1$s: quiz label.
										__(
											'This report exports the summarized information of all %s attempts',
											'learndash-reports-pro'
										),
										wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'quiz',
											'lower'
										)
									) }
								></span>
							</div>
							<div className="report-export-buttons">
								<button
									className="export-attempt-csv"
									onClick={ this.exportAttemptCSV.bind(
										this
									) }
								>
									CSV
								</button>
								<button
									className="export-attempt-xlsx"
									onClick={ this.exportAttemptXLSX.bind(
										this
									) }
								>
									XLSX
								</button>
							</div>
							<div className="export-link-wrapper">
								<div
									className={ `bulk-export-download ${ this.state.show_bulk_attempt_download }` }
								></div>
								<div
									className={ `bulk-export-progress ${ this.state.show_bulk_attempt_progress }` }
								>
									<label>
										{ __(
											'Downloading progress:',
											'learndash-reports-pro'
										) }
									</label>
									<progress value="0" max="100"></progress>
									<span></span>
								</div>
							</div>
						</div>
						<div className="export-attempt-learner-answers">
							<div className="report-label">
								<label>
									{ sprintf(
										// Translators: %s: quiz label.
										__(
											'Export %s attempts learner answers',
											'learndash-reports-pro'
										),
										wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'Quiz',
											'lower'
										)
									) }
								</label>
								<span
									className="dashicons dashicons-info-outline"
									data-title={ sprintf(
										// Translators: %1$s: quiz label.
										__(
											'This report exports the actual answers provided by learners for all the %s attempts',
											'learndash-reports-pro'
										),
										wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'quiz',
											'lower'
										)
									) }
								></span>
							</div>
							<div className="report-export-buttons">
								<button
									className="export-learner-csv"
									onClick={ this.exportLearnerCSV.bind(
										this
									) }
								>
									CSV
								</button>
								<button
									className="export-learner-xlsx"
									onClick={ this.exportLearnerXLSX.bind(
										this
									) }
								>
									XLSX
								</button>
							</div>
							<div className="export-link-wrapper">
								<div
									className={ `bulk-export-download ${ this.state.show_bulk_learner_download }` }
								></div>
								<div
									className={ `bulk-export-progress ${ this.state.show_bulk_learner_progress }` }
								>
									<label>
										{ __(
											'Downloading progress:',
											'learndash-reports-pro'
										) }
									</label>
									<progress value="0" max="100"></progress>
									<span></span>
								</div>
							</div>
						</div>
						<div className="export-note">
							<span>
								{ sprintf(
									// Translators: %s: quiz label.
									__(
										'Note: We recommend to download at most 10000 number of %s attempts to avoid server timeout.',
										'learndash-reports-pro'
									),
									wisdm_reports_get_ld_custom_lebel_if_avaiable(
										'Quiz',
										'lower'
									)
								) }
							</span>
						</div>
					</Modal>
				) }
				<button
					className="button-bulk-export"
					onClick={ this.openBulkExportModal }
				>
					{ __( 'Bulk Export', 'learndash-reports-pro' ) }
				</button>
			</div>
		);
		//Custom Filers
		if ( 'custom-quiz-reports' === this.state.report_type_selected ) {
			customFilterDropDowns = (
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
								// Translators: %s: Quiz label.
								__(
									'Customize your %s Results and analyze them in a detailed view. Please select the appropriate filters and the fields (by clicking on the Customize Report Button) and click on Apply Filters to display the reports below.',
									'learndash-reports-pro'
								),
								wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Quiz'
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
					<div className="filter-wrap">
						{ customFilterDropDowns }
						<div className="date-container">
							<div className="calendar-label">
								<span>
									{ __(
										'DATE OF ATTEMPT',
										'learndash-reports-pro'
									) }
								</span>
							</div>
							<ComponentDatepicker
								start={ this.state.export_start_date }
								end={ this.state.export_end_date }
							></ComponentDatepicker>
						</div>
					</div>
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
														'All Attempts Report Fields',
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
															'Username',
															'learndash-reports-pro'
														) }
													/>
													<Checkbox
														isChecked="yes"
														always_checked="yes"
														name="quiz_title"
														label={ wisdm_reports_get_ld_custom_lebel_if_avaiable(
															'Quiz'
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
														label={ sprintf(
															// Translators: Quiz label.
															__(
																'%s Status',
																'learndash-reports-pro'
															),
															wisdm_reports_get_ld_custom_lebel_if_avaiable(
																'Quiz'
															)
														) }
													/>
													<Checkbox
														isChecked="yes"
														always_checked="yes"
														name="quiz_category"
														label={ sprintf(
															// Translators: Quiz label.
															__(
																'%s Category',
																'learndash-reports-pro'
															),
															wisdm_reports_get_ld_custom_lebel_if_avaiable(
																'Quiz'
															)
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
					{ this.state.show_bulk_export_modal && (
						<Modal
							onRequestClose={ this.closeBulkExportModal }
							className={
								'learndash-propanel-modal bulk_export_modal'
							}
						>
							<div className="header bulk-export-header"></div>
							<div className="filter-section">
								{ customFilterDropDowns }
								<div className="date-container">
									<div className="calendar-label">
										<span>
											{ __(
												'DATE OF ATTEMPT',
												'learndash-reports-pro'
											) }
										</span>
									</div>
									<span className="export-date-range">
										<ComponentDatepicker
											start={
												this.state.export_start_date
											}
											end={ this.state.export_end_date }
										></ComponentDatepicker>
										<div className="apply_filters">
											<button
												className="apply-bulk-filters"
												disabled={
													this.state.disabled_button
												}
												onClick={
													this.applyExportFilters
												}
											>
												{ __(
													'APPLY FILTERS',
													'learndash-reports-pro'
												) }
											</button>
										</div>
									</span>
								</div>
							</div>
							<div className="bulk-export-heading">
								<h3>
									{ __( 'Export', 'learndash-reports-pro' ) }
								</h3>
								<div>
									Total quiz attempts - <span>???</span>
									<div> selected</div>
								</div>
							</div>
							<div className="export-attempt-results">
								<div className="report-label">
									<label>
										{ sprintf(
											// Translators: %s: quiz label.
											__(
												'Export all %s attempts result',
												'learndash-reports-pro'
											),
											wisdm_reports_get_ld_custom_lebel_if_avaiable(
												'Quiz',
												'lower'
											)
										) }
									</label>
									<span
										className="dashicons dashicons-info-outline"
										data-title={ sprintf(
											// Translators: %1$s: quiz label.
											__(
												'This report exports the summarized information of all %s attempts',
												'learndash-reports-pro'
											),
											wisdm_reports_get_ld_custom_lebel_if_avaiable(
												'quiz',
												'lower'
											)
										) }
									></span>
								</div>
								<div className="report-export-buttons">
									<button
										className="export-attempt-csv"
										onClick={ this.exportAttemptCSV.bind(
											this
										) }
									>
										CSV
									</button>
									<button
										className="export-attempt-xlsx"
										onClick={ this.exportAttemptXLSX.bind(
											this
										) }
									>
										XLSX
									</button>
								</div>
								<div className="export-link-wrapper">
									<div
										className={ `bulk-export-download ${ this.state.show_bulk_attempt_download }` }
									></div>
									<div
										className={ `bulk-export-progress ${ this.state.show_bulk_attempt_progress }` }
									>
										<label>
											{ __(
												'Downloading progress:',
												'learndash-reports-pro'
											) }
										</label>
										<progress
											value="0"
											max="100"
										></progress>
										<span></span>
									</div>
								</div>
							</div>
							<div className="export-attempt-learner-answers">
								<div className="report-label">
									<label>
										{ sprintf(
											// Translators: quiz label.
											__(
												'Export %s attempts learner answers',
												'learndash-reports-pro'
											),
											wisdm_reports_get_ld_custom_lebel_if_avaiable(
												'Quiz',
												'lower'
											)
										) }
									</label>
									<span
										className="dashicons dashicons-info-outline"
										data-title={ sprintf(
											// Translators: %1$s: quiz label.
											__(
												'This report exports the actual answers provided by learners for all the %s attempts',
												'learndash-reports-pro'
											),
											wisdm_reports_get_ld_custom_lebel_if_avaiable(
												'quiz',
												'lower'
											)
										) }
									></span>
								</div>
								<div className="report-export-buttons">
									<button
										className="export-learner-csv"
										onClick={ this.exportLearnerCSV.bind(
											this
										) }
									>
										CSV
									</button>
									<button
										className="export-learner-xlsx"
										onClick={ this.exportLearnerXLSX.bind(
											this
										) }
									>
										XLSX
									</button>
								</div>
								<div className="export-link-wrapper">
									<div
										className={ `bulk-export-download ${ this.state.show_bulk_learner_download }` }
									></div>
									<div
										className={ `bulk-export-progress ${ this.state.show_bulk_learner_progress }` }
									>
										<label>
											{ __(
												'Downloading progress:',
												'learndash-reports-pro'
											) }
										</label>
										<progress
											value="0"
											max="100"
										></progress>
										<span></span>
									</div>
								</div>
							</div>
							<div className="export-note">
								<span>
									{ sprintf(
										// Translators: quiz label.
										__(
											'Note: We recommend to download at most 10000 number of %s attempts to avoid server timeout.',
											'learndash-reports-pro'
										),
										wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'Quiz',
											'lower'
										)
									) }
								</span>
							</div>
						</Modal>
					) }
					<button
						className="button-bulk-export"
						onClick={ this.openBulkExportModal }
					>
						{ __( 'Bulk Export', 'learndash-reports-pro' ) }
					</button>
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
					<div
						className="quiz-report-types"
						onChange={ this.onQuizReportViewChange }
					>
						<input
							id="dfr"
							type="radio"
							value="default-quiz-reports"
							name="quiz-report-types"
							checked={
								'default-quiz-reports' ===
								this.state.report_type_selected
							}
							readOnly={ true }
						/>
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
							readOnly={ true }
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
		this.state = {
			isLoaded: false,
		};

		this.durationUpdated = this.durationUpdated.bind( this );
		this.applyFilters = this.applyFilters.bind( this );
		this.handleTabSelection = this.handleTabSelection.bind( this );
		this.changeCourseReportType = this.changeCourseReportType.bind( this );
		this.refreshBlock = this.refreshBlock.bind( this );

		this.refreshBlock();
	}

	durationUpdated( event ) {
		this.setState( {
			start_date: event.detail.startDate,
			end_date: event.detail.endDate,
		} );
		// this.setState({selected_categories:{value:null,label:__('All', 'learndash-reports-pro')},selected_groups:{value:null,label:__('All', 'learndash-reports-pro')},selected_courses:{value:null,label:__('All', 'learndash-reports-pro')},selected_lessons:{value:null,label:__('All', 'learndash-reports-pro')},selected_topics:{value:null,label:__('All', 'learndash-reports-pro')},selected_learners:null,});
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
		document.addEventListener( 'duration_updated', this.durationUpdated );
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

	handleAdminGroupChange = ( selectedGroup ) => {
		const categorySelectedByAdmin = this.state.selected_categories.value;
		if ( null == selectedGroup ) {
			this.setState( {
				selected_groups: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
				selected_courses: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
			this.updateSelectorsFor(
				'group',
				null,
				'/ldlms/v1/' + ld_api_settings[ 'sfwd-courses' ] + '?test=1'
			);
			this.setState( {
				courses: this.state.default_courses,
				categories_disabled: false,
			} );
		} else {
			this.setState( {
				selected_groups: selectedGroup,
				categories_disabled: true,
				selected_courses: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
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
					wisdm_ld_reports_common_script_data
						.exclude_courses.length > 0 &&
					false !=
						wisdm_ld_reports_common_script_data.is_pro_version_active
				) {
					for (
						let i = 0;
						i <
						wisdm_ld_reports_common_script_data
							.exclude_courses.length;
						i++
					) {
						url +=
							'&exclude[]=' +
							wisdm_ld_reports_common_script_data
								.exclude_courses[ i ];
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

	handleGroupChange = ( selectedGroup ) => {
		if ( null == selectedGroup || null == selectedGroup.value ) {
			this.setState( {
				selected_groups: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
				selected_courses: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
			this.updateSelectorsFor(
				'group',
				null,
				'/ldlms/v1/' + ld_api_settings[ 'sfwd-courses' ] + '?test=1'
			);
			this.setState( {
				courses: this.state.default_courses,
				categories_disabled: false,
			} );
		} else {
			this.setState( {
				selected_groups: selectedGroup,
				categories_disabled: true,
				selected_courses: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} );
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
				'/ldlms/v1/' + ld_api_settings[ 'sfwd-lessons' ] + '/'
			);
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
		// if (3>inputString.length) {
		//     return callback(requestResults);
		// }
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
					callback_path +
					'?ld_course_category[]=' +
					selection +
					'&per_page=-1';
				let url = '';
				if (
					wisdm_ld_reports_common_script_data
						.exclude_courses.length > 0 &&
					false !=
						wisdm_ld_reports_common_script_data.is_pro_version_active
				) {
					for (
						let i = 0;
						i <
						wisdm_ld_reports_common_script_data
							.exclude_courses.length;
						i++
					) {
						url +=
							'&exclude[]=' +
							wisdm_ld_reports_common_script_data
								.exclude_courses[ i ];
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
				callback_path = callback_path + '&per_page=-1';
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
								lessons: [],
								topics: [],
								courses_disabled: false,
								loading_courses: false,
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
							} );
						} else {
							this.setState( {
								courses: [],
								lessons: [],
								topics: [],
								course: {
									value: null,
									label: __( 'All', 'learndash-reports-pro' ),
								},
								loading_courses: false,
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
							} );
						}
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
						} else {
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
								loading_courses: false,
							} );
						}
					} );
				}
				break;
			case 'course':
				callback_path =
					callback_path + '?course=' + selection + '&per_page=-1';
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
					selection +
					'&per_page=-1';
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

		const applyFilters = new CustomEvent(
			'wisdm-ld-reports-filters-applied',
			{
				detail: {
					start_date: this.state.start_date,
					end_date: this.state.end_date,
					selected_categories: this.state.selected_categories.value,
					selected_categories_obj: this.state.selected_categories,
					selected_groups: this.state.selected_groups.value,
					selected_groups_obj: this.state.selected_groups,
					selected_courses: this.state.selected_courses.value,
					selected_courses_obj: this.state.selected_courses,
					selected_lessons: this.state.selected_lessons.value,
					selected_lessons_obj: this.state.selected_lessons,
					selected_topics: this.state.selected_topics.value,
					selected_topics_obj: this.state.selected_topics,
					selected_learners:
						null != this.state.selected_learners
							? this.state.selected_learners.value
							: null,
					selected_learners_obj:
						null != this.state.selected_learners
							? this.state.selected_learners
							: null,
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
				detail: {
					active_reports_tab: 'default-ld-reports',
					report_type: this.state.report_type_selected,
				},
			}
		);
		if ( 1 == tab_key ) {
			tabSwitchEvent = new CustomEvent(
				'wisdm-ld-reports-report-type-selected',
				{
					detail: { active_reports_tab: 'quiz-reports' },
				}
			);
			document.dispatchEvent(
				new CustomEvent( 'wisdm-ld-custom-report-type-select', {
					detail: { report_selector: '' },
				} )
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
				courses: this.state.default_courses,
				categories_disabled: false,
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

		global.reportTypeForTooltip = report_type;
		document.dispatchEvent(
			new CustomEvent( 'wisdm-ldrp-course-report-type-changed', {
				detail: { report_type },
			} )
		);
	}

	/**
	 * Grab the dropdown data for the Block and optionally refresh the other blocks on the page once the data has been pulled.
	 *
	 * @since 3.0.0
	 *
	 * @param  {bool} triggerEvent Whether the other Blocks on the page should update on completion. Defaults to true.
	 * @param  {bool} disableCache Whether the Transient Cache should be disabled or not. Defaults to false.
	 *
	 * @return {void}
	 */
	refreshBlock( triggerEvent, disableCache ) {
		if ( typeof triggerEvent === 'undefined' ) {
			triggerEvent = true;
		}

		if ( typeof disableCache === 'undefined' ) {
			disableCache = false;
		}

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

		const searchParams = new URLSearchParams();

		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			searchParams.set(
				'wpml_lang',
				wisdm_ld_reports_common_script_data.wpml_lang
			);
		}

		if ( disableCache ) {
			searchParams.set( 'disable_cache', true );
		}

		if ( searchParams.size > 0 ) {
			localized_data_url =
				localized_data_url + '?' + searchParams.toString();
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
				'quiz-reports' ==
				wisdm_ld_reports_common_script_data.report_type
					? 1
					: 0;
			this.state.active_tab = tab_selected;
			this.getDefaultOptions();
			let url =
				'/ldlms/v1/' +
				ld_api_settings[ 'sfwd-courses' ] +
				'?per_page=-1';
			if (
				wisdm_ld_reports_common_script_data
					.exclude_courses.length > 0 &&
				false !=
					wisdm_ld_reports_common_script_data.is_pro_version_active
			) {
				for (
					let i = 0;
					i <
					wisdm_ld_reports_common_script_data
						.exclude_courses.length;
					i++
				) {
					url +=
						'&exclude[]=' +
						wisdm_ld_reports_common_script_data
							.exclude_courses[ i ];
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
			const courses =
				window.wisdm_ld_reports_common_script_data
					.courses;

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

			if ( ! triggerEvent ) {
				return;
			}

			const defaultOptionsLoaded = new CustomEvent(
				'wrld-default-filters-loaded'
			);
			document.dispatchEvent( defaultOptionsLoaded );
		} );
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
									label: 'Michelle Schowalter', // cspell:disable-line
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
		let wrld_placeholder = __( 'Search', 'learndash-reports-pro' );
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
			wrld_placeholder = __( 'PAUL JOHN', 'learndash-reports-pro' );
			if ( 'group_leader' == userType || 'instructor' == userType ) {
				gl_class = 'wrld-gl';
			}
		}

		let bodyContent = null;
		if ( ! this.state.isLoaded ) {
			// yet loading
			bodyContent = <WisdmLoader key="wisdm-loader-report-filters" />;
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
			// cspell:disable-next-line
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
					key="report-filters-content"
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
					<div className="refresh-data">
						<div
							className="chart_update_time"
							onClick={ () => {
								this.setState(
									{ isLoaded: false },
									this.refreshBlock( false, true )
								);
							} }
						>
							<span>
								{ __(
									'Refresh available options',
									'learndash-reports-pro'
								) }
							</span>
							<div className="chart-refresh-icon">
								<span
									className="dashicons dashicons-image-rotate"
									data-title={ __(
										'Click this to refresh the available options',
										'learndash-reports-pro'
									) }
								></span>
							</div>
						</div>
					</div>
					<TabPanel>
						<div className="wisdm-learndash-reports-course-report-tools-wrap">
							<div
								className="course-report-by"
								onChange={ this.changeCourseReportType }
							>
								<input
									id="csr"
									type="radio"
									value="default-course-reports"
									name="course-report-types"
									checked={
										'default-course-reports' ===
										this.state.report_type_selected
									}
									readOnly={ true }
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
									readOnly={ true }
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
																" No learners found for the search string'",
																'learndash-reports-pro'
														  ) +
																element
																	.selectProps
																	.inputValue +
																"'"
														: __(
																' Type 3 or more letters to search',
																'learndash-reports-pro'
														  );
												},
											} }
											placeholder={ wrld_placeholder }
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
				key="report-filters-content"
			>
				{ bodyContent }
			</div>
		);
	}
}

export default ReportFilters;
export { QuizFilters };

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
		const course_groups =
			wisdm_ld_reports_common_script_data.course_groups;
		const group_course_list = [];
		if ( course_groups.length > 0 ) {
			course_groups.forEach( function ( course_group ) {
				if ( ! ( 'courses_enrolled' in course_group ) ) {
					return;
				}
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
		filtered_courses =
			wisdm_ld_reports_common_script_data.courses;
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
		if ( ! ( 'group_users' in group ) ) {
			return;
		}
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
		const container = elem[ 0 ];
		// Check if root already exists to prevent re-creating it
		if ( ! container._reactRoot ) {
			const root = createRoot( container );
			container._reactRoot = root;
			root.render( React.createElement( ReportFilters ) );
		} else {
			// If root exists, just update it
			container._reactRoot.render( React.createElement( ReportFilters ) );
		}
	}
} );
