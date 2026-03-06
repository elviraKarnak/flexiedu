// cspell:ignore myrow questioninfo markcorrect markincorrect allquestion qspoints

import './index.scss';

import WisdmLoader from '../commons/loader/index.js';
import React, { Component, Fragment } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
import WisdmFilters from '../commons/filters/index.js';
import { useTable, usePagination } from 'react-table';
import { Modal } from '@wordpress/components';
import StudentFilters from '../student-filters/index-student-filters.js';

// Custom component to render Genres
const CustomHtml = ( { values } ) => {
	// Loop through the array and create a badge-like component instead of a comma-separated string
	return (
		<>
			{
				<span
					dangerouslySetInnerHTML={ { __html: decodeURI( values ) } }
				></span>
			}
		</>
	);
};

// Custom component to render Genres
const CustomDiv = ( { values } ) => {
	// Loop through the array and create a badge-like component instead of a comma-separated string
	return <>{ <div dangerouslySetInnerHTML={ { __html: values } }></div> }</>;
};

function expandTableRow( event, i ) {
	const div = jQuery( '#myrow_' + i ).toggle( 100 );
	jQuery( '#myrow_' + i ).toggleClass(
		'student-dashboard-row-border-change-r2'
	);
	jQuery( '#my_parent_row_' + i ).toggleClass(
		'student-dashboard-row-border-change'
	);
}

function questionSelected( event ) {
	const div = jQuery( event.target );
	const qno = div.html();
	console.log( div.html() );
	const applyFilters = new CustomEvent( 'wisdm-ld-question-clicked', {
		detail: {
			question_detail: jQuery( div ).attr( 'data-question' ),
			attempt_detail: jQuery( div ).attr( 'data-attempt' ),
			all_questions: jQuery( div ).attr( 'data-allquestion' ),
			q_no: qno,
		},
	} );
	document.dispatchEvent( applyFilters );
	jQuery( '.question_detail_modal' ).trigger( 'click' );
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
		{ columns, data, initialState: { pageIndex: 0 } },
		usePagination
	);

	// Render the UI for your table
	return (
		<>
			<div className="course-reports-wrapper">
				<div className="course-table-wrap">
					<table
						className="course-list-table student-table"
						{ ...getTableProps() }
					>
						<thead>
							{ headerGroups.map( ( headerGroup, i ) => (
								<tr
									{ ...headerGroup.getHeaderGroupProps() }
									key={ i }
								>
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
									<Fragment key={ i }>
										<tr
											id={ 'my_parent_row_' + i }
											className="course-list-table-data-row student-dashboard-table-row"
											{ ...row.getRowProps() }
											key={ 'my_parent_row_' + i }
										>
											{ row.cells.map( ( cell, i ) => {
												return (
													<td
														className={
															cell.column
																.className
														}
														id="custom-reports"
														key={ i }
														{ ...cell.getCellProps() }
													>
														{ cell.column.Header ==
														'' ? (
															<span
																className="accordion-trigger"
																onClick={ (
																	event
																) => {
																	expandTableRow(
																		event,
																		i
																	);
																} }
																data-cname={ JSON.stringify(
																	cell.column
																		.className
																) }
															></span>
														) : (
															cell.render(
																'Cell'
															)
														) }
													</td>
												);
											} ) }
										</tr>
										<tr
											className="course-list-table-data-row myrow_"
											id={ 'myrow_' + i }
											{ ...row.getRowProps() }
											key={ 'myrow_' + i }
										>
											{ row.cells.map( ( cell, i ) => {
												return (
													<Fragment key={ i }>
														{ cell.column.Header ==
														'' ? (
															<td
																className={
																	cell.column
																		.className
																}
																{ ...cell.getCellProps() }
																colSpan="9"
															>
																<div className="question-container">
																	<div className="question-head">
																		<div className="question-count">
																			<strong>
																				{ __(
																					'List of ',
																					'learndash-reports-pro'
																				) }
																				{
																					cell
																						.row
																						.original
																						.questions
																						.length
																				}{ ' ' }
																				{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
																					'Questions'
																				) }
																			</strong>
																		</div>
																		<div className="answer-status">
																			<strong>
																				{ __(
																					'Answer Status:',
																					'learndash-reports-pro'
																				) }{ ' ' }
																			</strong>
																			<span className="list markcorrect">
																				{ __(
																					'Correct',
																					'learndash-reports-pro'
																				) }
																			</span>
																			<span className="list markincorrect">
																				{ __(
																					'Incorrect',
																					'learndash-reports-pro'
																				) }
																			</span>
																		</div>
																	</div>
																	<div className="question-body">
																		<ul>
																			{ ' ' }
																			{ cell.row.original.questions.map(
																				(
																					questionData,
																					i
																				) => {
																					const title_color =
																						parseInt(
																							questionData.qspoints
																						) >
																						0
																							? 'correct'
																							: 'incorrect';
																					return (
																						<li
																							className={
																								title_color
																							}
																							data-question={ JSON.stringify(
																								questionData
																							) }
																							data-attempt={ JSON.stringify(
																								cell
																									.row
																									.original
																							) }
																							data-allquestion={ JSON.stringify(
																								cell
																									.row
																									.original
																									.questions
																							) }
																							onClick={
																								questionSelected
																							}
																							key={
																								i
																							}
																						>
																							{ i +
																								1 }
																						</li>
																					);
																				}
																			) }
																		</ul>
																	</div>
																</div>
															</td>
														) : (
															''
														) }
													</Fragment>
												);
											} ) }
										</tr>
									</Fragment>
								);
							} ) }
						</tbody>
					</table>
				</div>
				{ /*
        Pagination can be built however you'd like.
        This is just a very basic UI implementation:
      */ }
				{ /* <div className="table-pagination">
        <button onClick={() => gotoPage(0)} disabled={!canPreviousPage}>
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
        <button onClick={() => gotoPage(pageCount - 1)} disabled={!canNextPage}>
          {">>"}
        </button>{" "}
      </div> */ }
			</div>
		</>
	);
}

class StudentTable extends Component {
	constructor( props ) {
		super( props );
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
			isLoaded: true,
			isQuestionLoaded: false,
			error,
			tableHeaders: [],
			tableData: [],
			start_date: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			).unix(),
			end_date: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			).unix(),
			user_id: wisdm_ld_reports_common_script_data.current_user.ID,
			show_question_detail_modal: false,
			questionData: {},
			attemptData: {},
			allQuestions: [],
			question_rendered: '',
			page: 1,
			entries: 0,
			course: null,
			quiz: null,
		};

		this.applyFilters = this.applyFilters.bind( this );
		this.addMoreData = this.addMoreData.bind( this );
		this.questionDetailsPopulate =
			this.questionDetailsPopulate.bind( this );
		this.closeQuestionModal = this.closeQuestionModal.bind( this );
		this.openQuestionModal = this.openQuestionModal.bind( this );
		this.changePageEvent = this.changePageEvent.bind( this );
	}

	changePageEvent() {
		const self = this;
		jQuery( document ).on(
			'click',
			'.pagination-section a.page-numbers',
			function ( event ) {
				event.preventDefault();
				const page = jQuery( this ).attr( 'data-page' );
				self.setState( {
					page,
				} );
				const request_url =
					'/rp/v1/student-dashboard-info/?start_date=' +
					self.state.start_date +
					'&end_date=' +
					self.state.end_date +
					'&course_id=' +
					self.state.course +
					'&quiz_id=' +
					self.state.quiz +
					'&user_id=' +
					self.state.user_id +
					'&page=' +
					page;
				self.getStudentQuizData( request_url );
			}
		);
		jQuery( document ).on(
			'click',
			'.pagination-section .previous-page',
			function ( event ) {
				event.preventDefault();
				if ( jQuery( this ).hasClass( 'disabled' ) ) {
					return true;
				}
				const next = self.state.page - 1;
				self.setState( { page: next } );
				const request_url =
					'/rp/v1/student-dashboard-info/?start_date=' +
					self.state.start_date +
					'&end_date=' +
					self.state.end_date +
					'&course_id=' +
					self.state.course +
					'&quiz_id=' +
					self.state.quiz +
					'&user_id=' +
					self.state.user_id +
					'&page=' +
					next;
				self.getStudentQuizData( request_url );
			}
		);
		jQuery( document ).on(
			'click',
			'.pagination-section .next-page',
			function ( event ) {
				event.preventDefault();
				if ( jQuery( this ).hasClass( 'disabled' ) ) {
					return true;
				}
				const next = self.state.page + 1;
				self.setState( { page: next } );
				const request_url =
					'/rp/v1/student-dashboard-info/?start_date=' +
					self.state.start_date +
					'&end_date=' +
					self.state.end_date +
					'&course_id=' +
					self.state.course +
					'&quiz_id=' +
					self.state.quiz +
					'&user_id=' +
					self.state.user_id +
					'&page=' +
					next;
				self.getStudentQuizData( request_url );
			}
		);
	}

	closeQuestionModal() {
		document.body.classList.remove( 'wrld-open' );
		this.setState( {
			show_question_detail_modal: false,
		} );
	}

	openQuestionModal() {
		document.body.classList.add( 'wrld-open' );
		this.setState( {
			show_question_detail_modal: true,
		} );
	}

	/**
	 * Based on the current user roles array this function decides wether a user is a group
	 * leader or an Administrator and returns the same.
	 */
	getUserType() {
		return wisdm_ld_reports_common_script_data.current_user.ID == 0;
	}

	questionDetailsPopulate( event ) {
		const questionData = JSON.parse( event.detail.question_detail );
		const attemptData = JSON.parse( event.detail.attempt_detail );
		const allQuestions = JSON.parse( event.detail.all_questions );
		const q_no = JSON.parse( event.detail.q_no );
		this.setState( {
			questionData,
			attemptData,
			allQuestions,
			isQuestionLoaded: false,
			question_rendered: '',
		} );
		let requestUrl =
			'/rp/v1/question-details/?question_data=' +
			event.detail.question_detail +
			'&q_no=' +
			q_no;
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			requestUrl +=
				'&wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		wp.apiFetch( {
			path: requestUrl,
		} ).then( ( response ) => {
			this.setState( {
				question_rendered: response.table,
				isQuestionLoaded: true,
			} );
		} );
	}

	applyFilters( event ) {
		const start_date = event.detail.start_date;
		const end_date = event.detail.end_date;
		const course = event.detail.selected_courses.value;
		const quiz = event.detail.selected_quiz.value;
		const user_id = event.detail.user_id;
		const courses = event.detail.courses;
		const quizzes = event.detail.quizzes;
		this.setState( {
			start_date,
			end_date,
			course,
			quiz,
			user_id,
			page: 1,
			courses,
			quizzes,
			course_label: event.detail.selected_courses.label,
			quiz_label: event.detail.selected_quiz.label,
		} );
		const request_url =
			'/rp/v1/student-dashboard-info/?start_date=' +
			start_date +
			'&end_date=' +
			end_date +
			'&course_id=' +
			course +
			'&quiz_id=' +
			quiz +
			'&user_id=' +
			user_id +
			'&page=1';
		this.getStudentQuizData( request_url );
	}

	addMoreData( event ) {
		const next = this.state.page + 1;
		this.setState( { page: next } );
		const request_url =
			'/rp/v1/student-dashboard-info/?start_date=' +
			this.state.start_date +
			'&end_date=' +
			this.state.end_date +
			'&course_id=' +
			this.state.course +
			'&quiz_id=' +
			this.state.quiz +
			'&user_id=' +
			this.state.user_id +
			'&page=' +
			next;
		this.getStudentQuizData( request_url );
	}

	componentDidMount() {
		const request_url =
			'/rp/v1/student-dashboard-info/?start_date=' +
			this.state.start_date +
			'&end_date=' +
			this.state.end_date +
			'&course_id=' +
			null +
			'&quiz_id=' +
			null +
			'&user_id=' +
			this.state.user_id +
			'&page=1';

		this.getStudentQuizData( request_url );
		this.changePageEvent();
		document.addEventListener(
			'wisdm-ld-reports-student-filters-applied',
			this.applyFilters
		);
		document.addEventListener(
			'wisdm-ld-question-clicked',
			this.questionDetailsPopulate
		);
	}

	componentDidUpdate() {}

	getStudentQuizData( request_url = '/rp/v1/course-list-info' ) {
		jQuery( '.button-quiz-preview' ).css( { cursor: 'progress' } );
		this.setState( {
			isLoaded: false,
		} );
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
				jQuery( '.button-quiz-preview' ).css( { cursor: 'pointer' } );
				this.setState( {
					isLoaded: true,
					isProVersion:
						wisdm_ld_reports_common_script_data.is_pro_version_active,
					tableHeaders: this.getTableHeadersByType( response.table ),
					tableData: this.getTableData( response.table ),
					request_data: response.requestData || {},
					entries: response.total || 0,
				} );
				const applyFilters = new CustomEvent(
					'wisdm-ld-table-reloaded',
					{
						detail: {
							parent_state: this.state,
						},
					}
				);
				document.dispatchEvent( applyFilters );
			} )
			.catch( ( error ) => {
				const data = error.data || {};

				this.setState( {
					error,
					isLoaded: true,
					request_data: data.requestData || {},
				} );
			} );
	}

	getTableData( response ) {
		const newTableData = [];
		response.map( ( columns ) => {
			let total_point = 0;
			let earned_point = 0;
			let total_time = 0;
			let score_in_percentage = '';
			columns.questions.map( ( question ) => {
				total_point = total_point + parseInt( question.points, 10 );
				earned_point = earned_point + parseInt( question.qspoints, 10 );
				total_time =
					total_time + parseInt( question.question_time, 10 );
			} );
			score_in_percentage =
				total_point == 0
					? 0
					: ( ( earned_point / total_point ) * 100 ).toFixed( 2 ) +
					  '%';

			total_time = new Date( total_time * 1000 )
				.toISOString()
				.slice( 11, 19 );

			newTableData.push( {
				...columns,
				total_time,
				total_point,
				score_prcentage: score_in_percentage, // cspell:disable-line
				earned_point,
				first_col: '',
			} );
		} );
		return newTableData;
	}

	getTableHeadersByType( response ) {
		const headers = [];
		headers.push( {
			Header: '',
			accessor: 'first_col',
			className: 'table-first_col',
		} );
		headers.push( {
			Header: sprintf(
				// Translators: %s: Quiz label.
				__( '%s title', 'learndash-reports-pro' ),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' )
			),
			accessor: 'quiz_title',
			className: 'table-quiz_title',
			Cell: ( { cell: { value } } ) => <CustomHtml values={ value } />,
		} );
		headers.push( {
			Header: sprintf(
				// Translators: %s: Course label.
				__( '%s title', 'learndash-reports-pro' ),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' )
			),
			accessor: 'course_title',
			className: 'table-course_title',
		} );
		headers.push( {
			Header: sprintf(
				// Translators: %s: Quiz label.
				__( '%s Category', 'learndash-reports-pro' ),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' )
			),
			accessor: 'quiz_category',
			className: 'table-quiz_category',
		} );
		headers.push( {
			Header: __( 'Points Earned', 'learndash-reports-pro' ),
			accessor: 'earned_point',
			className: 'table-earned_point',
		} );
		headers.push( {
			Header: __( 'Score in %', 'learndash-reports-pro' ),
			accessor: 'score_prcentage', // cspell:disable-line
			className: 'table-score_prcentage', // cspell:disable-line
		} );
		headers.push( {
			Header: __( 'Date of Attempt', 'learndash-reports-pro' ),
			accessor: 'date_attempt',
			className: 'table-date_attempt',
		} );
		headers.push( {
			Header: __( 'Time Taken', 'learndash-reports-pro' ),
			accessor: 'total_time',
			className: 'table-total_time',
		} );
		headers.push( {
			Header: __( 'Download', 'learndash-reports-pro' ),
			accessor: 'links',
			Cell: ( { cell: { value } } ) => <CustomHtml values={ value } />,
			className: 'table-links',
		} );
		return headers;
	}

	render() {
		let body = <div></div>;
		const question_type_map = {
			single: __( 'Single choice', 'learndash-reports-pro' ),
			multiple: __( 'Multiple choice', 'learndash-reports-pro' ),
			free_answer: __( 'Free choice', 'learndash-reports-pro' ),
			sort_answer: __( 'Sorting choice', 'learndash-reports-pro' ),
			matrix_sort_answer: __(
				'"Matrix Sorting" choice',
				'learndash-reports-pro'
			),
			cloze_answer: __( 'Fill in the blank', 'learndash-reports-pro' ),
			assessment_answer: __( 'Assessment', 'learndash-reports-pro' ),
			essay: __( 'Essay / Open Answer', 'learndash-reports-pro' ),
		};
		if ( this.state.error ) {
			body = (
				<div className="ldrp-nodata-container wrld-error">
					<div>
						<strong>
							{ ' ' }
							{ __(
								'Access Denied.',
								'learndash-reports-pro'
							) }{ ' ' }
						</strong>
						{ __(
							'You need to be logged in to access this page.',
							'learndash-reports-pro'
						) }
					</div>
				</div>
			);
		} else if ( ! this.state.isLoaded ) {
			// yet loading
			body = <WisdmLoader />;
		} else {
			const table_data = this.state.tableData;
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
			const pages = Math.ceil( this.state.entries / 10 );
			let page_divs = '<div className="pagination-section">';
			if ( pages > 1 ) {
				page_divs =
					page_divs +
					'<a href="#" className="previous-page button ' +
					( this.state.page == 1 ? 'disabled' : '' ) +
					'">' +
					__( 'Previous', 'learndash-reports-pro' ) +
					'</a>';
				for ( let i = 1; i <= pages; i++ ) {
					if ( this.state.page == i ) {
						page_divs =
							page_divs +
							'<span aria-current="page" className="page-numbers current">' +
							this.state.page +
							'</span>';
					} else {
						page_divs =
							page_divs +
							'<a className="page-numbers" data-page=' +
							i +
							' href="#">' +
							i +
							'</a>';
					}
				}
				page_divs =
					page_divs +
					'<a href="#" className="next-page button ' +
					( this.state.page == pages ? 'disabled' : '' ) +
					'">' +
					__( 'Next', 'learndash-reports-pro' ) +
					'</a>';
			}
			page_divs = page_divs + '</div>';
			body = (
				<div className="user-info-section">
					<StudentFilters parent={ this.state } />
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
												// cspell:disable-next-line
												wisdm_reports_get_ld_custom_lebel_if_avaiable(
													'Quiz'
												) +
												' ' +
												__(
													'Reports',
													'learndash-reports-pro'
												) }
										</span>
									</div>
								</div>
								{ this.state.tableData.length > 0 ? (
									<Table
										columns={ this.state.tableHeaders }
										data={ this.state.tableData }
									/>
								) : (
									<div>
										<Table
											columns={ this.state.tableHeaders }
											data={ this.state.tableData }
										/>
										<div className="error-message">
											<span>
												{ sprintf(
													// Translators: %s: quiz label.
													__(
														'No %s attempts for the selected filters.',
														'learndash-reports-pro'
													),
													wisdm_reports_get_ld_custom_lebel_if_avaiable(
														'Quiz',
														'lower'
													)
												) }
											</span>
										</div>
									</div>
								) }
								<CustomDiv values={ page_divs } />
							</div>
						</div>
					</div>
					{ this.state.show_question_detail_modal && (
						<Modal
							onRequestClose={ this.closeQuestionModal }
							className={
								'learndash-propanel-modal bulk_export_modal'
							}
						>
							<div className="qre-question-container question-detail-modal student-dash">
								<h2>
									{ sprintf(
										// Translators: %s: Question label.
										__(
											'%s Response Report',
											'learndash-reports-pro'
										),
										wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'Question'
										)
									) }
								</h2>
								<div className="question-details">
									<div className="outer-1">
										<div className="inner-1">
											<div className="user-detail">
												<div className="username">
													<span>
														<strong>
															{ ' ' }
															{ __(
																'User Name: ',
																'learndash-reports-pro'
															) }
														</strong>
														<br />
														{
															wisdm_ld_reports_common_script_data
																.current_user
																.data
																.display_name
														}
													</span>
												</div>
												<div className="quiz-title">
													<span>
														<strong>
															{ sprintf(
																// Translators: %s: Quiz label.
																__(
																	'%s Title: ',
																	'learndash-reports-pro'
																),
																wisdm_reports_get_ld_custom_lebel_if_avaiable(
																	'Quiz'
																)
															) }
														</strong>
														<br />
														<CustomHtml
															values={
																this.state
																	.attemptData
																	.quiz_title
															}
														/>
													</span>
												</div>
											</div>
											<div>
												<span>
													<strong>
														{ ' ' }
														{ __(
															'Score(in %): ',
															'learndash-reports-pro'
														) }
													</strong>
													<br />
													{
														this.state.attemptData
															.score_prcentage // cspell:disable-line
													}
												</span>
											</div>
										</div>
									</div>
									<br />
									<hr />
									<div className="outer-2 questioninfo">
										<div className="question-type">
											<strong>
												{ sprintf(
													// Translators: %s: Question label.
													__(
														'%s Type :',
														'learndash-reports-pro'
													),
													wisdm_reports_get_ld_custom_lebel_if_avaiable(
														'Question'
													)
												) }{ ' ' }
												{
													this.state.questionData
														.length
												}
											</strong>
											<br />
											<span>
												{
													question_type_map[
														this.state.questionData
															.answer_type
													]
												}
											</span>
										</div>
										<div className="question-category">
											<strong>
												{ ' ' }
												{ sprintf(
													// Translators: Question label.
													__(
														'%s Category',
														'learndash-reports-pro'
													),
													wisdm_reports_get_ld_custom_lebel_if_avaiable(
														'Question'
													)
												) }
											</strong>
											<br />
											{
												this.state.attemptData
													.quiz_category
											}
										</div>
									</div>
									<div className="outer-3">
										<div className="learndash">
											<div className="learndash-wrapper">
												<div className="wpProQuiz_content">
													<div className="wpProQuiz_quiz">
														{ this.state
															.isQuestionLoaded ? (
															<CustomDiv
																values={
																	this.state
																		.question_rendered
																}
															/>
														) : (
															<div className="wisdm-question-loader">
																<WisdmLoader />
															</div>
														) }
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div className="question-navigation question-body">
									<ul>
										{ this.state.allQuestions.map(
											( questionData, i ) => {
												const title_color =
													parseInt(
														questionData.qspoints
													) > 0
														? 'correct'
														: 'incorrect';
												return (
													<li
														className={
															title_color
														}
														data-question={ JSON.stringify(
															questionData
														) }
														data-allquestion={ JSON.stringify(
															this.state
																.allQuestions
														) }
														data-attempt={ JSON.stringify(
															this.state
																.attemptData
														) }
														onClick={
															questionSelected
														}
														key={ i }
													>
														{ i + 1 }
													</li>
												);
											}
										) }
									</ul>
								</div>
							</div>
						</Modal>
					) }
					<button
						className="question_detail_modal"
						onClick={ this.openQuestionModal }
						style={ { display: 'none' } }
					>
						{ sprintf(
							// Translators: Question label.
							__( '%s Response', 'learndash-reports-pro' ),
							wisdm_reports_get_ld_custom_lebel_if_avaiable(
								'Question'
							)
						) }
					</button>
				</div>
			);
		}
		return body;
	}
}

export default StudentTable;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-student-table front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( StudentTable ) );
	}
} );
