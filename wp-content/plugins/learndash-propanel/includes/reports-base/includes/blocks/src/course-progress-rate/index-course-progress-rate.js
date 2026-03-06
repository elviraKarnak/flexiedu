// cspell:ignore datapoint

import './index.scss';
import 'react-tabs/style/react-tabs.css';
import ApexCharts from 'apexcharts';
import ChartSummarySection from '../commons/chart-summary/index.js';
import WisdmFilters from '../commons/filters/index.js';
import WisdmLoader from '../commons/loader/index.js';
import DummyReports from '../commons/dummy-reports/index.js';
import React, { Component } from 'react';
import Chart from '../commons/chart-wrapper/index.js';
import { __, sprintf } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
import { Modal } from '@wordpress/components';
import DurationFilter from './component-duration-filter.js';
import LocalFilters from './component-local-filters.js';
import ProgressDetailsTable from './component-progress-details.js';

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
		filtered_courses = wisdm_ld_reports_common_script_data.courses;
	} else {
		filtered_courses = courseList;
	}

	let iSAllIncluded = true;
	for ( const i in filtered_courses ) {
		if ( i.value == null ) {
			iSAllIncluded = false;
		}
	}
	if ( iSAllIncluded ) {
		filtered_courses.unshift( {
			value: null,
			label: __( 'All', 'learndash-reports-pro' ),
		} );
	}
	return filtered_courses;
}

class CourseProgressRate extends Component {
	constructor( props ) {
		super( props );
		let error = null;
		if ( null == this.getUserType() ) {
			error = {
				message: __(
					'Sorry you are not allowed to access this block, please check if you have proper access permissions',
					'learndash-reports-pro'
				),
			};
		}
		const tab_selected =
			'course-reports' == wisdm_ld_reports_common_script_data.report_type
				? 1
				: 0;
		this.state = {
			isLoaded: false,
			error,
			series: [],
			options: [],
			reportTypeInUse: wisdm_ld_reports_common_script_data.report_type,

			chart_title:
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' ) + // cspell:disable-line
				' ' +
				__( 'Progress Rate', 'learndash-reports-pro' ),
			lock_icon: '',
			request_data: null,
			active_tab: tab_selected,
			group: { value: null, label: __( 'All', 'learndash-reports-pro' ) },
			groups: [],
			course: {
				value: null,
				label: sprintf(
					// Translators: %s: courses label.
					__( 'All %s', 'learndash-reports-pro' ),
					wisdm_reports_get_ld_custom_lebel_if_avaiable(
						'courses',
						'lower'
					)
				),
			},
			courses: [],
			category: {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			},
			categories: [],
			graph_type: 'donut',
			graph_summary: [],
			duration: {
				value: 'all',
				label: __( 'All time', 'learndash-reports-pro' ),
			},
			help_text: sprintf(
				// Translators: %s: courses label.
				__(
					'This report displays the average progress rate of %s.',
					'learndash-reports-pro'
				),
				wisdm_reports_get_ld_custom_lebel_if_avaiable(
					'Courses',
					'lower'
				)
			),
			course_report_type: null,
			show_supporting_text: false,
			show_progress_details_modal: false,
			page: 1,
		};

		this.applyFilters = this.applyFilters.bind( this );
		this.handleReportTypeChange = this.handleReportTypeChange.bind( this );
		// this.showDummyImages        = this.showDummyImages.bind(this);
		this.updateLocalDuration = this.updateLocalDuration.bind( this );
		this.updateLocalGroup = this.updateLocalGroup.bind( this );
		this.updateLocalCategory = this.updateLocalCategory.bind( this );
		this.updateLocalLearner = this.updateLocalLearner.bind( this );
		this.updateLocalCourse = this.updateLocalCourse.bind( this );
		this.updateLocalTab = this.updateLocalTab.bind( this );
		this.openProgressDetailsModal =
			this.openProgressDetailsModal.bind( this );
		this.closeProgressDetailsModal =
			this.closeProgressDetailsModal.bind( this );
		this.addMoreData = this.addMoreData.bind( this );
		this.startCSVDownload = this.startCSVDownload.bind( this );
		this.defaultFiltersLoaded = this.defaultFiltersLoaded.bind( this );
	}

	openProgressDetailsModal() {
		document.body.classList.add( 'wrld-open' );
		this.setState( {
			show_progress_details_modal: true,
		} );

		// setTimeout(function(){
		//     console.log(jQuery('div[data-modal="true"] > div'));
		//     jQuery('div[data-modal="true"] > div').css({
		//         'padding-top': '0px !important',
		//         'padding-right': '0px !important',
		//         'padding-left': '0px !important'
		//     });
		// }, 8200);
	}

	closeProgressDetailsModal() {
		document.body.classList.remove( 'wrld-open' );
		this.setState( {
			show_progress_details_modal: false,
		} );
		this.setState( {
			selected_data_point: '',
			table_data: [],
			more: 'no',
			page: 1,
		} );
	}

	/**
	 * Based on the current user roles array this function decides wether a user is a group
	 * leader or an Administrator and returns the same.
	 */
	getUserType() {
		let userRoles = wisdm_ld_reports_common_script_data.user_roles;
		if ( 'object' === typeof userRoles ) {
			userRoles = Object.keys( userRoles ).map(
				( key ) => userRoles[ key ]
			);
		}
		if ( undefined == userRoles || userRoles.length == 0 ) {
			return null;
		}
		if ( userRoles.includes( 'administrator' ) ) {
			return 'administrator';
		} else if ( userRoles.includes( 'group_leader' ) ) {
			return 'group_leader';
		}
		return null;
	}

	defaultFiltersLoaded() {
		let url =
			'/ldlms/v1/' +
			wisdm_ld_reports_common_script_data.ld_api_settings[
				'sfwd-courses'
			] +
			'?per_page=-1';
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
		this.setState( { isLoaded: false } );

		// wp.apiFetch({
		//     path: url  //Replace with the correct API
		// }).then(response => {
		// let courses     = this.getCourseListFromJson(response);
		const courses = wisdm_ld_reports_common_script_data.courses;
		const groups = wisdm_ld_reports_common_script_data.course_groups;
		const categories =
			wisdm_ld_reports_common_script_data.course_categories;
		// if ( groups.length > 0 ) {
		//   groups.unshift({value: null, label:__('All', 'learndash-reports-pro')});
		// }
		// if ( categories.length > 0 ) {
		//     categories.unshift({value: null, label:__('All categories', 'learndash-reports-pro')});
		// }

		if ( courses.length === 0 ) {
			this.setState( {
				isLoaded: true,
				error: { message: 'No courses found.' },
			} );
		}
		this.setState( {
			groups,
			courses,
			course:
				courses.length === 0
					? {
							value: null,
							label: __( 'All', 'learndash-reports-pro' ),
					  }
					: courses[ 0 ],
			categories,
		} );
		//Patch logic for react state update on browser refresh bug.
		const groupsLoadEvent = new CustomEvent(
			'progress-parent-groups-changed',
			{
				detail: { value: groups },
			}
		);
		const categoryLoadEvent = new CustomEvent(
			'progress-parent-category-changed',
			{
				detail: { value: categories },
			}
		);
		document.dispatchEvent( groupsLoadEvent );
		document.dispatchEvent( categoryLoadEvent );

		if ( courses.length === 0 ) {
			return;
		}

		this.updateChart(
			'/rp/v1/course-progress-rate?duration=' +
				this.state.duration.value +
				'&course=' +
				courses[ 0 ].value
		);
		// });
		const requestResults = [];
		if ( 'group_leader' == wisdmLdReportsGetUserType() ) {
			const groupUsers = wrldGetGroupAdminUsers();
			requestResults.push( {
				value: groupUsers[ 0 ].id,
				label: groupUsers[ 0 ].display_name,
			} );
			this.setState( { learner: requestResults[ 0 ] } );
		} else {
			let callback_path = '/rp/v1/learners?page=1&per_page=1';
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
						requestResults.push( {
							value: response.posts[ 0 ].ID,
							label: response.posts[ 0 ].name,
						} );
					}
					this.setState( { learner: requestResults[ 0 ] } );
				} )
				.catch( ( error ) => {} );
		}
	}

	componentDidMount() {
		document.addEventListener(
			'wisdm-ld-reports-filters-applied',
			this.applyFilters
		);
		document.addEventListener(
			'wisdm-ld-reports-report-type-selected',
			this.handleReportTypeChange
		);
		// document.addEventListener('wisdm-ldrp-course-report-type-changed', this.showDummyImages);
		document.addEventListener(
			'local_progress_duration_change',
			this.updateLocalDuration
		);
		document.addEventListener(
			'local_group_change_progress',
			this.updateLocalGroup
		);
		document.addEventListener(
			'local_learner_change_progress',
			this.updateLocalLearner
		);
		document.addEventListener(
			'local_category_change_progress',
			this.updateLocalCategory
		);
		document.addEventListener(
			'local_tab_change_progress',
			this.updateLocalTab
		);
		document.addEventListener(
			'local_course_change_progress',
			this.updateLocalCourse
		);
		document.addEventListener(
			'start_csv_download_cp',
			this.startCSVDownload
		);
		document.addEventListener(
			'wrld-default-filters-loaded',
			this.defaultFiltersLoaded
		);

		// Fallback: If this block is used alone without report-filters block,
		// fetch the filter data ourselves after a short delay.
		setTimeout( () => {
			if (
				this.state.categories.length === 0 &&
				this.state.groups.length === 0 &&
				! document.querySelector(
					'.wisdm-learndash-reports-report-filters'
				)
			) {
				// Fetch the filter data like report-filters block does.
				let localized_data_url = '/rp/v1/report-filters-data';
				if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
					localized_data_url +=
						'/?wpml_lang=' +
						wisdm_ld_reports_common_script_data.wpml_lang;
				}
				wp.apiFetch( {
					path: localized_data_url,
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
		}, 1000 );
	}

	addMoreData( event ) {
		const next = this.state.page + 1;
		this.setState( { page: next } );
		this.detailsModal( this.state.selected_data_point, true, next );
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

	updateSelectorsFor(
		element,
		selection,
		callback_path = '/wp/v2/categories/'
	) {
		switch ( element ) {
			case 'group':
				callback_path = callback_path + '&per_page=-1';
				if ( this.state.categories.length <= 0 ) {
					this.setState( {
						categories: [ { value: null, label: 'All' } ],
					} );
				}
				if ( null == selection ) {
					// wp.apiFetch({
					//     path: callback_path //Replace with the correct API
					//  }).then(response => {
					//     let courses = this.getCourseListFromJson(response);
					const courses = wisdm_ld_reports_common_script_data.courses;
					if ( false != courses && courses.length > 0 ) {
						this.setState( {
							courses,
							course: courses[ 0 ],
							category: this.state.categories[ 0 ],
						} );
					} else {
						this.setState( {
							courses: [],
							course: {
								value: null,
								label: sprintf(
									// Translators: %1$s: courses label, %2$s: group label.
									__(
										'No %1$s available for the %2$s',
										'learndash-reports-pro'
									),
									wisdm_reports_get_ld_custom_lebel_if_avaiable(
										'Quiz',
										'lower'
									),
									wisdm_reports_get_ld_custom_lebel_if_avaiable(
										'Group',
										'lower'
									)
								),
							},
							category: this.state.categories[ 0 ],
						} );
					}
					const request_url =
						'/rp/v1/course-progress-rate?duration=' +
						this.state.duration.value +
						'&group=' +
						selection +
						'&category=' +
						this.state.category.value +
						'&course=' +
						this.state.course.value;
					this.updateChart( request_url );
					//Patch logic for react state update on browser refresh bug.
					// const groupsLoadEvent = new CustomEvent("progress-parent-group-changed", {
					//   "detail": {"value": group }
					// });
					// document.dispatchEvent(groupsLoadEvent);
					// });
				} else {
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
								course: courses[ 0 ],
								category: this.state.categories[ 0 ],
							} );
						} else {
							this.setState( {
								courses: [],
								course: {
									value: null,
									label: sprintf(
										// Translators: %1$s: courses label, %2$s: group label.
										__(
											'No %1$s available for the %2$s',
											'learndash-reports-pro'
										),
										wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'Courses',
											'lower'
										),
										wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'Group',
											'lower'
										)
									),
								},
								category: this.state.categories[ 0 ],
							} );
						}
						//Patch logic for react state update on browser refresh bug.
						const groupsLoadEvent = new CustomEvent(
							'progress-parent-groups-changed',
							{
								detail: {
									value: {
										value: null,
										label: __(
											'All ',
											'learndash-reports-pro'
										),
									},
								},
							}
						);

						// document.dispatchEvent(groupsLoadEvent);
						const request_url =
							'/rp/v1/course-progress-rate?duration=' +
							this.state.duration.value +
							'&group=' +
							selection +
							'&category=' +
							this.state.category.value +
							'&course=' +
							this.state.course.value;
						this.updateChart( request_url );
					} );
				}
				break;
			case 'category':
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
				if ( null != selection ) {
					callback_path =
						callback_path +
						'?ld_course_category[]=' +
						selection +
						'&per_page=-1';
				} else {
					callback_path = callback_path + '?per_page=-1';
				}
				callback_path += url;
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
							const selected_course_id = this.state.course.value;
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
									course: courses[ 0 ],
									group: this.state.groups[ 0 ],
								} );
							}
							this.setState( {
								courses,
							} );
						}
						const request_url =
							'/rp/v1/course-progress-rate?duration=' +
							this.state.duration.value +
							'&group=' +
							this.state.group.value +
							'&category=' +
							selection +
							'&course=' +
							this.state.course.value;
						this.updateChart( request_url );
					} )
					.catch( ( error ) => {} );
				break;
			default:
				break;
		}
	}

	startCSVDownload( event ) {
		if ( this.state.active_tab == 1 ) {
			var requestUrl =
				'/rp/v1/course-progress-rate-csv/?duration=' +
				this.state.duration.value +
				'&learner=' +
				this.state.learner.value +
				'&page=' +
				'all';
		} else {
			var requestUrl =
				'/rp/v1/course-progress-rate-csv/?duration=' +
				this.state.duration.value +
				'&category=' +
				this.state.category.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&page=' +
				'all';
		}
		// this.setState({selected_data_point: data_point, table_data: [], more: 'yes'});
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			requestUrl +=
				'&wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		wp.apiFetch( {
			path: requestUrl, //Replace with the correct API
		} )
			.then( ( response ) => {
				window.location = response.filename;
				// jQuery('<a href="' + response.filename + '" download className="wrld-hidden"></a>').appendTo('body').trigger('click');
			} )
			.catch( ( error ) => {
				if ( error.data && error.data.requestData ) {
					self.setState( {
						request_data: error.data.requestData,
						moreDataLoading: false,
					} );
				}
				self.setState( {
					error,
					moreDataLoading: false,
				} );
			} );
	}

	updateLocalGroup( event ) {
		this.setState( { group: event.detail.value } );
		if ( null == event.detail.value.value ) {
			this.updateSelectorsFor(
				'group',
				null,
				'/ldlms/v1/' +
					wisdm_ld_reports_common_script_data.ld_api_settings[
						'sfwd-courses'
					] +
					'?test=1'
			);
		} else {
			let courses_enrolled = 9999999999999;
			if ( event.detail.value.courses_enrolled.length > 0 ) {
				courses_enrolled = event.detail.value.courses_enrolled;
			}
			this.updateSelectorsFor(
				'group',
				event.detail.value.value,
				'/ldlms/v1/' +
					wisdm_ld_reports_common_script_data.ld_api_settings[
						'sfwd-courses'
					] +
					'?include=' +
					courses_enrolled
			);
		}
		// let request_url = '/rp/v1/course-progress-rate?duration=' + this.state.duration.value + '&group=' + event.detail.value.value + '&category=' + this.state.category.value  + '&course=' + this.state.course.value;
		// this.updateChart(request_url);
	}

	updateLocalCategory( event ) {
		this.setState( { category: event.detail.value } );
		if ( null == event.detail.value.value ) {
			this.updateSelectorsFor(
				'category',
				null,
				'/ldlms/v1/' +
					wisdm_ld_reports_common_script_data.ld_api_settings[
						'sfwd-courses'
					]
			);
		} else {
			this.updateSelectorsFor(
				'category',
				event.detail.value.value,
				'/ldlms/v1/' +
					wisdm_ld_reports_common_script_data.ld_api_settings[
						'sfwd-courses'
					]
			);
		}
	}

	updateLocalLearner( event ) {
		this.setState( { learner: event.detail.value } );
		const request_url =
			'/rp/v1/course-progress-rate?duration=' +
			this.state.duration.value +
			'&learner=' +
			event.detail.value.value;
		this.updateChart( request_url );
	}

	updateLocalTab( event ) {
		this.setState( { active_tab: event.detail.value } );

		if ( 0 == event.detail.value ) {
			const request_url =
				'/rp/v1/course-progress-rate?duration=' +
				this.state.duration.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&category=' +
				this.state.category.value;
			this.updateChart( request_url );
		} else {
			const request_url =
				'/rp/v1/course-progress-rate?duration=' +
				this.state.duration.value +
				'&learner=' +
				this.state.learner.value;
			this.updateChart( request_url );
		}
	}

	updateLocalCourse( event ) {
		this.setState( { course: event.detail.value } );
		const request_url =
			'/rp/v1/course-progress-rate?duration=' +
			this.state.duration.value +
			'&group=' +
			this.state.group.value +
			'&course=' +
			event.detail.value.value +
			'&category=' +
			this.state.category.value;
		this.updateChart( request_url );
	}

	updateLocalDuration( event ) {
		this.setState( { duration: event.detail.value } );
		if ( 0 == this.state.active_tab ) {
			const request_url =
				'/rp/v1/course-progress-rate?duration=' +
				event.detail.value.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&category=' +
				this.state.category.value;
			this.updateChart( request_url );
		} else {
			const request_url =
				'/rp/v1/course-progress-rate?duration=' +
				event.detail.value.value +
				'&learner=' +
				this.state.learner.value;
			this.updateChart( request_url );
		}
	}

	componentDidUpdate() {
		jQuery( '.CourseProgressRate .mixed-chart' ).prepend(
			jQuery( '.CourseProgressRate .apexcharts-toolbar' )
		);
		jQuery(
			'.wisdm-learndash-reports-course-progress-rate .chart-title .dashicons, .wisdm-learndash-reports-course-progress-rate .chart-summary-revenue-figure .dashicons'
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
					.parents( '.graph-card-container' )
					.css( 'font-family' );
				$div.css( 'font-family', $font );
				$div.show();
			} )
			.on( 'mouseleave', function () {
				jQuery( this ).find( '.wdm-tooltip' ).remove();
			} );
	}

	handleReportTypeChange( event ) {
		this.setState( { reportTypeInUse: event.detail.active_reports_tab } );
		if ( 'quiz-reports' == event.detail.active_reports_tab ) {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-progress-rate',
				false
			);
		} else {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-progress-rate',
				true
			);
		}
	}

	/*showDummyImages(event){
      this.setState({course_report_type:event.detail.report_type});
      let tab_key = 'learner-specific-course-reports' == event.detail.report_type ? 1 : 0;
      const durationEvent = new CustomEvent("local_tab_change_progress", {
        "detail": {"value": tab_key }
      });
      document.dispatchEvent(durationEvent);
    }*/

	applyFilters( event ) {
		const category = event.detail.selected_categories;
		const group = event.detail.selected_groups;
		const course = event.detail.selected_courses;
		const lesson = event.detail.selected_lessons;
		const topic = event.detail.selected_topics;
		const learner = event.detail.selected_learners;

		let request_url =
			'/rp/v1/course-progress-rate/?duration=' +
			this.state.duration.value;
		if ( undefined == learner ) {
			request_url += '&category=' + category + '&group=' + group;
			this.setState( {
				category: event.detail.selected_categories_obj,
				group: event.detail.selected_groups_obj,
				active_tab: 0,
			} );
			if ( undefined != course ) {
				request_url += '&course=' + course;
				this.setState( { course: event.detail.selected_courses_obj } );
			} else {
				request_url += '&course=' + this.state.course.value;
			}
		} else {
			request_url += '&learner=' + learner;
			this.setState( {
				learner: event.detail.selected_learners_obj,
				active_tab: 1,
			} );
		}

		if ( undefined != course ) {
			this.setState( { show_supporting_text: true } );
		} else {
			this.setState( { show_supporting_text: false } );
		}

		//Time spent on a course chart should not display for lesson/topic
		if ( undefined == topic && undefined == lesson ) {
			this.updateChart( request_url );
			this.setState( { reportTypeInUse: 'default-ld-reports' } );
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-progress-rate',
				true
			);
		} else {
			//hide this block.
			this.setState( {
				reportTypeInUse: 'default-ld-reports-lesson-topic',
			} );
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-progress-rate',
				false
			);
		}
	}

	updateChart( requestUrl ) {
		this.setState( {
			isLoaded: false,
			error: null,
			request_data: null,
		} );

		// Initialize callStack if not already set (normally done by report-filters block).
		if ( ! window.callStack ) {
			window.callStack = [];
		}

		const self = this;
		const checkIfEmpty = function () {
			setTimeout( function () {
				if ( window.callStack.length > 4 ) {
					checkIfEmpty();
				} else {
					window.callStack.push( requestUrl );
					if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
						requestUrl +=
							'&wpml_lang=' +
							wisdm_ld_reports_common_script_data.wpml_lang;
					}
					wp.apiFetch( {
						path: requestUrl, //Replace with the correct API
					} )
						.then( ( response ) => {
							// Handle error responses (when API returns { code: 'no-data', message: '...' }).
							if ( response.code === 'no-data' ) {
								window.callStack.pop();
								self.setState( {
									error: {
										message: response.message,
									},
									series: [],
									options: [],
									isLoaded: true,
								} );
								if ( response.data && response.data.requestData ) {
									self.setState( {
										request_data: response.data.requestData,
									} );
								}
								if ( response.updated_on ) {
									self.setState( {
										updated_on: response.updated_on,
									} );
								} else if ( response.data && response.data.updated_on ) {
									self.setState( {
										updated_on: response.data.updated_on,
									} );
								}
								return;
							}

							if ( response.requestData ) {
								self.setState( {
									request_data: response.requestData,
								} );
							}
							if ( response.updated_on ) {
								self.setState( {
									updated_on: response.updated_on,
								} );
							}
							self.showCompletionChart( response );
						} )
						.catch( ( error ) => {
							window.callStack.pop();

							if ( error.data && error.data.requestData ) {
								self.setState( {
									request_data: error.data.requestData,
								} );
							}
							self.setState( {
								error,
								isLoaded: true,
							} );
						} );
				}
			}, 500 );
		};
		checkIfEmpty();
	}

	showCompletionChart( response ) {
		this.setState( { series: [], options: [], error: null } );

		if ( this.state.active_tab == 1 ) {
			const time_spent = [
				response.upto_20,
				response.upto_40,
				response.upto_60,
				response.upto_80,
				response.upto_100,
			];
			const users = [
				'0%-20%',
				'21%-40%',
				'41%-60%',
				'61%-80%',
				'81%-100%',
			];
			this.plotPieChart(
				time_spent,
				users,
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses' )
			);
			const chart_title = sprintf(
				// Translators: Course label.
				__( '%s Progress', 'learndash-reports-pro' ),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' )
			);
			this.setState( {
				isLoaded: true,
				chart_title,
				graph_summary: {
					left: [
						{
							title: __(
								'AVG LEARNER PROGRESS',
								'learndash-reports-pro'
							),
							value: response.averageCourseCompletion + '%',
						},
					],

					right: [
						{
							title: sprintf(
								'%s: ',
								wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Courses'
								)
							),
							value: response.course_count,
						},
						{
							title: sprintf(
								// Translators: %s: Courses label.
								__(
									'Completed All %s: ',
									'learndash-reports-pro'
								),
								wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Courses'
								)
							),
							value: response.completedCount,
						},
						{
							title: sprintf(
								// Translators: %s: Courses label.
								__(
									'Not Started Any %s: ',
									'learndash-reports-pro'
								),
								wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'courses'
								)
							),
							value: response.notstartedCount,
						},
						{
							title: __(
								'In Progress: ',
								'learndash-reports-pro'
							),
							value: response.inprogressCount,
						},
					],
					inner_help_text: sprintf(
						// Translators: %1$s: courses label, %2$s: courses label.
						__(
							'Avg Learner Progress = Total Progress in %1$s/No. of %2$s',
							'learndash-reports-pro'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'courses',
							'lower'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'courses',
							'lower'
						)
					),
				},
				help_text: sprintf(
					// Translators: %s: courses label.
					__(
						"This report shows the list of %s corresponding to the learner's progress rates.",
						'learndash-reports-pro'
					),
					wisdm_reports_get_ld_custom_lebel_if_avaiable(
						'Courses',
						'lower'
					)
				),
			} );
		} else {
			const time_percent = [
				response.upto_20,
				response.upto_40,
				response.upto_60,
				response.upto_80,
				response.upto_100,
			];
			const courses = [
				'0%-20%',
				'21%-40%',
				'41%-60%',
				'61%-80%',
				'81%-100%',
			];
			this.plotPieChart(
				time_percent,
				courses,
				__( 'Learners', 'learndash-reports-pro' )
			);
			this.setState( {
				isLoaded: true,
				chart_title: sprintf(
					// Translators: Course label.
					__( '%s Progress', 'learndash-reports-pro' ),
					wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' )
				),
				graph_summary: {
					left: [
						{
							title: sprintf(
								// Translators: %s: COURSE label.
								__(
									'AVG %s PROGRESS',
									'learndash-reports-pro'
								),
								wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'course'
								).toUpperCase()
							),
							value: response.percentage + '%',
						},
					],
					right: [],
					inner_help_text: sprintf(
						// Translators: %s: Course label.
						__(
							'Avg %s Progress = Total Progress by Learners/Learner Count',
							'learndash-reports-pro'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'course'
						)
					),
				},
				help_text: sprintf(
					// Translators: %s: course label.
					__(
						'This report shows the number and list of users against %s progress rate slabs.',
						'learndash-reports-pro'
					),
					wisdm_reports_get_ld_custom_lebel_if_avaiable(
						'Course',
						'lower'
					)
				),
			} );
		}
	}

	showDetailsModal() {
		jQuery( '.button-progress-details' ).trigger( 'click' );
	}

	detailsModal( data_point, is_paginated = false, page = 0 ) {
		const self = this;
		if ( 0 == page ) {
			page = this.state.page;
		}
		if ( 1 == this.state.active_tab ) {
			var requestUrl =
				'/rp/v1/course-progress-details/?duration=' +
				this.state.duration.value +
				'&category=' +
				this.state.category.value +
				'&learner=' +
				this.state.learner.value +
				'&datapoint=' +
				data_point +
				'&page=' +
				page;
		} else {
			var requestUrl =
				'/rp/v1/course-progress-details/?duration=' +
				this.state.duration.value +
				'&category=' +
				this.state.category.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&datapoint=' +
				data_point +
				'&page=' +
				page;
		}
		// this.setState({selected_data_point: data_point, table_data: [], more: 'yes'});
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			requestUrl +=
				'&wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		wp.apiFetch( {
			path: requestUrl, //Replace with the correct API
		} )
			.then( ( response ) => {
				// Handle error responses (when API returns { code: 'no-data', message: '...' }).
				if ( response.code === 'no-data' ) {
					self.setState( {
						error: {
							message: response.message,
						},
						table_data: [],
						more: false,
					} );
					if ( response.data && response.data.requestData ) {
						self.setState( {
							request_data: response.data.requestData,
						} );
					}
					return;
				}

				let table = response.tableData;
				if ( undefined == response ) {
					table = [];
				}
				if ( is_paginated ) {
					table = {
						...this.state.table_data,
						...table,
					};
					// table = this.state.tableData.concat(table); // for array
				}
				this.setState( {
					selected_data_point: data_point,
					table_data: table,
					more: response.more_data,
					error: null,
				} );
				self.showDetailsModal();
			} )
			.catch( ( error ) => {
				if ( error.data && error.data.requestData ) {
					self.setState( { request_data: error.data.requestData } );
				}
				self.setState( {
					error,
				} );
			} );
	}

	plotPieChart(
		data,
		labels,
		tooltipTextLineOne = sprintf(
			// Translators: Course label.
			__( '%s Progress', 'learndash-reports-pro' ),
			wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' )
		)
	) {
		const self = this;
		const chart_options = {
			colors: [ '#5f5f5f' ],
			theme: {
				monochrome: {
					enabled: true,
					color: '#008AD8',
					shadeTo: 'dark',
					shadeIntensity: 0.65,
				},
			},
			chart: {
				width: '100%',
				height: '300',
				type: 'donut',
				dropShadow: {
					enabled: true,
					color: '#111',
					top: -1,
					left: 3,
					blur: 3,
					opacity: 0.2,
				},
				events: {
					mounted( chartContext, config ) {
						window.callStack.pop();
					},
					dataPointSelection: ( event, chartContext, config ) => {
						self.detailsModal(
							config.w.config.labels[ config.dataPointIndex ]
						);
					},
				},
			},
			stroke: {
				width: 0,
			},

			plotOptions: {
				pie: {
					donut: {
						dataLabels: {
							enabled: true,
						},
						total: {
							show: false,
							showAlways: false,
							label: 'Total',
							fontSize: '22px',
							fontFamily: 'Helvetica, Arial, sans-serif',
							fontWeight: 600,
							color: '#373d3f',
							formatter( w ) {
								return w.globals.seriesTotals.reduce(
									( a, b ) => {
										return a + b;
									},
									0
								);
							},
						},
					},
				},
			},
			labels,
			responsive: [
				{
					breakpoint: 480,
					options: {
						chart: {
							width: 200,
						},
					},
				},
			],
			legend: {
				formatter( seriesName, opts ) {
					return [
						seriesName,
						' - ',
						opts.w.globals.series[ opts.seriesIndex ],
						' ',
						tooltipTextLineOne,
						'',
					];
				},
			},
			tooltip: {
				custom( { series, seriesIndex, dataPointIndex, w } ) {
					return (
						'<div className="wisdm-donut-chart-tooltip"> <div className="tooltip-body"> <span><strong>' +
						w.globals.labels[ seriesIndex ] +
						'</strong>' +
						' | ' +
						tooltipTextLineOne +
						' : ' +
						series[ seriesIndex ] +
						'</span></div></div>'
					);
				},

				y: {
					formatter(
						value,
						{ series, seriesIndex, dataPointIndex, w }
					) {
						return value;
					},
				},
			},
		};
		this.setState( {
			graph_type: 'donut',
			series: data,
			options: chart_options,
		} );
		window.callStack.pop();
	}

	isValidGraphData() {
		if (
			undefined == this.state.options ||
			0 == this.state.options.length
		) {
			return false;
		}
		if ( undefined == this.state.series || 0 == this.state.series.length ) {
			return false;
		}

		return true;
	}

	refreshUpdateTime() {
		if ( 0 == this.state.active_tab ) {
			const request_url =
				'/rp/v1/course-progress-rate?duration=' +
				this.state.duration.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&category=' +
				this.state.category.value +
				'&disable_cache=true';
			this.updateChart( request_url );
		} else {
			const request_url =
				'/rp/v1/course-progress-rate?duration=' +
				this.state.duration.value +
				'&learner=' +
				this.state.learner.value +
				'&disable_cache=true';
			this.updateChart( request_url );
		}
	}

	render() {
		let body = <div></div>;
		// if(this.state.course_report_type == 'learner-specific-course-reports' && !wisdm_ld_reports_common_script_data.is_pro_version_active){
		//     body =  <DummyReports image_path='tsoc.png'></DummyReports>; // cspell:disable-line
		//       return (body);
		// }
		let data_validation = '';
		if ( ! this.isValidGraphData() ) {
			data_validation = 'invalid-or-empty-data';
		}
		if (
			'' != this.state.reportTypeInUse &&
			'default-ld-reports' != this.state.reportTypeInUse
		) {
			body = '';
		} else if ( ! this.state.isLoaded ) {
			// yet loading
			body = <WisdmLoader text={ this.state.show_supporting_text } />;
		} else {
			let graph = '';
			if ( ! this.state.error ) {
				graph = (
					<div className="course-progress-rate">
						<Chart
							options={ this.state.options }
							series={ this.state.series }
							width={ this.state.options.chart.width }
							height={ this.state.options.chart.height }
							type={ this.state.graph_type }
						/>
					</div>
				);
			}
			body = (
				<div
					className={
						'wisdm-learndash-reports-chart-block ' + data_validation
					}
				>
					<div className="wisdm-learndash-reports-course-progress-rate graph-card-container">
						<div className="chart-header course-progress-rate-chart-header">
							<div className="chart-title">
								<span>{ this.state.chart_title }</span>
								<span
									className="dashicons dashicons-info-outline widm-ld-reports-info"
									data-title={ this.state.help_text }
								></span>
								<DurationFilter
									wrapper_class="chart-summary-inactive-users"
									duration={ this.state.duration }
								/>
							</div>
							<div className="chart_update_time">
								<span>
									{ __(
										'Last updated: ',
										'learndash-reports-pro'
									) }
								</span>
								<span>{ this.state.updated_on }</span>
								<div className="chart-refresh-icon">
									<span
										className="dashicons dashicons-image-rotate"
										data-title={ __(
											'Click this to refresh the chart',
											'learndash-reports-pro'
										) }
										onClick={ this.refreshUpdateTime.bind(
											this
										) }
									></span>
								</div>
							</div>
							<LocalFilters
								group={ this.state.group }
								category={ this.state.category }
								groups={ this.state.groups }
								categories={ this.state.categories }
								course={ this.state.course }
								courses={ this.state.courses }
								active_tab={ this.state.active_tab }
								learner={ this.state.learner }
							/>
							<ChartSummarySection
								wrapper_class="chart-summary-course-progress-rate"
								graph_summary={ this.state.graph_summary }
								error={ this.state.error }
							/>
							{ /*<SummarySection />*/ }
						</div>
						<div>
							{ graph }
							<span className="note">
								<strong>
									{ __( 'Note: ', 'learndash-reports-pro' ) }
								</strong>
								{ __(
									'Click on any item on the pie chart to see more details.',
									'learndash-reports-pro'
								) }
							</span>
						</div>
					</div>
					{ this.state.show_progress_details_modal && (
						<Modal
							onRequestClose={ this.closeProgressDetailsModal }
							className={
								'learndash-propanel-modal progress_details_modal'
							}
						>
							<ProgressDetailsTable
								type={
									1 == this.state.active_tab
										? 'learner'
										: 'course'
								}
								data_point={ this.state.selected_data_point }
								table={ this.state.table_data }
								course={ this.state.course.label }
								learner={
									this.state.learner
										? this.state.learner.label
										: ''
								}
							/>
							{ 'yes' == this.state.more ? (
								<span
									className="load-more-ajax"
									onClick={ this.addMoreData }
								>
									{ __(
										'View More',
										'learndash-reports-pro'
									) }
								</span>
							) : (
								<span></span>
							) }
						</Modal>
					) }
					<button
						className="button-progress-details wrld-hidden"
						onClick={ this.openProgressDetailsModal }
					></button>
				</div>
			);
		}
		return body;
	}
}

export default CourseProgressRate;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-course-progress-rate front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( CourseProgressRate ) );
	}
} );
