// cspell:ignore globalfilters

import './index.scss';
import { createHooks } from '@wordpress/hooks';
import React, { Component } from 'react';
import {__, sprintf} from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
import { useTable, usePagination } from 'react-table';
import WisdmLoader from '../commons/loader/index.js';
import DummyReports from '../commons/dummy-reports/index.js';
import WisdmFilters from '../commons/filters/index.js';
import { CSVLink } from 'react-csv';
import moment from 'moment';

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
		{ columns, data, initialState: { pageIndex: 0 } },
		usePagination
	);

	//tooltip message configuration
	let tooltip_text = '';
	let icon_enabled = false;
	const time_tracking_enabled =
		wisdm_ld_reports_common_script_data.is_idle_tracking_enabled;

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
								'View Idle Time Configuration Log',
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
								'"Idle Time" not configured. Configure the Settings from here ',
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
							'"Idle Time" not configured. This is available in the PRO version of the plugin',
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
						{ __( 'Upgrade To PRO', 'learndash-reports-pro' ) }
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
			if ( time_tracking_enabled ) {
				tooltip_text =
					__(
						'Idle Time Configured , Activated on ',
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

	// Render the UI for your table
	return (
		<>
			<div className="course-reports-wrapper">
				<div className="course-table-wrap">
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
											{ column.toolTip && (
												<div className="cl_tooltip">
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
													{ tooltip_text && (
														<span className="cl_tooltiptext wdm-tooltip">
															{ tooltip_text }{ ' ' }
															<div className="hover_helper"></div>
														</span>
													) }
												</div>
											) }
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
				<div className="table-pagination">
					<button
						onClick={ () => gotoPage( 0 ) }
						disabled={ ! canPreviousPage }
					>
						{ '<<' }
					</button>{ ' ' }
					<button
						onClick={ () => previousPage() }
						disabled={ ! canPreviousPage }
					>
						{ '<' }
					</button>{ ' ' }
					<span>
						{ __( 'Page', 'learndash-reports-pro' ) + ' ' }
						<strong>
							{ pageIndex + 1 }{ ' ' }
							{ ' ' + __( 'Of', 'learndash-reports-pro' ) + ' ' }{ ' ' }
							{ pageOptions.length }
						</strong>{ ' ' }
					</span>
					<button
						onClick={ () => nextPage() }
						disabled={ ! canNextPage }
					>
						{ '>' }
					</button>{ ' ' }
					<button
						onClick={ () => gotoPage( pageCount - 1 ) }
						disabled={ ! canNextPage }
					>
						{ '>>' }
					</button>{ ' ' }
				</div>
			</div>
		</>
	);
}

class CourseList extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			isLoaded: false,
			isProVersion: false,
			start_date: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			).unix(),
			end_date: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			).unix(),
			reportTypeInUse:
				wisdm_ld_reports_common_script_data.report_type,
			error: null,
			request_data: null,
			course_report_type: null,
			show_supporting_text: false,
		};

		this.applyFilters = this.applyFilters.bind( this );
		this.handleReportTypeChange = this.handleReportTypeChange.bind( this );
		this.getExcelFile = this.getExcelFile.bind( this );
		this.durationUpdated = this.durationUpdated.bind( this );
		this.showDummyImages = this.showDummyImages.bind( this );
	}

	componentDidMount() {
		const start_date = this.state.start_date;
		const end_date = this.state.end_date;
		const request_url =
			'/rp/v1/course-list-info/?start_date=' +
			start_date +
			'&end_date=' +
			end_date;
		this.getCourseListStateData( request_url );
		document.addEventListener(
			'wisdm-ld-reports-filters-applied',
			this.applyFilters
		);
		document.addEventListener(
			'wisdm-ld-reports-report-type-selected',
			this.handleReportTypeChange
		);
		document.addEventListener( 'duration_updated', this.durationUpdated );
		document.addEventListener(
			'wisdm-ldrp-course-report-type-changed',
			this.showDummyImages
		);
	}

	durationUpdated( event ) {
		this.setState(
			{
				isLoaded: false,
				start_date: event.detail.startDate,
				end_date: event.detail.endDate,
			},
			() => {
				let requestUrl = '/rp/v1/course-list-info/';

				if ( event.type === 'duration_updated' ) {
					requestUrl =
						'/rp/v1/course-list-info/?start_date=' +
						event.detail.startDate +
						'&&end_date=' +
						event.detail.endDate;
				}

				if ( window.globalfilters !== undefined ) {
					const category =
						window.globalfilters.detail.selected_categories;
					const group = window.globalfilters.detail.selected_groups;
					const course = window.globalfilters.detail.selected_courses;
					const lesson = window.globalfilters.detail.selected_lessons;
					const topic = window.globalfilters.detail.selected_topics;
					const learner =
						window.globalfilters.detail.selected_learners;
					requestUrl =
						requestUrl +
						'&category=' +
						category +
						'&group=' +
						group +
						'&course=' +
						course +
						'&lesson=' +
						lesson +
						'&topic=' +
						topic +
						'&learner=' +
						learner;
				}

				this.getCourseListStateData( requestUrl );
			}
		);
	}

	handleReportTypeChange( event ) {
		this.setState( { reportTypeInUse: event.detail.active_reports_tab } );
		if ( 'quiz-reports' == event.detail.active_reports_tab ) {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-list',
				false
			);
		} else {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-list',
				true
			);
		}
	}

	showDummyImages( event ) {
		this.setState( { course_report_type: event.detail.report_type } );
	}

	applyFilters( event ) {
		const start_date = this.state.start_date;
		const end_date = this.state.end_date;
		const category = event.detail.selected_categories;
		const group = event.detail.selected_groups;
		const course = event.detail.selected_courses;
		const lesson = event.detail.selected_lessons;
		const topic = event.detail.selected_topics;
		const learner = event.detail.selected_learners;
		const request_url =
			'/rp/v1/course-list-info/?start_date=' +
			start_date +
			'&end_date=' +
			end_date +
			'&category=' +
			category +
			'&group=' +
			group +
			'&course=' +
			course +
			'&lesson=' +
			lesson +
			'&topic=' +
			topic +
			'&learner=' +
			learner;
		if ( undefined != course ) {
			this.setState( { show_supporting_text: true } );
		} else {
			this.setState( { show_supporting_text: false } );
		}
		this.getCourseListStateData( request_url );
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
			course: wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' ), // cspell:disable-line
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
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) + // cspell:disable-line
				' ' +
				__( 'Title', 'learndash-reports-pro' ),
			total_attempts: __( 'Total Attempts', 'learndash-reports-pro' ),
			attempts:
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) + // cspell:disable-line
				' ' +
				__( 'Attempts', 'learndash-reports-pro' ),
			pass_rate:
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) + // cspell:disable-line
				' ' +
				__( 'Pass %', 'learndash-reports-pro' ),
			avg_score:
				__( 'Avg', 'learndash-reports-pro' ) +
				' ' +
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) + // cspell:disable-line
				' ' +
				__( 'Score', 'learndash-reports-pro' ),
			pass_count: sprintf(
				// Translators: Quizzes label.
				__( 'No. Of %s Pass', 'learndash-reports-pro' ),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes' )
			),
			fail_count: sprintf(
				// Translators: Quizzes label.
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
				__(
					'No. Of Students Completed %s',
					'learndash-reports-pro'
				),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' )
			),
			topic_title:
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Topic' ) + // cspell:disable-line
				' ' +
				__( ' Title', 'learndash-reports-pro' ),
			topic_completion_count:
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Topic' ) + // cspell:disable-line
				' ' +
				__( 'Completed By Students', 'learndash-reports-pro' ),
			quiz_time:
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) + // cspell:disable-line
				' ' +
				__( 'Time', 'learndash-reports-pro' ),
			quiz_attempts:
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) + // cspell:disable-line
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

	getCourseListStateData( request_url = '/rp/v1/course-list-info' ) {
		this.setState( {
			isLoaded: false,
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
					window.callStack.push( request_url );
					if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
						request_url +=
							'&wpml_lang=' +
							wisdm_ld_reports_common_script_data.wpml_lang;
					}
					wp.apiFetch( {
						path: request_url,
					} )
						.then( ( response ) => {
							let table = response.table;
							if ( undefined == response ) {
								table = [];
							}
							if ( response.updated_on ) {
								self.setState( {
									updated_on: response.updated_on,
								} );
							}
							self.setState( {
								isLoaded: true,
								error: null,
								isProVersion:
									wisdm_ld_reports_common_script_data.is_pro_version_active,
								tableHeaders: self.getTableHeadersByType(
									response.table
								),
								tableData: response.table,
								request_data: response.requestData,
							} );
							window.callStack.pop();
						} )
						.catch( ( error ) => {
							if ( error.data && error.data.requestData ) {
								self.setState( {
									request_data: error.data.requestData,
								} );
							}
							self.setState( {
								error,
								graph_summary: [],
								series: [],
								isLoaded: true,
							} );
							window.callStack.pop();
						} );
				}
			}, 500 );
		};
		checkIfEmpty();
	}

	getExcelFile() {
		jQuery.ajax( {
			type: 'POST',
			url: wisdm_ld_reports_common_script_data.ajaxurl,
			dataType: 'JSON',
			data: {
				action: 'export_course_statistics',
				report_nonce:
					wisdm_ld_reports_common_script_data.report_nonce,
				tableHeaders: this.state.tableHeaders,
				tableData: JSON.stringify( this.state.tableData ),
			},
			error( eventData ) {
				if ( 'timeout' === eventData.status ) {
					alert( 'Request Timed Out' );
				}
			},
			success( response ) {
				if ( 'success' == response.status ) {
					window.open( response.data, '_blank' );
					//process download
				} else {
					alert( response.data );
				}
			},
			timeout: 60000, // sets timeout to 60 seconds
		} );
	}

	refreshUpdateTime() {
		this.setState( { isLoaded: false } );
		let requestUrl = '/rp/v1/course-list-info/';
		if ( window.globalfilters != undefined ) {
			const category = window.globalfilters.detail.selected_categories;
			const group = window.globalfilters.detail.selected_groups;
			const course = window.globalfilters.detail.selected_courses;
			const lesson = window.globalfilters.detail.selected_lessons;
			const topic = window.globalfilters.detail.selected_topics;
			const learner = window.globalfilters.detail.selected_learners;
			const start_date = this.state.start_date;
			const end_date = this.state.end_date;
			requestUrl =
				requestUrl +
				'?category=' +
				category +
				'&group=' +
				group +
				'&course=' +
				course +
				'&lesson=' +
				lesson +
				'&topic=' +
				topic +
				'&learner=' +
				learner +
				'&disable_cache=true' +
				'&start_date=' +
				start_date +
				'&end_date=' +
				end_date;
		}
		this.getCourseListStateData( requestUrl );
	}

	render() {
		let body = '';
		if (
			this.state.course_report_type ==
				'learner-specific-course-reports' &&
			! wisdm_ld_reports_common_script_data.is_pro_version_active
		) {
			body = (
				<DummyReports
					image_path="dcr.png"
					url="https://go.learndash.com/ppaddon"
				></DummyReports>
			);
			return body;
		}
		if (
			'' != this.state.reportTypeInUse &&
			'default-ld-reports' != this.state.reportTypeInUse
		) {
			body = '';
		} else if ( ! this.state.isLoaded ) {
			body = <WisdmLoader text={ this.state.show_supporting_text } />;
		} else if ( this.state.error ) {
			// error
			body = (
				<div className="wisdm-learndash-reports-chart-block">
					<div className="wisdm-learndash-reports-course-list table-chart-container">
						<div className="course-list-table-container">
							<WisdmFilters
								request_data={ this.state.request_data }
							/>
						</div>
						<div className="chart-summary error">
							<div className="error-message">
								<span>{ this.state.error.message }</span>
							</div>
						</div>
					</div>
				</div>
			);
		} else {
			const table_data = this.state?.tableData ?? [];
			const table_headers = [];
			const table_parsed_data = [];

			for (
				let iterator = 0;
				iterator < this.state.tableHeaders.length;
				iterator++
			) {
				table_headers[ iterator ] =
					this.state.tableHeaders[ iterator ].Header;
			}
			table_parsed_data.push( table_headers );
			for ( const letter of table_data.values() ) {
				table_parsed_data.push( Object.values( letter ) );
			}
			body = (
				<div className="wisdm-learndash-reports-chart-block">
					<div className="wisdm-learndash-reports-course-list table-chart-container">
						<div className="course-list-table-container">
							<WisdmFilters
								request_data={ this.state.request_data }
							/>
							<div className="course-list-table-header">
								<div className="chart-title">
									<span>
										{ __(
											'Detailed',
											'learndash-reports-pro'
										) +
											' ' +
											//  cspell:disable-next-line
											wisdm_reports_get_ld_custom_lebel_if_avaiable(
												'Course'
											) +
											' ' +
											__(
												'Reports',
												'learndash-reports-pro'
											) }
									</span>
									<div className="course-list-table-download-options">
										<span>
											{ __(
												'Download ',
												'learndash-reports-pro'
											) }
										</span>
										{ this.state.isProVersion ? (
											<CSVLink
												data={ table_parsed_data }
												filename={
													'exported_table.csv'
												}
												className="btn btn-primary"
												target="_blank"
											>
												<button className="download-csv-button">
													<img
														src={
															wisdm_ld_reports_common_script_data.plugin_asset_url +
															'/images/csv.svg'
														}
													></img>
												</button>
											</CSVLink>
										) : (
											<button
												disabled
												className="disabled download-csv-button"
												title={ __(
													'Available in pro version',
													'learndash-reports-pro'
												) }
											>
												<img
													src={
														wisdm_ld_reports_common_script_data.plugin_asset_url +
														'/images/csv.svg'
													}
												></img>
											</button>
										) }
										{ this.state.isProVersion ? (
											<span
												onClick={ this.getExcelFile }
												className="btn btn-primary"
												target="_blank"
											>
												<button className="download-csv-button">
													<img
														src={
															wisdm_ld_reports_common_script_data.plugin_asset_url +
															'/images/xls.svg'
														}
													></img>
												</button>
											</span>
										) : (
											<button
												disabled
												className="disabled download-csv-button"
												title={ __(
													'Available in pro version',
													'learndash-reports-pro'
												) }
											>
												<img
													src={
														wisdm_ld_reports_common_script_data.plugin_asset_url +
														'/images/xls.svg'
													}
												></img>
											</button>
										) }
									</div>
								</div>
								<div className="refresh-data">
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
								</div>
							</div>
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
						</div>
					</div>
				</div>
			);
		}
		return body;
	}
}

export default CourseList;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-course-list'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( CourseList ) );
	}
} );
