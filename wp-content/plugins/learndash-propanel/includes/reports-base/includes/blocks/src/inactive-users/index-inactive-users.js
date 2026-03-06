import './index.scss';
import WisdmFilters from '../commons/filters/index.js';
import DurationFilter from './component-duration-filter.js';
import LocalFilters from './component-local-filters.js';
import WisdmLoader from '../commons/loader/index.js';
import DummyReports from '../commons/dummy-reports/index.js';
import React, { Component } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
import { useTable, usePagination } from 'react-table';

var ld_api_settings = wisdm_ld_reports_common_script_data.ld_api_settings;

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

	if ( 'administrator' != user_type ) {
		if ( filtered_courses.length > 0 ) {
			filtered_courses.unshift( {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			} );
		}
	}
	return filtered_courses;
}

function loadInactiveUsers( event ) {
	const durationEvent = new CustomEvent( 'load_next_page_inactive_users', {
		detail: { value: event },
	} );
	document.dispatchEvent( durationEvent );
}

function Table( { columns, data } ) {
	// Use the state and functions returned from useTable to build your UI
	const {
		getTableProps,
		getTableBodyProps,
		headerGroups,
		prepareRow,
		page, // Instead of using 'rows', we'll use page,
		// which has only the rows for the active page

		// The rest of these things are super handy, too ;)
		canPreviousPage,
		canNextPage,
		pageOptions,
		pageCount,
		gotoPage,
		nextPage,
		previousPage,
		setPageSize,
		state: { pageIndex, pageSize },
	} = useTable(
		{ columns, data, initialState: { pageIndex: 0, pageSize: 5000 } },
		usePagination
	);

	//tooltip message configuration
	const tooltip_text = '';
	const icon_enabled = false;

	// Render the UI for your table
	return (
		<>
			<div className="course-reports-wrapper">
				<div className="inactive-user-table-wrap">
					<table className="course-list-table" { ...getTableProps() }>
						<thead>
							{ headerGroups.map( ( headerGroup ) => (
								<tr { ...headerGroup.getHeaderGroupProps() }>
									{ headerGroup.headers.map( ( column ) => (
										<th
											{ ...column.getHeaderProps() }
											className={ column.className }
										>
											{ column.render( 'Header' ) }
										</th>
									) ) }
								</tr>
							) ) }
						</thead>
						<tbody { ...getTableBodyProps() }>
							{ page.map( ( row, i ) => {
								prepareRow( row );
								return (
									<tr
										className="course-list-table-data-row"
										{ ...row.getRowProps() }
									>
										{ row.cells.map( ( cell ) => {
											return (
												<td
													className={
														cell.column.className
													}
													{ ...cell.getCellProps() }
												>
													{ cell.render( 'Cell' ) }
												</td>
											);
										} ) }
									</tr>
								);
							} ) }
						</tbody>
					</table>
				</div>
				{ /*
        Pagination can be built however you'd like.
        This is just a very basic UI implementation:
      */ }
				{ /*<button onClick={() => gotoPage(0)} disabled={!canPreviousPage}>
          {"<<"}
        </button>{" "}
        <button onClick={() => previousPage()} disabled={!canPreviousPage}>
          {"<"}
        </button>{" "}
        <span>
          {__('Page', 'learndash-reports-pro') + " "}
          <strong>
            {pageIndex + 1}  {' ' + __('Of', 'learndash-reports-pro') + ' ' }  {pageOptions.length}
          </strong>{" "}
        </span>
        <button onClick={() => nextPage()} disabled={!canNextPage}>
          {">"}
        </button>{" "}
        <button 78 6onClick={() => gotoPage(pageCount - 1)} disabled={!canNextPage}>
          {">>"}
        </button>{" "}*/ }
				{ /*<button onClick={() => gotoPage(pageCount - 1)} disabled={!canNextPage}>
              {">>"}
            </button>{" "}*/ }
				{ /*{ canNextPage &&
            <span className="load-more-ajax" onClick={loadInactiveUsers}>{__( 'View More', 'learndash-reports-pro' )}</span>
        }*/ }
			</div>
		</>
	);
}

class InactiveUsers extends Component {
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
		this.state = {
			isLoaded: false,
			error,
			reportTypeInUse: wisdm_ld_reports_common_script_data.report_type,
			duration: {
				value: '30 days',
				label: __( 'Last 30 days', 'learndash-reports-pro' ),
			},
			page: 1,
			group: { value: null, label: __( 'All', 'learndash-reports-pro' ) },
			course: {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			},
			courses: [],
			groups: [],
			chart_title: __( 'Inactive Users List', 'learndash-reports-pro' ),
			lock_icon: '',
			request_data: null,
			help_text: sprintf(
				// Translators: %s: course label.
				__(
					'This report displays the inactive users on the website. Note: The users who are created from the backend and enrolled in a %s, but never visited the platform will not be displayed in the table.',
					'learndash-reports-pro'
				),
				wisdm_reports_get_ld_custom_lebel_if_avaiable(
					'Course',
					'lower'
				)
			),
			course_report_type: null,
			tableHeaders: [],
			tableData: [],
			show_supporting_text: false,
		};

		if (
			false == wisdm_ld_reports_common_script_data.is_pro_version_active
		) {
			// cspell:disable-next-line
			this.upgdare_to_pro = 'wisdm-ld-reports-upgrade-to-pro-front';
			this.lock_icon = (
				<span
					title={ __(
						'Please upgrade the plugin to access this feature',
						'learndash-reports-pro'
					) }
					className="dashicons dashicons-lock ld-reports top-corner"
				></span>
			);
		}

		this.applyFilters = this.applyFilters.bind( this );
		this.handleReportTypeChange = this.handleReportTypeChange.bind( this );
		this.showDummyImages = this.showDummyImages.bind( this );
		this.updateLocalDuration = this.updateLocalDuration.bind( this );
		this.updateLocalGroup = this.updateLocalGroup.bind( this );
		this.updateLocalCourse = this.updateLocalCourse.bind( this );
		this.addMoreData = this.addMoreData.bind( this );
		this.defaultFiltersLoaded = this.defaultFiltersLoaded.bind( this );
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

	componentDidMount() {
		// this.updateChart('/rp/v1/inactive-users?duration=' + this.state.duration.value);
		this.getCourseListStateData(
			'/rp/v1/inactive-users?duration=' + this.state.duration.value
		);
		document.addEventListener(
			'wisdm-ld-reports-filters-applied',
			this.applyFilters
		);
		document.addEventListener(
			'wisdm-ld-reports-report-type-selected',
			this.handleReportTypeChange
		);
		document.addEventListener(
			'wisdm-ldrp-course-report-type-changed',
			this.showDummyImages
		);
		document.addEventListener(
			'local_duration_change',
			this.updateLocalDuration
		);
		document.addEventListener(
			'local_group_change',
			this.updateLocalGroup
		);
		document.addEventListener(
			'local_course_change',
			this.updateLocalCourse
		);
		document.addEventListener(
			'load_next_page_inactive_users',
			this.addMoreData
		);
		document.addEventListener(
			'wrld-default-filters-loaded',
			this.defaultFiltersLoaded
		);

		// Fallback: If report-filters block is not present, load data directly after a short delay.
		setTimeout( () => {
			if (
				this.state.courses.length === 0 &&
				this.state.groups.length === 0 &&
				! document.querySelector(
					'.wisdm-learndash-reports-report-filters'
				)
			) {
				// Initialize callStack if not already set (normally done by report-filters block).
				if ( ! window.callStack ) {
					window.callStack = [];
				}

				// Fetch the courses and groups data first, like report-filters block does.
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

	defaultFiltersLoaded() {
		let courses = [
			{ value: null, label: __( 'All', 'learndash-reports-pro' ) },
		];
		courses = courses.concat( wisdm_ld_reports_common_script_data.courses );
		const groups = wisdm_ld_reports_common_script_data.course_groups;
		if ( groups.length > 0 ) {
			groups.unshift( {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			} );
		}

		this.setState( {
			isLoaded: true,
			groups,
			courses,
		} );
		//Patch logic for react state update on browser refresh bug.
		const groupsLoadEvent = new CustomEvent(
			'wisdm-ld-reports-parent-groups-changed',
			{
				detail: { value: groups },
			}
		);
		document.dispatchEvent( groupsLoadEvent );
	}

	updateLocalDuration( event ) {
		this.setState( { duration: event.detail.value, page: 1 } );
		const request_url =
			'/rp/v1/inactive-users/?duration=' +
			event.detail.value.value +
			'&group=' +
			this.state.group.value +
			'&course=' +
			this.state.course.value +
			'&page=1';
		this.getCourseListStateData( request_url );
	}
	updateLocalGroup( event ) {
		if ( null == event.detail.value.value ) {
			this.setState( {
				group: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
				page: 1,
			} );
			this.updateSelectorsFor(
				'group',
				null,
				'/ldlms/v1/' + ld_api_settings[ 'sfwd-courses' ] + '?test=1'
			);
		} else {
			this.setState( { group: event.detail.value, page: 1 } );
			let courses_enrolled = 9999999999999;
			if ( event.detail.value.courses_enrolled.length > 0 ) {
				courses_enrolled = event.detail.value.courses_enrolled;
			}
			this.updateSelectorsFor(
				'group',
				event.detail.value,
				'/ldlms/v1/' +
					ld_api_settings[ 'sfwd-courses' ] +
					'?include=' +
					courses_enrolled
			);
		}
		const request_url =
			'/rp/v1/inactive-users/?duration=' +
			this.state.duration.value +
			'&group=' +
			event.detail.value.value +
			'&course=' +
			this.state.course.value +
			'&page=1';
		this.getCourseListStateData( request_url );
	}
	updateLocalCourse( event ) {
		if ( null == event.detail.value ) {
			this.setState( {
				course: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
				page: 1,
			} );
		} else {
			this.setState( { course: event.detail.value, page: 1 } );
		}
		const request_url =
			'/rp/v1/inactive-users/?duration=' +
			this.state.duration.value +
			'&group=' +
			this.state.group.value +
			'&course=' +
			event.detail.value.value +
			'&page=1';
		this.getCourseListStateData( request_url );
	}

	addMoreData( event ) {
		const next = this.state.page + 1;
		this.setState( { page: next } );
		const request_url =
			'/rp/v1/inactive-users/?duration=' +
			this.state.duration.value +
			'&group=' +
			this.state.group.value +
			'&course=' +
			this.state.course.value +
			'&page=' +
			next;
		this.getCourseListStateData( request_url, true );
	}

	componentDidUpdate() {
		jQuery(
			'.wisdm-learndash-reports-inactive-users .chart-title .dashicons, .wisdm-learndash-reports-inactive-users .chart-summary-revenue-figure .dashicons'
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
				'.wp-block-wisdm-learndash-reports-inactive-users',
				false
			);
		} else {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-inactive-users',
				true
			);
		}
	}

	showDummyImages( event ) {
		this.setState( { course_report_type: event.detail.report_type } );
		if ( 'learner-specific-course-reports' === event.detail.report_type ) {
			jQuery( '.wisdm-learndash-reports-inactive-users' ).parent().hide();
		} else {
			jQuery( '.wisdm-learndash-reports-inactive-users' ).parent().show();
		}
	}

	applyFilters( event ) {
		if (
			'learner-specific-course-reports' === this.state.course_report_type
		) {
			jQuery( '.wisdm-learndash-reports-inactive-users' ).parent().hide();
		}

		const group = event.detail.selected_groups;
		const course = event.detail.selected_courses;
		const request_url =
			'/rp/v1/inactive-users/?duration=' +
			this.state.duration.value +
			'&group=' +
			group +
			'&course=' +
			course +
			'&page=' +
			this.state.page;
		if ( undefined != course ) {
			this.setState( { show_supporting_text: true } );
		} else {
			this.setState( { show_supporting_text: false } );
		}
		this.getCourseListStateData( request_url );

		const course_label = this.state.courses.find(
			( o ) => o.value === course
		);
		const group_label = this.state.groups.find(
			( o ) => o.value === group
		);

		this.setState( { course: course_label, group: group_label } );
	}

	getCourseListStateData(
		request_url = '/rp/v1/inactive-users',
		is_paginated = false
	) {
		if ( ! is_paginated ) {
			this.setState( {
				isLoaded: false,
			} );
		}
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			request_url +=
				'&wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		wp.apiFetch( {
			path: request_url,
		} )
			.then( ( response ) => {
				// Handle error responses (when API returns { code: 'no-data', message: '...' }).
				if ( response.code === 'no-data' ) {
					this.setState( {
						error: {
							message: response.message,
						},
						tableData: [],
						tableHeaders: [],
						more: false,
						isLoaded: true,
					} );
					if ( response.data && response.data.requestData ) {
						this.setState( {
							request_data: response.data.requestData,
						} );
					}
					return;
				}

				let table = response.table;
				if ( undefined == response ) {
					table = [];
				}
				if ( is_paginated ) {
					table = this.state.tableData.concat( table );
				}
				this.setState( {
					isLoaded: true,
					error: null,
					isProVersion:
						wisdm_ld_reports_common_script_data.is_pro_version_active,
					tableHeaders: this.getTableHeadersByType( table ),
					tableData: table,
					request_data: response.requestData,
					more: response.more_data,
				} );
			} )
			.catch( ( error ) => {
				if ( error.data && error.data.requestData ) {
					this.setState( { request_data: error.data.requestData } );
				}
				this.setState( {
					error,
					isLoaded: true,
				} );
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

	updateSelectorsFor(
		element,
		selection,
		callback_path = '/wp/v2/categories/'
	) {
		switch ( element ) {
			case 'group':
				callback_path = callback_path + '&per_page=-1';
				if ( null == selection ) {
					// this.setState(
					//     {
					//     course:{value:null, label:__('All', 'learndash-reports-pro')},
					// });
					// wp.apiFetch({
					//     path: callback_path //Replace with the correct API
					//  }).then(response => {
					//     let courses = this.getCourseListFromJson(response);
					const courses = wisdm_ld_reports_common_script_data.courses;
					if ( false != courses && courses.length > 0 ) {
						this.setState( {
							courses,
							courses_disabled: false,
							loading_courses: false,
							course: {
								value: null,
								label: __( 'All', 'learndash-reports-pro' ),
							},
						} );
					} else {
						this.setState( {
							courses: [],
							course: {
								value: null,
								label: __( 'All', 'learndash-reports-pro' ),
							},
							loading_courses: false,
						} );
					}
					//Patch logic for react state update on browser refresh bug.
					const groupsLoadEvent = new CustomEvent(
						'wisdm-ld-reports-course-changed',
						{
							detail: { value: this.state.course },
						}
					);
					document.dispatchEvent( groupsLoadEvent );
					// });
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
								course: {
									value: null,
									label: __( 'All', 'learndash-reports-pro' ),
								},
								courses,
								courses_disabled: false,
								loading_courses: false,
							} );
						} else {
							this.setState( {
								course: {
									value: null,
									label: __( 'All', 'learndash-reports-pro' ),
								},
								courses: [],
								loading_courses: false,
							} );
						}
						//Patch logic for react state update on browser refresh bug.
						const groupsLoadEvent = new CustomEvent(
							'wisdm-ld-reports-course-changed',
							{
								detail: {
									value: {
										value: null,
										label: __(
											'All',
											'learndash-reports-pro'
										),
									},
								},
							}
						);
						document.dispatchEvent( groupsLoadEvent );
					} );
				}
				break;
			default:
				break;
		}
	}

	getTableHeadersByType( response ) {
		const headers = [];
		const table_header_names = {
			id: __( 'ID', 'learndash-reports-pro' ),
			name: __( 'Name', 'learndash-reports-pro' ),
			email: __( 'Email ID', 'learndash-reports-pro' ),
			status: __( 'Status', 'learndash-reports-pro' ),
			steps: __( 'Steps Completed', 'learndash-reports-pro' ),
			date: __( 'Completion Date', 'learndash-reports-pro' ),
			time: __( 'Time spent', 'learndash-reports-pro' ),
			total_spent_time: __( 'Total Time Spent', 'learndash-reports-pro' ),
			category:
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' ) +
				' ' +
				__( 'Category', 'learndash-reports-pro' ),
			// cspell:disable-next-line
			course: wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' ),
			groups: wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Groups' ),
			user_name: __( 'Learner', 'learndash-reports-pro' ),
			instructors: __( 'Instructor', 'learndash-reports-pro' ),
			students: __( 'No. Of Students', 'learndash-reports-pro' ),
			start_date: __( 'Start Date', 'learndash-reports-pro' ),
			started: __( 'Enrolled On', 'learndash-reports-pro' ),
			end_date: __( 'End Date', 'learndash-reports-pro' ),
			completed: __( 'Completion Date', 'learndash-reports-pro' ),
			completion_rate: __( 'Completion %', 'learndash-reports-pro' ),
			completion_rate2: __( '% Completion', 'learndash-reports-pro' ),
			completed_users: __(
				'Completed Learners',
				'learndash-reports-pro'
			),
			in_progress: __( 'In Progress', 'learndash-reports-pro' ),
			not_started: __( 'Not Started', 'learndash-reports-pro' ),
			lesson: wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Lesson' ),
			course_progress: __( 'Completion %', 'learndash-reports-pro' ),
			quizzes:
				__( 'No. Of', 'learndash-reports-pro' ) +
				' ' +
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes' ), // cspell:disable-line
			quiz_count:
				__( 'No. Of', 'learndash-reports-pro' ) +
				' ' +
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes' ), // cspell:disable-line
			quiz_title:
				//  cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) +
				' ' +
				__( 'Title', 'learndash-reports-pro' ),
			total_attempts: __( 'Total Attempts', 'learndash-reports-pro' ),
			attempts:
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) +
				' ' +
				__( 'Attempts', 'learndash-reports-pro' ),
			pass_rate:
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) +
				' ' +
				__( 'Pass %', 'learndash-reports-pro' ),
			avg_score:
				__( 'Avg', 'learndash-reports-pro' ) +
				' ' +
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) +
				' ' +
				__( 'Score', 'learndash-reports-pro' ),
			pass_count: sprintf(
				// Translators: %s: Quizzes label.
				__( 'No. Of %s Pass', 'learndash-reports-pro' ),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes' )
			),
			fail_count: sprintf(
				// Translators: %s: Quizzes label.
				__( 'No. Of %s Fail', 'learndash-reports-pro' ),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes' )
			),
			time_spent: __( 'Time Spent', 'learndash-reports-pro' ),
			total_time_spent: __( 'Total Time Spent', 'learndash-reports-pro' ),
			avg_total_time_spent: __(
				'Avg. Total Time Spent',
				'learndash-reports-pro'
			),
			course_completion_time: __(
				'Completion Time',
				'learndash-reports-pro'
			),
			avg_time_spent: __(
				'Avg. Completion Time',
				'learndash-reports-pro'
			),
			quiz_attendant_count: sprintf(
				// Translators: %s: Quiz label.
				__( 'No. Of Students Completed %s', 'learndash-reports-pro' ),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' )
			),
			last_access: __( 'Last Activity', 'learndash-reports-pro' ),
			topic_title:
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Topic' ) +
				' ' +
				__( ' Title', 'learndash-reports-pro' ),
			topic_completion_count:
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Topic' ) +
				' ' +
				__( 'Completed By Students', 'learndash-reports-pro' ),
			quiz_time:
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) +
				' ' +
				__( 'Time', 'learndash-reports-pro' ),
			quiz_attempts:
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) +
				' ' +
				__( 'Attempts', 'learndash-reports-pro' ),
		};

		if ( null != response && response.length > 0 ) {
			const response_headers = Object.keys( response[ 0 ] );
			if ( response_headers.length > 0 ) {
				for ( let i = 0; i < response_headers.length; i++ ) {
					const name = response_headers[ i ];
					if ( undefined == table_header_names[ name ] ) {
						headers.push( {
							Header: name,
							accessor: name,
							className: 'table-' + name,
							toolTip:
								name == 'total_time_spent' ||
								name == 'time_spent' ||
								name == 'time' ||
								name == 'avg_time_spent' ||
								name == 'course_completion_time' ||
								name == 'avg_total_time_spent'
									? true
									: false,
						} );
					} else {
						headers.push( {
							Header: table_header_names[ name ],
							accessor: name,
							className: 'table-' + name,
							toolTip:
								name == 'total_time_spent' ||
								name == 'time_spent' ||
								name == 'time' ||
								name == 'avg_time_spent' ||
								name == 'course_completion_time' ||
								name == 'avg_total_time_spent'
									? true
									: false,
						} );
					}
				}
			}
		}
		return headers;
	}

	updateChart( requestUrl ) {
		this.setState( { isLoaded: false, error: null, request_data: null } );
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			requestUrl +=
				'&wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		wp.apiFetch( {
			path: requestUrl, //Replace with the correct API
		} )
			.then( ( response ) => {
				if ( response.requestData ) {
					this.setState( { request_data: response.requestData } );
				}
			} )
			.catch( ( error ) => {
				if ( error.data && error.data.requestData ) {
					this.setState( { request_data: error.data.requestData } );
				}
				this.setState( {
					error,
					isLoaded: true,
				} );
			} );
	}

	render() {
		let body = <div></div>;
		if (
			this.state.course_report_type == 'learner-specific-course-reports'
		) {
			return '';
		}
		if ( ! wisdm_ld_reports_common_script_data.is_pro_version_active ) {
			body = (
				<DummyReports
					image_path="iu.jpg"
					url="https://go.learndash.com/ppaddon"
				></DummyReports>
			);
			// body = '';
			return body;
		}
		if (
			'' != this.state.reportTypeInUse &&
			'default-ld-reports' != this.state.reportTypeInUse
		) {
			body = '';
		} else if ( ! this.state.isLoaded ) {
			// yet loading
			body = <WisdmLoader text={ this.state.show_supporting_text } />;
		} else if ( this.state.error ) {
			body = (
				<div className={ 'wisdm-learndash-reports-chart-block ' }>
					<div className="wisdm-learndash-reports-inactive-users graph-card-container">
						<div className="chart-header inactive-users-chart-header">
							<div className="chart-title">
								<div>
									<span>{ this.state.chart_title }</span>
									<span
										className="dashicons dashicons-info-outline widm-ld-reports-info"
										data-title={ this.state.help_text }
									></span>
								</div>
								<DurationFilter
									pro_upgrade_option={ this.upgdare_to_pro } // cspell:disable-line
									wrapper_class="chart-summary-inactive-users"
									duration={ this.state.duration }
								/>
							</div>
							<LocalFilters
								group={ this.state.group }
								course={ this.state.course }
								groups={ this.state.groups }
								courses={ this.state.courses }
							/>
						</div>
						<div>{ this.state.error.message }</div>
					</div>
				</div>
			);
		} else {
			body = (
				<div className={ 'wisdm-learndash-reports-chart-block ' }>
					<div className="wisdm-learndash-reports-inactive-users graph-card-container">
						<div className="chart-header inactive-users-chart-header">
							<div className="chart-title">
								<div>
									<span>{ this.state.chart_title }</span>
									<span
										className="dashicons dashicons-info-outline widm-ld-reports-info"
										data-title={ this.state.help_text }
									></span>
								</div>
								<DurationFilter
									pro_upgrade_option={ this.upgdare_to_pro } // cspell:disable-line
									wrapper_class="chart-summary-inactive-users"
									duration={ this.state.duration }
								/>
							</div>
							<LocalFilters
								group={ this.state.group }
								course={ this.state.course }
								groups={ this.state.groups }
								courses={ this.state.courses }
							/>
						</div>
						<div>
							{ this.state.tableHeaders.length > 0 ? (
								<Table
									columns={ this.state.tableHeaders }
									data={ this.state.tableData }
								/>
							) : (
								<div className="error-message">
									<span>
										{ __(
											'No Data Found',
											'learndash-reports-pro'
										) }
									</span>
								</div>
							) }
							{ 'yes' == this.state.more ? (
								<span
									className="load-more-ajax"
									onClick={ loadInactiveUsers }
								>
									{ __(
										'View More',
										'learndash-reports-pro'
									) }
								</span>
							) : (
								<span></span>
							) }
						</div>
					</div>
				</div>
			);
		}
		return body;
	}
}

export default InactiveUsers;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-inactive-users front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( InactiveUsers ) );
	}
} );
