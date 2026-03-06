// cspell:ignore labeltext timespent datapoint rtype

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
import Select from '../commons/emotion-styles-provider/select.js';
import { Modal } from '@wordpress/components';
import DurationFilter from './component-duration-filter.js';
import LocalFilters from './component-local-filters.js';
import TimeSpentTable from './component-time-spent-table.js';
import Cookies from 'js-cookie';

window.ld_api_settings = {
	'sfwd-courses': 'sfwd-courses',
	'sfwd-lessons': 'sfwd-lessons',
	'sfwd-topic': 'sfwd-topic',
	'sfwd-quiz': 'sfwd-quiz',
	'sfwd-question': 'sfwd-question',
	users: 'users',
	groups: 'groups',
};

function wisdmLDRConvertTime( seconds ) {
	// Handle invalid input: NaN, null, undefined, or non-numeric values
	if ( seconds === undefined || seconds === null ) {
		return '00:00:00';
	}
	// Convert to number and check for NaN
	const numSeconds = Number( seconds );
	if ( isNaN( numSeconds ) || ! isFinite( numSeconds ) ) {
		return '00:00:00';
	}
	let hours = Math.floor( numSeconds / 3600 );
	let minutes = Math.floor( ( numSeconds % 3600 ) / 60 );
	var seconds = Math.floor( ( numSeconds % 3600 ) % 60 );
	if ( hours < 10 ) {
		hours = '0' + hours;
	}
	if ( minutes < 10 ) {
		minutes = '0' + minutes;
	}
	if ( seconds < 10 ) {
		seconds = '0' + seconds;
	}
	if ( !! hours ) {
		if ( !! minutes ) {
			return `${ hours }:${ minutes }:${ seconds }`;
		}
		return `${ hours }:00:${ seconds }`;
	}
	if ( !! minutes ) {
		return `00:${ minutes }:${ seconds }`;
	}
	return `00:00:${ seconds }`;
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

class TimeSpent extends Component {
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
			noData: false,
			error,
			series: [],
			options: [],
			reportTypeInUse: wisdm_ld_reports_common_script_data.report_type,
			chart_title: __( 'Time spent', 'learndash-reports-pro' ),
			lock_icon: '',
			request_data: null,
			active_tab: tab_selected,
			group: {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			},
			groups: [],
			course: {
				value: null,
				label: sprintf(
					// Translators: %s: courses label.
					__( 'All %s', 'learndash-reports-pro' ),
					wisdm_reports_get_ld_custom_lebel_if_avaiable(
						'Courses',
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
				// Translators: %s: course label.
				__(
					'This report displays the time spent on a %s.',
					'learndash-reports-pro'
				),
				wisdm_reports_get_ld_custom_lebel_if_avaiable(
					'course',
					'lower'
				)
			),
			course_report_type: null,
			show_supporting_text: false,
			is_group_enabled: false,
			is_category_enabled: false,
			show_time_spent_detail_modal: false,
			page: 1,
			timePeriodOptions: [
				{
					value: 1,
					label: __( 'Minutes', 'learndash-reports-pro' ),
				},
				{
					value: 2,
					label: __( 'Hours', 'learndash-reports-pro' ),
				},
				{
					value: 3,
					label: __( 'Days', 'learndash-reports-pro' ),
				},
			],
			selectedTimePeriodOptions: {
				value: 1,
				label: __( 'Minutes', 'learndash-reports-pro' ),
			},
			isToggleChecked: false,
			learnerTotaltimeonEachCourse: [], // cspell:disable-line
			learnerTotalAvgTimeByAll: [],
			learnerLabels: [],
			hourIntervalForLearner: 0,
			minuteIntervalForLerner: 0,
			daysIntervalForLearner: 0,
			isOldDataMigrated: true,
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
		this.openTimeSpentModal = this.openTimeSpentModal.bind( this );
		this.closeProgressDetailsModal =
			this.closeProgressDetailsModal.bind( this );
		this.addMoreData = this.addMoreData.bind( this );
		this.startCSVDownload = this.startCSVDownload.bind( this );
		this.timeSpentFilterSetting = this.timeSpentFilterSetting.bind( this );
		this.handleToggleSwich = this.handleToggleSwich.bind( this ); // cspell:disable-line
		this.handleTimePeriodChange = this.handleTimePeriodChange.bind( this );
		this.defaultFiltersLoaded = this.defaultFiltersLoaded.bind( this );
	}

	openTimeSpentModal() {
		document.body.classList.add( 'wrld-ts-open' );
		this.setState( {
			show_time_spent_detail_modal: true,
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
		document.body.classList.remove( 'wrld-ts-open' );
		this.setState( {
			show_time_spent_detail_modal: false,
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
		this.setState( { isLoaded: false } );

		const courses = wisdm_ld_reports_common_script_data.courses;
		const groups = wisdm_ld_reports_common_script_data.course_groups;
		const categories =
			wisdm_ld_reports_common_script_data.course_categories;

		if ( courses.length === 0 ) {
			this.setState( {
				isLoaded: true,
				noData: true,
				error: { message: 'No courses found' },
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
			'/rp/v1/time-spent-on-a-course?duration=' +
				this.state.duration.value +
				'&course=' +
				courses[ 0 ].value +
				'&rtype=false' +
				'&timeperiod= 1'
		);

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
				.catch( ( error ) => {
					console.log( 'No data error encountered.' );
				} );
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
			'local_time_spent_duration_change',
			this.updateLocalDuration
		);
		document.addEventListener(
			'local_group_change_time_spent',
			this.updateLocalGroup
		);
		document.addEventListener(
			'local_learner_change_time_spent',
			this.updateLocalLearner
		);
		document.addEventListener(
			'local_category_change_time_spent',
			this.updateLocalCategory
		);
		document.addEventListener(
			'local_tab_change_time_spent',
			this.updateLocalTab
		);
		document.addEventListener(
			'local_course_change_time_spent',
			this.updateLocalCourse
		);
		document.addEventListener(
			'start_csv_download_ts',
			this.startCSVDownload
		);
		document.addEventListener(
			'time_spent_filter_setting',
			this.timeSpentFilterSetting
		);
		document.addEventListener(
			'wrld-default-filters-loaded',
			this.defaultFiltersLoaded
		);

		// Fallback: If report-filters block is not present, load data directly after a short delay.
		setTimeout( () => {
			if (
				! this.state.isLoaded &&
				! document.querySelector(
					'.wisdm-learndash-reports-report-filters'
				)
			) {
				// Initialize callStack if not already set (normally done by report-filters block).
				if ( ! window.callStack ) {
					window.callStack = [];
				}

				// Fetch the courses data first, like report-filters block does.
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
					//   path: callback_path, //Replace with the correct API
					// }).then((response) => {

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
					const request_url =
						'/rp/v1/time-spent-on-a-course?duration=' +
						this.state.duration.value +
						'&group=' +
						selection +
						'&category=' +
						this.state.category.value +
						'&course=' +
						courses[ 0 ].value +
						'&rtype=false' +
						'&timeperiod= 1';
					this.updateChart( request_url );
					//Patch logic for react state update on browser refresh bug.
					// const groupsLoadEvent = new CustomEvent("progress-parent-group-changed", {
					//   "detail": {"value": group }
					// });
					// document.dispatchEvent(groupsLoadEvent);
					//});
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
							'/rp/v1/time-spent-on-a-course?duration=' +
							this.state.duration.value +
							'&group=' +
							selection +
							'&category=' +
							this.state.category.value +
							'&course=' +
							this.state.course.value +
							'&is_group_enabled=' +
							this.state.is_group_enabled +
							'&is_category_enabled=' +
							this.state.is_category_enabled +
							'&rtype=' +
							this.state.isToggleChecked +
							'&timeperiod=' +
							this.state.selectedTimePeriodOptions.value;
						this.updateChart( request_url );
					} );

					// });
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
						let courses = this.getCourseListFromJson( response );
						if ( false != courses && courses.length > 0 ) {
							const allowed_courses = courses.map(
								( value ) => value.value
							);
							courses = courses.filter( ( value ) =>
								allowed_courses.includes( value.value )
							);
							console.log( courses );
							if ( courses.length > 0 ) {
								//if selected course is not in the list then clear the field
								let course_in_the_list = false;
								const selected_course_id =
									this.state.course.value;
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
							} else {
								this.setState( {
									courses,
									course: {
										value: null,
										label: sprintf(
											// Translators: %s: courses label.
											__(
												'No %s available for the category',
												'learndash-reports-pro'
											),
											wisdm_reports_get_ld_custom_lebel_if_avaiable(
												'Courses',
												'lower'
											)
										),
										group: this.state.groups[ 0 ],
									},
									// course: courses[0]
								} );
							}
						}
						const request_url =
							'/rp/v1/time-spent-on-a-course?duration=' +
							this.state.duration.value +
							'&group=' +
							this.state.group.value +
							'&category=' +
							selection +
							'&course=' +
							this.state.course.value +
							'&is_group_enabled=' +
							this.state.is_group_enabled +
							'&is_category_enabled=' +
							this.state.is_category_enabled +
							'&rtype=' +
							this.state.isToggleChecked +
							'&timeperiod=' +
							this.state.selectedTimePeriodOptions.value;
						this.updateChart( request_url );
					} )
					.catch( ( error ) => {} );

				// })
				// .catch((error) => {});
				break;
			default:
				break;
		}
	}

	timeSpentFilterSetting( event ) {
		this.setState( {
			is_group_enabled: event.detail.group,
			is_category_enabled: event.detail.category,
		} );
		const group_enabled = event.detail.group ? 'enable' : 'disable';
		const category_enabled = event.detail.category ? 'enable' : 'disable';
		localStorage.setItem( 'is_group_enabled', group_enabled );
		localStorage.setItem( 'is_category_enabled', category_enabled );
		//call api to save filter
		// let filter_request_url =
		//           "/rp/v1/time-spent-on-a-course-filter?category=" + event.detail.category + "&group="+ event.detail.group;
		// wp.apiFetch({
		//   path: filter_request_url, //Replace with the correct API
		// })
		//   .then((response) => {
		//     if (response.requestData) {
		//       console.log("Filters are saved");
		//     }
		//   })
		//   .catch((error) => {
		//       console.log("Filters not saved");
		//   });
	}

	startCSVDownload( event ) {
		const self = this;
		if ( this.state.active_tab == 1 ) {
			var requestUrl =
				'/rp/v1/time-spent-on-a-course-csv/?duration=' +
				this.state.duration.value +
				'&learner=' +
				( this.state.learner ? this.state.learner.value : '' ) +
				'&page=' +
				'all' +
				'&rtype=' +
				this.state.isToggleChecked +
				'&timeperiod=' +
				this.state.selectedTimePeriodOptions.value;
		} else {
			var requestUrl =
				'/rp/v1/time-spent-on-a-course-csv/?duration=' +
				this.state.duration.value +
				'&category=' +
				this.state.category.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&page=' +
				'all' +
				'&rtype=' +
				this.state.isToggleChecked +
				'&timeperiod=' +
				this.state.selectedTimePeriodOptions.value;
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
				'/ldlms/v1/' + ld_api_settings[ 'sfwd-courses' ] + '?test=1'
			);
		} else {
			this.updateSelectorsFor(
				'group',
				event.detail.value.value,
				'/ldlms/v1/' +
					ld_api_settings[ 'sfwd-courses' ] +
					'?include=' +
					event.detail.value.courses_enrolled
			);
		}
		// let request_url = '/rp/v1/time-spent-on-a-course?duration=' + this.state.duration.value + '&group=' + event.detail.value.value + '&category=' + this.state.category.value  + '&course=' + this.state.course.value;
		// this.updateChart(request_url);
	}

	updateLocalCategory( event ) {
		this.setState( { category: event.detail.value } );
		if ( null == event.detail.value.value ) {
			this.updateSelectorsFor(
				'category',
				null,
				'/ldlms/v1/' + ld_api_settings[ 'sfwd-courses' ]
			);
		} else {
			this.updateSelectorsFor(
				'category',
				event.detail.value.value,
				'/ldlms/v1/' + ld_api_settings[ 'sfwd-courses' ]
			);
		}
	}

	updateLocalLearner( event ) {
		this.setState( { learner: event.detail.value } );
		const request_url =
			'/rp/v1/time-spent-on-a-course?duration=' +
			this.state.duration.value +
			'&learner=' +
			event.detail.value.value;
		this.updateChart( request_url );
	}

	updateLocalTab( event ) {
		this.setState( {
			active_tab: event.detail.value,
			selectedTimePeriodOptions: {
				value: 1,
				label: __( 'Minutes', 'learndash-reports-pro' ),
			},
		} );

		if ( 0 == event.detail.value ) {
			const request_url =
				'/rp/v1/time-spent-on-a-course?duration=' +
				this.state.duration.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&category=' +
				this.state.category.value +
				'&rtype=' +
				this.state.isToggleChecked +
				'&timeperiod=' +
				this.state.selectedTimePeriodOptions.value;
			this.state.courses.length === 0
				? this.setState( { error: { message: 'No courses found.' } } )
				: this.updateChart( request_url );
		} else {
			const request_url =
				'/rp/v1/time-spent-on-a-course?duration=' +
				this.state.duration.value +
				'&learner=' +
				( this.state.learner ? this.state.learner.value : '' );
			this.state.courses.length === 0
				? this.setState( {
						error: {
							message:
								'No data available for the selected duration or filters.',
						},
				  } )
				: this.updateChart( request_url );
		}
	}

	updateLocalCourse( event ) {
		this.setState( { course: event.detail.value } );
		const request_url =
			'/rp/v1/time-spent-on-a-course?duration=' +
			this.state.duration.value +
			'&group=' +
			this.state.group.value +
			'&course=' +
			event.detail.value.value +
			'&category=' +
			this.state.category.value +
			'&rtype=' +
			this.state.isToggleChecked +
			'&timeperiod=' +
			this.state.selectedTimePeriodOptions.value;
		this.updateChart( request_url );
	}

	updateLocalDuration( event ) {
		this.setState( { duration: event.detail.value } );
		if ( 0 == this.state.active_tab ) {
			const request_url =
				'/rp/v1/time-spent-on-a-course?duration=' +
				event.detail.value.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&category=' +
				this.state.category.value +
				'&rtype=' +
				this.state.isToggleChecked +
				'&timeperiod=' +
				this.state.selectedTimePeriodOptions.value;
			this.updateChart( request_url );
		} else {
			const request_url =
				'/rp/v1/time-spent-on-a-course?duration=' +
				event.detail.value.value +
				'&learner=' +
				( this.state.learner ? this.state.learner.value : '' );
			this.updateChart( request_url );
		}
	}

	componentDidUpdate() {
		jQuery( '.TimeSpent .mixed-chart' ).prepend(
			jQuery( '.TimeSpent .apexcharts-toolbar' )
		);
		jQuery(
			'.wisdm-learndash-reports-time-spent-on-a-course .chart-title .dashicons, .wisdm-learndash-reports-time-spent-on-a-course .chart-summary-revenue-figure .dashicons'
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
				'.wp-block-wisdm-learndash-reports-time-spent-on-a-course',
				false
			);
		} else {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-time-spent-on-a-course',
				true
			);
		}
	}

	// cspell:disable-next-line
	handleToggleSwich( event ) {
		this.setState( ( prevState ) => ( {
			isToggleChecked: ! prevState.isToggleChecked,
		} ) );

		if ( this.state.active_tab == 0 ) {
			const request_url =
				'/rp/v1/time-spent-on-a-course?duration=' +
				this.state.duration.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&category=' +
				this.state.category.value +
				'&rtype=' +
				! this.state.isToggleChecked +
				'&timeperiod=' +
				this.state.selectedTimePeriodOptions.value;
			this.updateChart( request_url );
		}
	}
	// cspell:disable-next-line
	handleTimePeriodChange( envt ) {
		this.setState( ( prevState ) => ( {
			selectedTimePeriodOptions: envt, // cspell:disable-line
		} ) );

		if ( this.state.active_tab == 0 ) {
			const request_url =
				'/rp/v1/time-spent-on-a-course?duration=' +
				this.state.duration.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&category=' +
				this.state.category.value +
				'&rtype=' +
				this.state.isToggleChecked +
				'&timeperiod=' +
				envt.value; // cspell:disable-line
			this.updateChart( request_url );
		} else {
			this.setState( {
				isLoaded: false,
				selectedTimePeriodOptions: envt, // cspell:disable-line
			} );
			console.log( envt ); // cspell:disable-line
			this.plotLearnerChart(
				this.state.learnerTotaltimeonEachCourse, // cspell:disable-line
				this.state.learnerTotalAvgTimeByAll,
				this.state.learnerLabels,
				envt, // cspell:disable-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses' )
			);
			const self = this;
			setTimeout( () => {
				self.setState( { isLoaded: true } );
			}, 500 );
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
			'/rp/v1/time-spent-on-a-course/?duration=' +
			this.state.duration.value;
		if ( undefined == learner ) {
			request_url += '&category=' + category + '&group=' + group;
			this.setState( {
				category: event.detail.selected_categories_obj,
				group: event.detail.selected_groups_obj,
				active_tab: 0,
			} );
			if ( undefined != course ) {
				request_url +=
					'&course=' + course + '&rtype=false' + '&timeperiod= 1';
				this.setState( { course: event.detail.selected_courses_obj } );
			} else {
				request_url +=
					'&course=' +
					this.state.course.value +
					'&rtype=false' +
					'&timeperiod= 1';
			}
		} else {
			request_url +=
				'&learner=' + learner + '&rtype=false' + '&timeperiod= 1';
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
				'.wp-block-wisdm-learndash-reports-time-spent-on-a-course',
				true
			);
		} else {
			//hide this block.
			this.setState( {
				reportTypeInUse: 'default-ld-reports-lesson-topic',
			} );
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-time-spent-on-a-course',
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
							window.callStack.pop();
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

							if (
								error &&
								error.data &&
								error.data.updated_on !== undefined
							) {
								self.setState( {
									updated_on: error.data.updated_on,
									isOldDataMigrated:
										error.data.is_old_data_migrated,
								} );
							}
							if (
								error &&
								error.data &&
								error.data.requestData
							) {
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
		// Handle no-data response (now returned as 200 instead of error).
		if ( response.noData ) {
			this.setState( {
				isLoaded: true,
				error: { message: response.message },
				request_data: response.requestData,
				updated_on: response.updated_on,
				isOldDataMigrated: response.is_old_data_migrated,
			} );
			return;
		}

		const local_group_enabled =
			localStorage.getItem( 'is_group_enabled' ) === 'enable'
				? true
				: false;
		const local_category_enabled =
			localStorage.getItem( 'is_category_enabled' ) === 'enable'
				? true
				: false;

		this.setState( {
			series: [],
			options: [],
			error: null,
			learnerLabels: response.courseLabels,
			// cspell:disable-next-line
			learnerTotaltimeonEachCourse:
				response.total_time_by_user_on_each_course,
			learnerTotalAvgTimeByAll: response.average_time_spent_by_all,
			is_group_enabled: local_group_enabled,
			is_category_enabled: local_category_enabled,
			isOldDataMigrated: response.is_old_data_migrated,
		} );

		if ( this.state.active_tab == 1 ) {
			this.plotLearnerChart(
				response.total_time_by_user_on_each_course,
				response.average_time_spent_by_all,
				response.courseLabels,
				this.state.selectedTimePeriodOptions,
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses' )
			);
			const chart_title = __( 'Time Spent', 'learndash-reports-pro' );
			this.setState( {
				isLoaded: true,
				chart_title,
				graph_summary: {
					left: [
						{
							title: __(
								'AVG. time spent by learner',
								'learndash-reports-pro'
							),
							value: wisdmLDRConvertTime(
								response.average_time_by_learner
							),
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
							value: response.total_courses,
						},
					],
					inner_help_text: sprintf(
						// Translators: %1$s: courses label, %2$s: courses label.
						__(
							'Avg Time Spent by Learner = Total time spent in %1$s/No. of %2$s',
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
						"This report shows the list of %s corresponding to the learner's time spent.",
						'learndash-reports-pro'
					),
					wisdm_reports_get_ld_custom_lebel_if_avaiable(
						'courses',
						'lower'
					)
				),
			} );
		} else {
			this.plotBarCourseChart(
				response.learner_counts_in_interval,
				response.time_invervals, // cspell:disable-line
				response.total_learners,
				response.average_time_spent,
				this.state.selectedTimePeriodOptions.value,
				__( 'Learners', 'learndash-reports-pro' )
			);
			this.setState( {
				isLoaded: true,
				chart_title: __( 'Time Spent', 'learndash-reports-pro' ),
				graph_summary: {
					left: [
						{
							title: this.state.isToggleChecked
								? sprintf(
										// Translators: %s: course label.
										__(
											'AVG. time spent (%s progress)',
											'learndash-reports-pro'
										),
										wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'course',
											'lower'
										)
								  )
								: sprintf(
										// Translators: %s: course label.
										__(
											'AVG. time spent (%s completion)',
											'learndash-reports-pro'
										),
										wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'course',
											'lower'
										)
								  ),
							value: wisdmLDRConvertTime(
								response.average_time_spent
							),
						},
					],
					right: [
						{
							title: __(
								'Total learners: ',
								'learndash-reports-pro'
							),
							value: response.total_learners,
						},

						{
							title: __(
								this.state.isToggleChecked
									? 'Learners in progress: '
									: 'Learners completed: ',
								'learndash-reports-pro'
							),
							value:
								parseInt( response.learner_below_average ) +
								parseInt( response.learner_above_average ),
						},
						{
							title: __(
								'Behind avg. time : ',
								'learndash-reports-pro'
							),
							value: response.learner_below_average,
						},
						{
							title: __(
								'Exceeding avg. time : ',
								'learndash-reports-pro'
							),
							value: response.learner_above_average,
						},
					],
					inner_help_text: __(
						'Avg Time Spent = Total time spent by Learners/Learner Count',
						'learndash-reports-pro'
					),
				},
				help_text: __(
					'This report shows the number and list of users against time spent slabs.',
					'learndash-reports-pro'
				),
			} );
		}
	}

	showDetailsModal() {
		jQuery( '.button-timespent-detail' ).trigger( 'click' );
	}

	detailsModal( selectedBarData, is_paginated = false, page = 0 ) {
		const self = this;
		if ( 0 == page ) {
			page = this.state.page;
		}
		if ( 1 == this.state.active_tab ) {
			var requestUrl =
				'/rp/v1/course-time-details/?duration=' +
				this.state.duration.value +
				'&category=' +
				this.state.category.value +
				'&learner=' +
				( this.state.learner ? this.state.learner.value : '' ) +
				'&datapoint=' +
				selectedBarData.dataPoint +
				'&lower_limit=' +
				selectedBarData.lower +
				'&upper_limit=' +
				selectedBarData.upper +
				'&page=' +
				page;
		} else {
			var requestUrl =
				'/rp/v1/course-time-details/?duration=' +
				this.state.duration.value +
				'&category=' +
				this.state.category.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&datapoint=' +
				selectedBarData.dataPoint +
				'&lower_limit=' +
				selectedBarData.lower +
				'&upper_limit=' +
				selectedBarData.upper +
				'&page=' +
				page +
				'&timeperiod=' +
				this.state.selectedTimePeriodOptions.value +
				'&rtype=' +
				this.state.isToggleChecked;
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
				console.log( response );
				let table = response.tableData;
				if ( undefined == response ) {
					table = [];
				}
				if ( is_paginated ) {
					console.log( 'paginated : ' + table.length );
					table = [ ...this.state.table_data, ...table ];
					console.log( 'paginated after : ' + table.length );
					// table = this.state.tableData.concat(table); // for array
				}
				this.setState( {
					selected_data_point: selectedBarData.dataPoint,
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
	formateLearnerYaxis( text ) {
		const words = text.toString().split( ' ' );
		const result = [];
		let currentSubstring = '';

		for ( let i = 0; i < words.length; i++ ) {
			const word = words[ i ];

			if ( ( currentSubstring + ' ' + word ).length <= 10 ) {
				currentSubstring += ( currentSubstring ? ' ' : '' ) + word;
			} else {
				result.push( currentSubstring );
				currentSubstring = word;
			}
		}

		if ( currentSubstring ) {
			result.push( currentSubstring );
		}

		return result;
	}

	getMaxHoursLimit( newSeconds ) {
		// Calculate the minimum and maximum interval values

		if ( newSeconds < 518400 ) {
			this.setState( { daysIntervalForLearner: 60 * 60 } );
			return 518400;
		}

		const minValue = 0;
		const maxValue = Math.ceil( newSeconds / ( 24 * 60 * 60 ) ); // Maximum value for an interval

		const intervalCount = 6; // Number of intervals

		const intervalSize = Math.ceil(
			( maxValue - minValue ) / intervalCount
		); // Calculate the interval size
		console.log( intervalSize * ( 24 * 60 * 60 ) );
		this.setState( {
			daysIntervalForLearner: intervalSize * ( 24 * 60 * 60 ),
		} );
		console.log( 'max learner : ' + intervalSize * 5 * ( 24 * 60 * 60 ) );
		const maxHoursLimit = intervalSize * 5 * ( 24 * 60 * 60 );
		return newSeconds;
		return maxHoursLimit;
	}

	getLearnerXaxisLabels( value, timePeriod ) {
		if ( value == 0 ) {
			return 0;
		}
		if ( timePeriod.value == 1 ) {
			const actualTime = value / 60;
			return parseInt( actualTime );
		}
		if ( timePeriod.value == 2 ) {
			const actualTime = value / ( 60 * 60 );
			return parseInt( actualTime );
		}

		if ( timePeriod.value == 3 ) {
			const actualTime = value / this.state.daysIntervalForLearner;
			return parseInt( actualTime );
		}
		return value;
	}

	plotLearnerChart(
		learnerData,
		allLearnerData,
		labels,
		timePeriod,
		tooltipTextLineOne = __( 'Time Spent', 'learndash-reports-pro' )
	) {
		const self = this;

		// Ensure arrays are defined and not null
		learnerData = learnerData || [];
		allLearnerData = allLearnerData || [];
		labels = labels || [];

		if ( wisdm_ld_reports_common_script_data.is_rtl ) {
			learnerData = learnerData.reverse();
			allLearnerData = allLearnerData.reverse();
			labels = labels.reverse();
		}
		const lernerName = this.state.learner
			? this.state.learner.label
			: __( 'All Learners', 'learndash-reports-pro' );
		const series = [
			{
				name:
					__( 'Total time spent by ', 'learndash-reports-pro' ) +
					lernerName,
				data: learnerData,
			},
			{
				name: __(
					'Avg. time spent by all learners',
					'learndash-reports-pro'
				),
				data: allLearnerData,
			},
		];
		const dataX = labels;
		const no_day = __(
			'Time spent (in ' + timePeriod.label.toLowerCase() + ')',
			'learndash-reports-pro'
		);
		const with_day = __( 'Time spent (in hours)', 'learndash-reports-pro' );
		const nameX = timePeriod.label === 'Days' ? with_day : no_day;
		const nameY = __( 'Learners', 'learndash-reports-pro' );

		const n_chart_options = {
			chart: {
				id: 'basic-bar',
				width: 530,
				height: labels.length * 70 < 450 ? 400 : labels.length * 70,
				zoom: {
					enabled: false,
				},
				toolbar: {
					show: false,
				},
				events: {
					mounted( chartContext, config ) {
						window.callStack.pop();
					},
					dataPointSelection: ( event, chartContext, config ) => {
						//self.detailsModal(dataX[config.dataPointIndex]);
					},
				},
			},
			legend: {
				position: 'top', // Set the position of the legends to 'top'
			},
			colors: [ '#008AD8', '#787878' ],

			plotOptions: {
				bar: {
					horizontal: true,
					barWidth: '80%', // Adjust the bar width as per your requirement
				},
			},
			xaxis: {
				title: {
					text: nameX,
				},
				categories: dataX,
				labels: {
					formatter: ( value ) => {
						return this.getLearnerXaxisLabels( value, timePeriod );
					},
					rotate: wisdm_ld_reports_common_script_data.is_rtl
						? 45
						: -45,
				},
				tickAmount: 6,
				min: 0,
			},
			yaxis: {
				axisBorder: {
					show: true,
				},
				labels: {
					formatter: ( value ) => {
						return this.formateLearnerYaxis( value );
					},
					align: wisdm_ld_reports_common_script_data.is_rtl
						? 'left'
						: 'right',
					// offsetX: 50, // Bug offset never gets applied.
				},
				reversed: wisdm_ld_reports_common_script_data.is_rtl,
				min: 0,
				max:
					timePeriod.value == 1
						? Math.max( ...learnerData ) + 600
						: timePeriod.value == 2
						? ( Math.max( ...learnerData ) < 60 * 60 * 6
								? 60 * 60 * 6
								: Math.max( ...learnerData ) ) + 3600
						: this.getMaxHoursLimit( Math.max( ...learnerData ) ),
			},
			dataLabels: {
				enabled: false,
			},
			tooltip: {
				custom( { series, seriesIndex, dataPointIndex, w } ) {
					return (
						// cspell:disable-next-line
						'<div className="wisdm-bar-timespent-chart-tooltip"> <div className="tooltip-body-learner"> <div className="wrld-learner-time">  <span className="wrld-lerner-time-lable"></span> <span className="wrld-lerner-time-lable-text"> Total time spent</span> </div> <b><span className="wrld-ts-time wrld-ts-time-learner">  ' +
						wisdmLDRConvertTime( series[ 0 ][ dataPointIndex ] ) +
						// cspell:disable-next-line
						' </span></b>  </br>  <div className="wrld-learner-avarage-time"> <span className="wrld-all-lerner-time-lable"></span> <span className="wrld-lerner-time-lable-text"> Avg. time spent by all learners</span> </div><b> <span className="wrld-ts-time" >' +
						wisdmLDRConvertTime( series[ 1 ][ dataPointIndex ] ) +
						'</span></b></div> </div></div>'
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
				position: 'middle',
				offsetY: -50, // Adjust this value to move the tooltip vertically
				offsetX: -50, // Adjust this value to move the tooltip horizontally
			},
		};

		this.setState( {
			graph_type: 'bar',
			series,
			options: n_chart_options,
		} );
		window.callStack.pop();
	}

	getUpperLowerBounds( index, timePeriod ) {
		const upper =
			index == 0
				? 0
				: index == 1
				? timePeriod === 1
					? 3600
					: timePeriod === 2
					? 14400
					: 86400
				: index == 2
				? timePeriod === 1
					? 7200
					: timePeriod === 2
					? 28800
					: 172800
				: index == 3
				? timePeriod === 1
					? 10800
					: timePeriod === 2
					? 43200
					: 259200
				: index == 4
				? timePeriod === 1
					? 14400
					: timePeriod === 2
					? 57600
					: 345600
				: timePeriod === 1
				? null
				: timePeriod === 2
				? null
				: null;

		const lower =
			index == 0
				? 0
				: index == 1
				? timePeriod === 1
					? 1
					: timePeriod === 2
					? 1
					: 1
				: index == 2
				? timePeriod === 1
					? 3600
					: timePeriod === 2
					? 14401
					: 86400
				: index == 3
				? timePeriod === 1
					? 7200
					: timePeriod === 2
					? 28801
					: 172801
				: index == 4
				? timePeriod === 1
					? 10800
					: timePeriod === 2
					? 43201
					: 259201
				: timePeriod === 1
				? 14400
				: timePeriod === 2
				? 57600
				: 345600;

		return { lower, upper };
	}

	getAnnotationData( data, timePeriod ) {
		const annotationArray = [];

		const index =
			timePeriod === 1
				? data <= 3600
					? 1
					: data <= 7200
					? 2
					: data <= 10800
					? 3
					: data <= 14400
					? 4
					: 5
				: timePeriod === 2
				? data <= 14400
					? 1
					: data <= 28800
					? 2
					: data <= 43200
					? 3
					: data <= 57600
					? 4
					: 5
				: data <= 86400
				? 1
				: data <= 172800
				? 2
				: data <= 259200
				? 3
				: data <= 345600
				? 4
				: 5;

		if ( data != 0 ) {
			const d1 = {
				x:
					index == 0
						? timePeriod === 1
							? '0'
							: timePeriod === 2
							? '0'
							: '0'
						: index == 1
						? timePeriod === 1
							? '0-60'
							: timePeriod === 2
							? '0-4'
							: '0-24'
						: index == 2
						? timePeriod === 1
							? '61-120'
							: timePeriod === 2
							? '4-8'
							: '24-48'
						: index == 3
						? timePeriod === 1
							? '121-180'
							: timePeriod === 2
							? '8-12'
							: '48-72'
						: index == 4
						? timePeriod === 1
							? '181-240'
							: timePeriod === 2
							? '12-16'
							: '72-96'
						: timePeriod === 1
						? '>240'
						: timePeriod === 2
						? '>16'
						: '>96',
				borderColor: '#775DD0',
				opacity: 1, // Set the opacity to 1 for a solid line
				strokeDashArray: 0, // Set the stroke dash array to 0 for a solid line
				borderWidth: 1,
				label: {
					style: {
						color: '#000',
					},
					text: 'Avg. Time ' + wisdmLDRConvertTime( data ),
				},
			};
			annotationArray.push( d1 );
		}

		return annotationArray;
	}

	plotBarCourseChart(
		learnerData,
		labels,
		totalLearner,
		average,
		timePeriod,
		tooltipTextLineOne = __( 'Time Spent', 'learndash-reports-pro' )
	) {
		const self = this;
		// Ensure arrays are defined and not null
		learnerData = learnerData || [];
		labels = labels || [];

		if ( wisdm_ld_reports_common_script_data.is_rtl ) {
			learnerData = learnerData.reverse();
			labels = labels.reverse();
		}
		const series = [
			{
				name: 'Learner',
				data: learnerData,
			},
		];

		const dataX = labels;
		const dataY = learnerData;
		const no_day = __(
			'Time spent (in ' +
				self.state.selectedTimePeriodOptions.label.toLowerCase() +
				')',
			'learndash-reports-pro'
		);
		const with_day = __( 'Time spent (in hours)', 'learndash-reports-pro' );
		const nameX =
			self.state.selectedTimePeriodOptions.label === 'Days'
				? with_day
				: no_day;
		const nameY = __( 'Learners', 'learndash-reports-pro' );

		const n_chart_options = {
			chart: {
				id: 'basic-bar',
				width: dataX.length * 75 < 645 ? '100%' : dataX.length * 75,
				height: 400,
				zoom: {
					enabled: false,
				},
				toolbar: {
					show: false,
					export: {
						csv: {
							filename: __(
								'Completion Time.csv',
								'learndash-reports-pro'
							),
							columnDelimiter: ',',
							headerCategory: nameX,
							headerValue: nameY,
						},
						svg: {
							filename: undefined,
						},
						png: {
							filename: undefined,
						},
					},
				},
				events: {
					mounted( chartContext, config ) {
						window.callStack.pop();
					},
					dataPointSelection: ( event, chartContext, config ) => {
						const upper = 0;
						const lower = 0;
						if ( wisdm_ld_reports_common_script_data.is_rtl ) {
							config.dataPointIndex = 5 - config.dataPointIndex;
						}
						const data = self.getUpperLowerBounds(
							config.dataPointIndex,
							self.state.selectedTimePeriodOptions.value
						);
						self.detailsModal( {
							dataPoint: dataX[ config.dataPointIndex ],
							...data,
						} );
					},
				},
			},
			colors: [ '#008AD8' ],
			dataLabels: {
				enabled: false,
				formatter( val ) {
					return val;
				},
				offsetY: -25,
				style: {
					fontSize: '12px',
					colors: [ '#008AD8' ],
				},
			},
			plotOptions: {
				bar: {
					borderRadius: 5,
					dataLabels: {
						enabled: true,
						position: 'top',
					},
				},
			},
			xaxis: {
				title: {
					text: nameX,
				},
				categories: dataX,
				labels: {
					hideOverlappingLabels: false,
					trim: true,
					formatter: ( value ) => {
						return value;
					},
					rotate: wisdm_ld_reports_common_script_data.is_rtl
						? 45
						: -45,
				},
				tickPlacement: 'on',
				min: 1,
			},
			yaxis: {
				max: Math.max( ...learnerData ) + 1,
				axisBorder: {
					show: ! wisdm_ld_reports_common_script_data.is_rtl,
				},
				title: {
					text: nameY,
					offsetX: wisdm_ld_reports_common_script_data.is_rtl
						? -35
						: 0,
				},
				labels: {
					formatter: ( value ) => {
						return parseInt( value );
					},
					align: wisdm_ld_reports_common_script_data.is_rtl
						? 'right'
						: 'left',
					offsetX: wisdm_ld_reports_common_script_data.is_rtl
						? 10
						: 0,
				},
				opposite: wisdm_ld_reports_common_script_data.is_rtl,
			},

			annotations: {
				xaxis: self.getAnnotationData( average, timePeriod ),
			},

			tooltip: {
				custom( { series, seriesIndex, dataPointIndex, w } ) {
					const learnerPercentage =
						( parseInt( series[ 0 ][ dataPointIndex ] ) /
							parseInt( totalLearner ) ) *
						100;
					const tooltip =
						"<div class='wisdm-bar-timespent-chart-tooltip'><div class='tooltip-body'><div class='wrld-learner'><div className='wrld-learner-container'>No. of learners</div></div><div className='wrld-average-label'><b>" +
						series[ 0 ][ dataPointIndex ] +
						' (' +
						learnerPercentage.toFixed( 2 ) +
						' %) </b></div></div></div>';
					return tooltip;
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
			graph_type: 'bar',
			series,
			options: n_chart_options,
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
		this.setState( { isLoaded: false } );
		if ( this.state.courses.length === 0 ) {
			const self = this;
			setTimeout( () => {
				self.setState( { isLoaded: true } );
			}, 1000 );

			return;
		}
		let requestUrl = '/rp/v1/time-spent-on-a-course/';
		const learner_selected = null;
		let course_selected = null;

		if ( this.state.active_tab === 1 ) {
			requestUrl =
				requestUrl +
				'?duration=' +
				this.state.duration.value +
				'&learner=' +
				( this.state.learner ? this.state.learner.value : '' ) +
				'&disable_cache=true' +
				'&timeperiod=' +
				this.state.selectedTimePeriodOptions.value;
		} else {
			course_selected = this.state.course.value;

			requestUrl =
				requestUrl +
				'?duration=' +
				this.state.duration.value +
				'&group=' +
				this.state.group.value +
				'&course=' +
				this.state.course.value +
				'&category=' +
				this.state.category.value +
				'&rtype=' +
				this.state.isToggleChecked +
				'&timeperiod=' +
				this.state.selectedTimePeriodOptions.value +
				'&disable_cache=true';
		}

		this.updateChart( requestUrl );
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
			if ( ! this.state.error && ! this.state.noData ) {
				graph = (
					<div
						className={
							this.state.active_tab == 1
								? 'time-spent-new'
								: 'time-spent'
						}
					>
						<Chart
							options={ this.state.options }
							series={ this.state.series }
							width={
								undefined == this.state.options.chart
									? 0
									: this.state.options.chart.width
							}
							height={
								undefined == this.state.options.chart
									? 0
									: this.state.options.chart.height
							}
							type={ this.state.graph_type }
						/>
					</div>
				);
			}

			//tooltip configuration
			let tooltip_text = '';
			let icon_enabled = false;
			let block_description = '';
			const time_tracking_enabled =
				wisdm_ld_reports_common_script_data.is_idle_tracking_enabled;

			if (
				undefined == global.reportTypeForTooltip ||
				global.reportTypeForTooltip == 'default-course-reports'
			) {
				block_description = sprintf(
					// Translators: %s: course label.
					__(
						'This report displays the average time spent on a %s by a learner.',
						'learndash-reports-pro'
					),
					wisdm_reports_get_ld_custom_lebel_if_avaiable(
						'course',
						'lower'
					)
				);
			} else {
				block_description = sprintf(
					// Translators: %s: courses label.
					__(
						'This report shows the time spent by a learner compared to others for enrolled %s.',
						'learndash-reports-pro'
					),
					wisdm_reports_get_ld_custom_lebel_if_avaiable(
						'courses',
						'lower'
					)
				);
			}

			if ( wisdm_ld_reports_common_script_data.is_admin_user ) {
				//If current user is admin
				if (
					wisdm_ld_reports_common_script_data.is_pro_version_active
				) {
					//need time tracking module setting
					if ( time_tracking_enabled == 'on' ) {
						//Checking weather time tracking is enabled or not
						tooltip_text = (
							<p>
								{ __(
									'Idle Time Configured , Activated on ',
									'learndash-reports-pro'
								) +
									wisdm_ld_reports_common_script_data.idle_tracking_active_from +
									'. ' }{ ' ' }
								<a
									href={
										wisdm_ld_reports_common_script_data.time_tacking_setting_url
									}
								>
									{ __(
										'View Idle Time Configuration Log.',
										'learndash-reports-pro'
									) }
								</a>{ ' ' }
							</p>
						);
						icon_enabled = true;
					} else {
						tooltip_text = (
							<div className="tooltip_container">
								<p>
									{ __(
										'"Idle Time" not configured. Configure the Settings from here. ',
										'learndash-reports-pro'
									) }
								</p>
								<a
									href={
										wisdm_ld_reports_common_script_data.time_tacking_setting_url
									}
									className="tooltip_button"
								>
									{ __(
										'Time Tracking Setting',
										'learndash-reports-pro'
									) }
								</a>
							</div>
						);
					}
				} else {
					tooltip_text = (
						<div className="tooltip_container">
							<p>
								{ __(
									'"Idle Time" not configured. This is available in the PRO version of the plugin.',
									'learndash-reports-pro'
								) }
							</p>
							<a
								href={
									wisdm_ld_reports_common_script_data.upgrade_link
								}
								target="_blank"
								className="tooltip_button"
								rel="noreferrer" // cspell:disable-line
							>
								{ __(
									'Upgrade To PRO',
									'learndash-reports-pro'
								) }
							</a>
						</div>
					);
				}
			} else {
				//For non-admin users group leader , instructor
				if (
					wisdm_ld_reports_common_script_data.is_pro_version_active
				) {
					//need time tracking module setting
					if ( time_tracking_enabled == 'on' ) {
						tooltip_text =
							__(
								'Idle Time Configured , Activated at ',
								'learndash-reports-pro'
							) +
							wisdm_ld_reports_common_script_data.idle_tracking_active_from;
						icon_enabled = true;
					} else {
						tooltip_text = __(
							'Idle Time Not Configured',
							'learndash-reports-pro'
						);
					}
				} else {
					console.log( 'Pro version is not active' );
				}
			}

			const migration_tooltip_text = (
				<p>
					{ __(
						"You need to run an data upgrade to see learner's earlier time spent information on this enhanced report. Click below to go to Time Tracking settings or navigate to WP Dashboard > ProPanel > Settings > Time Tracking settings.",
						'learndash-reports-pro'
					) }
					<br />
					<a
						href={
							wisdm_ld_reports_common_script_data.time_tacking_setting_url
						}
					>
						{ __( 'Upgrade data now', 'learndash-reports-pro' ) }
					</a>{ ' ' }
				</p>
			);

			body = (
				<div
					className={
						'wisdm-learndash-reports-chart-block ' + data_validation
					}
				>
					<div className="wisdm-learndash-reports-time-spent-on-a-course graph-card-container">
						<div className="chart-header time-spent-chart-header">
							<div className="chart-title">
								<span>{ this.state.chart_title }</span>

								<div className="tooltip">
									{ icon_enabled && (
										<img
											src={
												wisdm_ld_reports_common_script_data.plugin_asset_url +
												'/images/time_tracking_active.png'
											}
										></img>
									) }

									{ ! icon_enabled && (
										<img
											src={
												wisdm_ld_reports_common_script_data.plugin_asset_url +
												'/images/time-tracking-disabled.png'
											}
										></img>
									) }
									{ /* on remove tooltiptext class button inside tooltip will not be reachable */ }
									<span className="tooltiptext wdm-tooltip">
										{ block_description }
										<br /> <br />
										{ tooltip_text }
									</span>
								</div>
								{ ! this.state.isOldDataMigrated &&
									wisdm_ld_reports_common_script_data.is_admin_user && (
										<div className="tooltip">
											<img
												src={
													wisdm_ld_reports_common_script_data.plugin_asset_url +
													'/images/warning_icon_ts.svg'
												}
											></img>
											{ /* on remove tooltiptext class button inside tooltip will not be reachable */ }
											<span className="tooltiptext wdm-tooltip">
												{ migration_tooltip_text }
											</span>
										</div>
									) }

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
								is_category_enabled={
									this.state.is_category_enabled
								}
								is_group_enabled={ this.state.is_group_enabled }
							/>
							{ ! this.state.noData && (
								<div className="wrld-secondary-filter">
									<div
										className={
											this.state.active_tab == 0
												? 'wrld-secondary-filter-item left-item '
												: 'wrld-secondary-filter-item left-item ts-toggle-hide'
										}
									>
										<span className="wrld-time-spent-label">
											{ __(
												'Learners status',
												'learndash-reports-pro'
											) }
										</span>
										<div className="wrld-ts-switch-action">
											<span
												className={
													this.state.isToggleChecked
														? 'labeltext'
														: 'labeltext labeltext-bold'
												}
											>
												{ __(
													'Completed',
													'learndash-reports-pro'
												) }
											</span>
											<label
												className={
													this.state.isToggleChecked
														? 'toggle-switch'
														: 'toggle-switch labeltext-bold'
												}
											>
												<input
													type="checkbox"
													checked={
														this.state
															.isToggleChecked
													}
													onChange={
														this.handleToggleSwich // cspell:disable-line
													}
												/>
												<span className="slider"></span>
											</label>
											<span
												className={
													! this.state.isToggleChecked
														? 'labeltext'
														: 'labeltext labeltext-bold'
												}
											>
												{ __(
													'In Progress',
													'learndash-reports-pro'
												) }
											</span>
										</div>
									</div>
									<div className="wrld-secondary-filter-item right-item">
										<p>
											{ __(
												'Time Period',
												'learndash-reports-pro'
											) }{ ' ' }
										</p>
										<div className="dropdown-container">
											<Select
												options={
													this.state.timePeriodOptions
												}
												value={
													this.state
														.selectedTimePeriodOptions
												}
												onChange={
													this.handleTimePeriodChange
												}
												isSearchable={ false }
												className="wrld-timeperiod-dropdown"
											/>
										</div>
									</div>
								</div>
							) }

							<ChartSummarySection
								wrapper_class="chart-summary-time-spent"
								graph_summary={ this.state.graph_summary }
								error={ this.state.error }
							/>
							{ /*<SummarySection />*/ }
						</div>

						<div>
							{ graph }
							{ this.state.active_tab == 0 &&
								! this.state.noData && (
									<span className="note">
										<strong>
											{ __(
												'Note: ',
												'learndash-reports-pro'
											) }
										</strong>
										{ __(
											'Click on any item on the bar chart to see more details.',
											'learndash-reports-pro'
										) }
									</span>
								) }
						</div>
					</div>
					{ this.state.show_time_spent_detail_modal && (
						<Modal
							onRequestClose={ this.closeProgressDetailsModal }
							className={
								'learndash-propanel-modal time_spent_detail_modal'
							}
						>
							{ ! this.state.noData && (
								<TimeSpentTable
									type={
										1 == this.state.active_tab
											? 'learner'
											: 'course'
									}
									data_point={
										this.state.selected_data_point
									}
									table={ this.state.table_data }
									course={
										this.state.course
											? this.state.course.label
											: sprintf(
													// Translators: %s: courses label.
													__(
														'No %s found',
														'learndash-reports-pro'
													),
													wisdm_reports_get_ld_custom_lebel_if_avaiable(
														'courses',
														'lower'
													)
											  )
									}
									timeperiod={
										this.state.selectedTimePeriodOptions
											.value ?? 1
									}
									learner={
										this.state.learner
											? this.state.learner.label
											: __(
													'No Learners found',
													'learndash-reports-pro'
											  )
									}
								/>
							) }
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
						className="button-timespent-detail wrld-hidden"
						onClick={ this.openTimeSpentModal }
					></button>
				</div>
			);
		}
		return body;
	}
}

export default TimeSpent;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	// Your code to run since DOM is loaded and ready

	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-time-spent-on-a-course'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( TimeSpent ) );
	}
} );
