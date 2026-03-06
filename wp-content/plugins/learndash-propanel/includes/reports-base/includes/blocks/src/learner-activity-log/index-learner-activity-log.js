import './index.scss';
import { AsyncSelect } from '../commons/emotion-styles-provider/select.js';
import DurationFilter from './component-duration-filter.js';
import WisdmLoader from '../commons/loader/index.js';
import DummyReports from '../commons/dummy-reports/index.js';
import React, { Component } from 'react';
import { __ } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';

const ld_api_settings = wisdm_ld_reports_common_script_data.ld_api_settings;

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

function loadLearnerActivityLog( event ) {
	const durationEvent = new CustomEvent(
		'load_next_page_learner_activity_log',
		{
			detail: { value: event },
		}
	);
	document.dispatchEvent( durationEvent );
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

function getFormattedTime( timestamp ) {
	const ts = new Date( timestamp * 1000 );
	let hours = ts.getHours();
	let minutes = ts.getMinutes();
	let seconds = ts.getSeconds();
	if ( hours < 10 ) {
		hours = '0' + hours;
	}
	if ( minutes < 10 ) {
		minutes = '0' + minutes;
	}
	if ( seconds < 10 ) {
		seconds = '0' + seconds;
	}
	return hours + ':' + minutes + ':' + seconds;
}

class LearnerActivityLog extends Component {
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
			learner: null,
			chart_title: __( 'Learner Activity Log', 'learndash-reports-pro' ),
			lock_icon: '',
			request_data: null,
			help_text:
				undefined == global.reportTypeForTooltip ||
				global.reportTypeForTooltip == 'default-course-reports'
					? __(
							'This report displays the learner activity log on the website.',
							'learndash-reports-pro'
					  )
					: __(
							'This report logs the activity of a particular learner.',
							'learndash-reports-pro'
					  ),
			course_report_type: null,
			show_triggers: true,
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
		this.addMoreData = this.addMoreData.bind( this );
		this.showUserSpecificResults =
			this.showUserSpecificResults.bind( this );
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
		this.getCourseListStateData(
			'/rp/v1/learner-activity-log?duration=' +
				this.state.duration.value +
				'&page=' +
				this.state.page
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
			'local_duration_log_change',
			this.updateLocalDuration
		);
		document.addEventListener(
			'load_next_page_learner_activity_log',
			this.addMoreData
		);
		document.addEventListener(
			'wrld-default-filters-loaded',
			this.defaultFiltersLoaded
		);
	}

	defaultFiltersLoaded() {
		this.getDefaultOptions();
	}

	updateLocalDuration( event ) {
		this.setState( { duration: event.detail.value, page: 1 } );
		let learner = null;
		if ( null !== this.state.learner ) {
			learner = this.state.learner.value;
		}
		const request_url =
			'/rp/v1/learner-activity-log/?duration=' +
			event.detail.value.value +
			'&learner=' +
			learner +
			'&page=' +
			this.state.page;
		this.getCourseListStateData( request_url );
	}

	addMoreData( event ) {
		const next = this.state.page + 1;
		this.setState( { page: next } );
		let learner = null;
		if ( null !== this.state.learner ) {
			learner = this.state.learner.value;
		}
		const request_url =
			'/rp/v1/learner-activity-log/?duration=' +
			this.state.duration.value +
			'&learner=' +
			learner +
			'&page=' +
			next;
		this.getCourseListStateData( request_url, true );
		// this.setState({page:this.state.page + 1});
	}

	componentDidUpdate() {
		jQuery(
			'.wisdm-learndash-reports-learner-activity-log .chart-title .dashicons, .wisdm-learndash-reports-learner-activity-log .chart-summary-revenue-figure .dashicons'
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
				'.wp-block-wisdm-learndash-reports-learner-activity-log',
				false
			);
		} else {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-learner-activity-log',
				true
			);
		}
	}

	showDummyImages( event ) {
		this.setState( { course_report_type: event.detail.report_type } );
	}

	applyFilters( event ) {
		const learner = event.detail.selected_learners;

		if ( ! learner ) {
			return;
		}

		const course = event.detail.selected_courses;
		if ( undefined != course ) {
			this.setState( { show_supporting_text: true } );
		} else {
			this.setState( { show_supporting_text: false } );
		}
		const request_url =
			'/rp/v1/learner-activity-log/?duration=' +
			this.state.duration.value +
			'&learner=' +
			learner +
			'&page=' +
			this.state.page;
		this.getCourseListStateData( request_url );
		wp.apiFetch( {
			path: '/wp/v2/users/' + learner,
		} ).then( ( response ) => {
			this.setState( {
				reportTypeInUse: 'default-ld-reports',
				learner: { value: learner, label: response.name },
				page: 1,
			} );
		} );
		// let learner_label = this.state.learner.find(o => o.value === learner);
		//Time spent on a course chart should not display for lesson/topic
		// this.updateChart(request_url);
		wisdm_reports_change_block_visibility(
			'.wp-block-wisdm-learndash-reports-learner-activity-log',
			true
		);
	}

	handleLearnerChange = ( selectedLearner ) => {
		if ( null == selectedLearner ) {
			this.setState( { learner: null, page: 1 } );
			// this.updateSelectorsFor('learner', null);
		} else {
			this.setState( { learner: selectedLearner, page: 1 } );
		}

		let requestURL =
			'/rp/v1/learner-activity-log/?duration=' +
			this.state.duration.value +
			'&page=1';

		if ( selectedLearner?.value ) {
			requestURL += '&learner=' + selectedLearner.value;
		}

		this.getCourseListStateData( requestURL );
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
						response.posts.forEach( ( user ) => {
							requestResults.push( {
								value: user.ID,
								label: user.name,
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

	objectMerger( a, b ) {
		for ( const i in a ) {
			if ( i in b ) {
				a[ i ] = [].concat( a[ i ], b[ i ] );
			}
		}
		for ( const j in b ) {
			if ( ! ( j in a ) ) {
				a[ j ] = b[ j ];
			}
		}
		return a;
	}

	getCourseListStateData(
		request_url = '/rp/v1/learner-activity-log',
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
				let table = response.table;
				if ( undefined == response ) {
					table = [];
				}
				if ( is_paginated ) {
					// table = {...table, ...this.state.tableData};
					table = this.objectMerger( this.state.tableData, table );
					// table = this.state.tableData.concat(table);
				}
				this.setState( {
					isLoaded: true,
					error: null,
					isProVersion:
						wisdm_ld_reports_common_script_data.is_pro_version_active,
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
					graph_summary: [],
					series: [],
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

	showUserSpecificResults( event, item ) {
		let learner = null;
		if ( item == null ) {
			this.setState( {
				reportTypeInUse: 'default-ld-reports',
				learner: null,
				page: 1,
				show_triggers: ! this.state.show_triggers,
			} );
		} else {
			learner = item.user_id;
			this.setState( {
				reportTypeInUse: 'default-ld-reports',
				learner: {
					value: learner,
					label: item.user_name,
				},
				page: 1,
				show_triggers: ! this.state.show_triggers,
			} );
		}
		const request_url =
			'/rp/v1/learner-activity-log/?duration=' +
			this.state.duration.value +
			'&learner=' +
			learner +
			'&page=1';
		this.getCourseListStateData( request_url );
	}
	getDefaultOptions = () => {
		// perform a request
		const requestResults = [];

		if ( 'group_leader' == wisdmLdReportsGetUserType() ) {
			const groupUsers = wrldGetGroupAdminUsers();
			groupUsers.forEach( ( user ) => {
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
	render() {
		let body = <div></div>;
		if ( ! wisdm_ld_reports_common_script_data.is_pro_version_active ) {
			body = (
				<DummyReports
					image_path="lal.png"
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
			let proClass = 'select-control';
			if (
				true !=
				wisdm_ld_reports_common_script_data.is_pro_version_active
			) {
				proClass = 'ldr-pro';
			}
			body = (
				<div className={ 'wisdm-learndash-reports-chart-block ' }>
					<div className="wisdm-learndash-reports-learner-activity-log graph-card-container">
						<div className="chart-header learner-activity-log-chart-header">
							<div className="chart-title">
								<span>{ this.state.chart_title }</span>
								<span
									className="dashicons dashicons-info-outline widm-ld-reports-info"
									data-title={ this.state.help_text }
								></span>
							</div>
							<div className="local_wrapper">
								<div className="selector lr-learner">
									<div className="selector-label">
										{ __(
											'Learners',
											'learndash-reports-pro'
										) }
										{ this.state.lock_icon }
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
											placeholder={ __(
												'Search',
												'learndash-reports-pro'
											) }
											value={ this.state.learner }
											loadOptions={
												this.handleLearnerSearch
											}
											onChange={
												this.handleLearnerChange
											}
											isClearable="true"
										/>
									</div>
								</div>
								<DurationFilter
									pro_upgrade_option={ this.upgdare_to_pro } // cspell:disable-line
									wrapper_class="chart-summary-learner-activity-log"
									duration={ this.state.duration }
								/>
							</div>
						</div>
						<div>{ this.state.error.message }</div>
					</div>
				</div>
			);
		} else {
			let proClass = 'select-control';
			if (
				true !=
				wisdm_ld_reports_common_script_data.is_pro_version_active
			) {
				proClass = 'ldr-pro';
			}
			const list = (
				<div className="list-wrapper">
					<div className="left-wrapper">
						{ Object.keys( this.state.tableData ).map(
							( key, index ) => (
								<div key={ index }>
									<div className="left-side">
										<span>{ key }</span>
									</div>
									<div className="right-side">
										{ this.state.tableData[ key ].map(
											( item, i ) => (
												<div key={ i }>
													<div className="user-name">
														<span>
															{ item.user_name }
														</span>
														{ this.state
															.show_triggers && (
															<span
																className="right-trigger"
																onClick={ (
																	e
																) =>
																	this.showUserSpecificResults(
																		e,
																		item
																	)
																}
															>
																<img
																	src={
																		wisdm_ld_reports_common_script_data.plugin_asset_url +
																		'/images/right.png'
																	}
																/>
															</span>
														) }
													</div>
													<div className="item-title">
														{ ! this.state
															.show_triggers && (
															<span className="time">
																{ getFormattedTime(
																	item.latest
																) }
															</span>
														) }
														<h2>
															{ item.post_id }
														</h2>
														<span>
															{ item.course_id }
														</span>
													</div>
													<div className="activity-status">
														{ item.status }
													</div>
												</div>
											)
										) }
										{ ! this.state.show_triggers && (
											<span
												className="right-trigger reset-trigger"
												onClick={ ( e ) =>
													this.showUserSpecificResults(
														e
													)
												}
											>
												<img
													src={
														wisdm_ld_reports_common_script_data.plugin_asset_url +
														'/images/down-arrow.png'
													}
												/>
											</span>
										) }
									</div>
								</div>
							)
						) }
					</div>
					<div className="pagination"></div>
				</div>
			);
			body = (
				<div className={ 'wisdm-learndash-reports-chart-block ' }>
					<div className="wisdm-learndash-reports-learner-activity-log graph-card-container">
						<div className="chart-header learner-activity-log-chart-header">
							<div className="chart-title">
								<span>{ this.state.chart_title }</span>
								<span
									className="dashicons dashicons-info-outline widm-ld-reports-info"
									data-title={ this.state.help_text }
								></span>
							</div>
							<div className="local_wrapper">
								<div className="selector lr-learner">
									<div className="selector-label">
										{ __(
											'Learners',
											'learndash-reports-pro'
										) }
										{ this.state.lock_icon }
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
											placeholder={ __(
												'Search',
												'learndash-reports-pro'
											) }
											value={ this.state.learner }
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
								<DurationFilter
									pro_upgrade_option={ this.upgdare_to_pro } // cspell:disable-line
									wrapper_class="chart-summary-learner-activity-log"
									duration={ this.state.duration }
								/>
							</div>
						</div>
						<div>
							{ Object.keys( this.state.tableData ).length > 0 ? (
								list
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
									onClick={ loadLearnerActivityLog }
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

export default LearnerActivityLog;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-learner-activity-log front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( LearnerActivityLog ) );
	}
} );
