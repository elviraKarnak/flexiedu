/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/#registering-a-block
 */
import { registerBlockType, RichText, source } from '@wordpress/blocks';

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __, sprintf } from '@wordpress/i18n';

import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';

import React, { Component } from 'react';
import { select, subscribe } from '@wordpress/data';
import {
	hasReportToolsBlock,
	getReportToolsBlockSettings,
} from '../commons/has-report-tools-block.js';
import ChartSummarySection from '../commons/chart-summary/index.js';

/**
 * Safe wrapper for getting LearnDash custom labels.
 *
 * @param {string} label    The default label to get.
 * @param {string} format   The format ('lower' for lowercase, default for normal).
 * @return {string} The custom label or default.
 */
function getCustomLabel( label, format = '' ) {
	if ( typeof wisdm_reports_get_ld_custom_lebel_if_avaiable === 'function' ) {
		return wisdm_reports_get_ld_custom_lebel_if_avaiable( label, format );
	}
	const result = __( label, 'learndash-reports-pro' );
	return format === 'lower' ? result.toLowerCase() : result;
}

const icon = (
	<svg
		version="1.0"
		xmlns="http://www.w3.org/2000/svg"
		width="24.000000pt"
		height="24.000000pt"
		viewBox="0 0 24.000000 24.000000"
		preserveAspectRatio="xMidYMid meet"
	>
		<path d="M11.9,8.7h4.8c0.3,0,0.6-0.3,0.6-0.7c0-0.4-0.3-0.7-0.6-0.7h-4.8c-0.3,0-0.6,0.3-0.6,0.7C11.4,8.3,11.6,8.7,11.9,8.7z" />
		<path d="M16.7,12.4h-4.8c-0.3,0-0.6,0.3-0.6,0.7s0.3,0.7,0.6,0.7h4.8c0.3,0,0.6-0.3,0.6-0.7S17,12.4,16.7,12.4z" />
		<path d="M16.5,17.5h-4.8c-0.3,0-0.6,0.3-0.6,0.7c0,0.4,0.3,0.7,0.6,0.7h4.8c0.3,0,0.6-0.3,0.6-0.7C17.1,17.5,16.7,17.5,16.5,17.5z" />
		<path
			d="M7.5,6.2C7.4,6.1,7.2,6.1,7,6.2C6.8,6.3,6.7,6.5,6.7,6.7v2.9c0,0.2,0.1,0.5,0.3,0.5c0.2,0,0.4-0.1,0.5-0.2l2.3-1.4
        	C9.9,8.4,10,8.2,10,8.1c0-0.2-0.1-0.3-0.2-0.4L7.5,6.2z M8.6,8.1L7.7,8.6v-1L8.6,8.1z"
		/>
		<path
			d="M7.5,11.2c-0.1-0.1-0.3-0.1-0.5,0c-0.2,0.1-0.3,0.3-0.3,0.5v2.9c0,0.2,0.1,0.5,0.3,0.5c0.2,0,0.4-0.1,0.5-0.2l2.3-1.4
        	c0.1-0.1,0.2-0.3,0.2-0.4c0-0.2-0.1-0.3-0.2-0.4L7.5,11.2z M8.6,13.1l-0.9,0.5v-1L8.6,13.1z"
		/>
		<path
			d="M7.5,16.2c-0.1-0.1-0.3-0.1-0.5,0s-0.3,0.3-0.3,0.5v2.9c0,0.2,0.1,0.5,0.3,0.5c0.2,0,0.4-0.1,0.5-0.2l2.3-1.4
        	c0.1-0.1,0.2-0.3,0.2-0.4c0-0.2-0.1-0.3-0.2-0.4L7.5,16.2z M8.6,18.1l-0.9,0.5v-1L8.6,18.1z"
		/>
		<path
			d="M2.9,21.6c0,1.1,0.9,2,1.9,2h14.4c1.1,0,1.9-0.9,1.9-2V3.7c0-1.1-0.9-2-1.9-2h-2.6l-0.1-0.1c-0.7-0.8-1.7-1.2-2.7-1.2h-3.7
        	c-1,0-1.9,0.4-2.6,1.2l0,0.1H4.9c-1.1,0-2,0.9-2,2V21.6z M7.8,3.2c0.4-1,1.4-1.7,2.3-1.7h3.7c1,0,2,0.7,2.3,1.7l0.1,0.2H7.7L7.8,3.2
        	z M4.1,3.7c0-0.5,0.4-0.9,0.8-0.9h2L6.8,3C6.7,3.3,6.6,3.7,6.6,4c0,0.4,0.2,0.6,0.6,0.6h9.7c0.3,0,0.5-0.3,0.5-0.6s0-0.6-0.1-0.9
        	l-0.1-0.2h2c0.5,0,0.8,0.4,0.8,0.9v17.9c0,0.5-0.4,0.9-0.8,0.9H4.9c-0.5,0-0.8-0.4-0.8-0.9V3.7z"
		/>
	</svg>
);

class QuizReport extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			isLoaded: false,
			error: null,
			entries: null,
			noDataMessage: '',
			isPreviewMode: false,
			hasReportTools: hasReportToolsBlock(),
			isQuizReportsTabActive:
				getReportToolsBlockSettings().active_tab === 'quiz',
			totalCount: 0,
			currentPage: 1,
			totalPages: 1,
			limit: 5,
		};
		this.quizReportsRef = React.createRef();
		this.blockWrapperRef = React.createRef();
		this.unsubscribe = null;

		this.fetchData = this.fetchData.bind( this );
		this.handlePageChange = this.handlePageChange.bind( this );
		this.handleLimitChange = this.handleLimitChange.bind( this );
		this.handlePreviousPage = this.handlePreviousPage.bind( this );
		this.handleNextPage = this.handleNextPage.bind( this );
		this.handleReportTypeChange = this.handleReportTypeChange.bind( this );
	}

	componentDidMount() {
		// Subscribe to block changes to detect when Report Tools block is added/removed
		this.unsubscribe = subscribe( () => {
			const hasReportTools = hasReportToolsBlock();
			const isQuizTabActive =
				getReportToolsBlockSettings().active_tab === 'quiz';
			if (
				hasReportTools !== this.state.hasReportTools ||
				isQuizTabActive !== this.state.isQuizReportsTabActive
			) {
				this.setState( {
					hasReportTools,
					isQuizReportsTabActive: isQuizTabActive,
				} );
			}
		} );

		// Listen for tab changes
		document.addEventListener(
			'wisdm-ld-reports-report-type-selected',
			this.handleReportTypeChange
		);

		// Always fetch quiz reports data for editor preview
		// The notice will be shown at the top if Quiz Reports tab is not active
		this.fetchData( 1, this.state.limit );
	}

	handleReportTypeChange( event ) {
		const isQuizTabActive =
			event.detail && event.detail.active_reports_tab === 'quiz-reports';
		if ( isQuizTabActive !== this.state.isQuizReportsTabActive ) {
			this.setState( { isQuizReportsTabActive: isQuizTabActive } );
			// Refetch data when tab changes to ensure fresh content
			if ( isQuizTabActive ) {
				this.fetchData( 1, this.state.limit );
			}
		}
	}

	/**
	 * Fetches quiz reports data from the API.
	 *
	 * @param {number} page  The page number to fetch.
	 * @param {number} limit The number of entries per page.
	 */
	fetchData( page, limit ) {
		// Set loading state immediately
		this.setState( {
			isLoaded: false,
			error: null,
		} );

		// wrld-common-script is a dependency, so wisdm_ld_reports_common_script_data should be available
		if (
			typeof wisdm_ld_reports_common_script_data === 'undefined' ||
			typeof jQuery === 'undefined'
		) {
			// Fallback if data or jQuery is not available - show preview placeholder
			this.setState( {
				isLoaded: true,
				isPreviewMode: true,
			} );
			return;
		}

		const ajaxurl = wisdm_ld_reports_common_script_data.ajaxurl;
		const nonce = wisdm_ld_reports_common_script_data.report_nonce;
		const wpmlLang = wisdm_ld_reports_common_script_data.wpml_lang || '';

		jQuery
			.get(
				ajaxurl,
				{
					action: 'get_quiz_reports_data',
					nonce,
					report: '',
					page,
					pageno: page,
					limit,
					wpml_lang: wpmlLang,
				},
				( response ) => {
					if ( response.success && response.data ) {
						const entriesData = response.data.entries;
						// Handle both array and object formats
						let entries = [];
						let noDataMsg = __(
							'No quiz reports data available.',
							'learndash-reports-pro'
						);
						let totalCount = 0;
						let currentPage = 1;
						let totalPages = 1;

						if ( entriesData && typeof entriesData === 'object' ) {
							if ( Array.isArray( entriesData ) ) {
								// entries is an empty array - no data available
								entries = [];
							} else {
								// entries is an object with data property
								entries = entriesData.data || [];
								noDataMsg = entriesData.no_data || noDataMsg;
								totalCount = entriesData.total || entries.length;
								currentPage = entriesData.page || 1;
								const entriesLimit = entriesData.limit || limit;
								totalPages = Math.ceil( totalCount / entriesLimit ) || 1;
							}
						}

						this.setState( {
							isLoaded: true,
							entries,
							noDataMessage: noDataMsg,
							totalCount,
							currentPage,
							totalPages,
							limit,
						} );
					} else {
						this.setState( {
							isLoaded: true,
							error: new Error(
								response.data && response.data.message
									? response.data.message
									: __(
											'Failed to load quiz reports data',
											'learndash-reports-pro'
									  )
							),
						} );
					}
				}
			)
			.fail( () => {
				this.setState( {
					isLoaded: true,
					error: new Error(
						__(
							'Failed to load quiz reports data',
							'learndash-reports-pro'
						)
					),
				} );
			} );
	}

	/**
	 * Handles page change.
	 *
	 * @param {number} newPage The new page number.
	 */
	handlePageChange( newPage ) {
		const { totalPages, limit } = this.state;
		if ( newPage >= 1 && newPage <= totalPages ) {
			this.fetchData( newPage, limit );
		}
	}

	/**
	 * Handles limit (entries per page) change.
	 *
	 * @param {Object} event The change event.
	 */
	handleLimitChange( event ) {
		const newLimit = parseInt( event.target.value, 10 );
		this.fetchData( 1, newLimit );
	}

	/**
	 * Handles previous page button click.
	 *
	 * @param {Object} event The click event.
	 */
	handlePreviousPage( event ) {
		event.preventDefault();
		const { currentPage } = this.state;
		if ( currentPage > 1 ) {
			this.handlePageChange( currentPage - 1 );
		}
	}

	/**
	 * Handles next page button click.
	 *
	 * @param {Object} event The click event.
	 */
	handleNextPage( event ) {
		event.preventDefault();
		const { currentPage, totalPages } = this.state;
		if ( currentPage < totalPages ) {
			this.handlePageChange( currentPage + 1 );
		}
	}

	componentWillUnmount() {
		// Clean up subscription
		if ( this.unsubscribe ) {
			this.unsubscribe();
		}
		document.removeEventListener(
			'wisdm-ld-reports-report-type-selected',
			this.handleReportTypeChange
		);
	}

	/**
	 * Renders an HTML string as React elements, stripping dangerous scripts.
	 *
	 * @param {string} html - The HTML string to render.
	 * @return {JSX.Element} The rendered HTML.
	 */
	renderHtml( html ) {
		return (
			<span dangerouslySetInnerHTML={ { __html: html } } />
		);
	}

	/**
	 * Gets the Quiz label from LearnDash custom labels.
	 *
	 * @return {string} The Quiz label.
	 */
	getQuizLabel() {
		return getCustomLabel( 'Quiz' );
	}

	/**
	 * Renders a preview placeholder table for the editor.
	 *
	 * @return {JSX.Element} The preview table element.
	 */
	renderPreviewTable() {
		const quizLabel = this.getQuizLabel();
		const placeholderData = [
			{
				index: 1,
				quiz_title: sprintf(
					__( 'Sample %s 1', 'learndash-reports-pro' ),
					quizLabel
				),
				user_name: __( 'John Doe', 'learndash-reports-pro' ),
				date_attempt: '2024-01-15',
				score: '85%',
				time_taken: '5:30',
			},
			{
				index: 2,
				quiz_title: sprintf(
					__( 'Sample %s 2', 'learndash-reports-pro' ),
					quizLabel
				),
				user_name: __( 'Jane Smith', 'learndash-reports-pro' ),
				date_attempt: '2024-01-14',
				score: '92%',
				time_taken: '4:15',
			},
			{
				index: 3,
				quiz_title: sprintf(
					__( 'Sample %s 1', 'learndash-reports-pro' ),
					quizLabel
				),
				user_name: __( 'Bob Johnson', 'learndash-reports-pro' ),
				date_attempt: '2024-01-13',
				score: '78%',
				time_taken: '6:45',
			},
		];

		return (
			<div className="wisdm-learndash-reports-quiz-reports-preview">
				<table className="course-list-table">
					<thead>
						<tr>
							<th>{ __( 'No.', 'learndash-reports-pro' ) }</th>
							<th>
								{ sprintf(
									__( '%s Title', 'learndash-reports-pro' ),
									quizLabel
								) }
							</th>
							<th>{ __( 'Student Name', 'learndash-reports-pro' ) }</th>
							<th>{ __( 'Date of Attempt', 'learndash-reports-pro' ) }</th>
							<th>{ __( 'Score', 'learndash-reports-pro' ) }</th>
							<th>{ __( 'Time Taken', 'learndash-reports-pro' ) }</th>
							<th>{ __( 'Download', 'learndash-reports-pro' ) }</th>
						</tr>
					</thead>
					<tbody>
						{ placeholderData.map( ( entry, index ) => (
							<tr key={ index } className="course-list-table-data-row">
								<td>{ entry.index }</td>
								<td>{ entry.quiz_title }</td>
								<td>{ entry.user_name }</td>
								<td>{ entry.date_attempt }</td>
								<td>{ entry.score }</td>
								<td>{ entry.time_taken }</td>
								<td>
									<span>{ __( 'View', 'learndash-reports-pro' ) }</span>
								</td>
							</tr>
						) ) }
					</tbody>
				</table>
				<div className="wisdm-learndash-reports-quiz-reports-preview-overlay">
					<p>
						{ sprintf(
							__( '%s Reports Preview', 'learndash-reports-pro' ),
							quizLabel
						) }
					</p>
					<small>
						{ __( 'Actual data will be displayed on the frontend.', 'learndash-reports-pro' ) }
					</small>
				</div>
			</div>
		);
	}

	/**
	 * Renders the pagination controls matching frontend structure.
	 *
	 * @return {JSX.Element|null} The pagination element or null if only one page.
	 */
	renderPagination() {
		const { currentPage, totalPages } = this.state;

		if ( totalPages <= 1 ) {
			return null;
		}

		return (
			<div className="pagination-section">
				<button
					type="button"
					className="previous-page"
					disabled={ currentPage <= 1 }
					onClick={ this.handlePreviousPage }
				>
					{ __( 'Previous', 'learndash-reports-pro' ) }
				</button>
				<span>
					{ __( 'Page', 'learndash-reports-pro' ) }{ ' ' }
					<input
						type="text"
						className="page"
						key={ `page-input-${ currentPage }` }
						defaultValue={ currentPage }
						data-max={ totalPages }
						onKeyDown={ ( e ) => {
							if ( e.key === 'Enter' ) {
								e.preventDefault();
								const newPage = parseInt( e.target.value, 10 );
								if ( ! isNaN( newPage ) && newPage >= 1 && newPage <= totalPages ) {
									this.handlePageChange( newPage );
								}
							}
						} }
						onBlur={ ( e ) => {
							const newPage = parseInt( e.target.value, 10 );
							if ( ! isNaN( newPage ) && newPage >= 1 && newPage <= totalPages && newPage !== currentPage ) {
								this.handlePageChange( newPage );
							}
						} }
					/>
					{ ' ' }{ __( 'of', 'learndash-reports-pro' ) } { totalPages }
				</span>
				<button
					type="button"
					className="next-page"
					disabled={ currentPage >= totalPages }
					onClick={ this.handleNextPage }
				>
					{ __( 'Next', 'learndash-reports-pro' ) }
				</button>
			</div>
		);
	}

	/**
	 * Renders the quiz reports table with data.
	 *
	 * @return {JSX.Element} The table element.
	 */
	renderTable() {
		const { entries, noDataMessage, isPreviewMode, totalCount, limit } = this.state;

		// If in preview mode (data not available), show placeholder
		if ( isPreviewMode ) {
			return this.renderPreviewTable();
		}

		if ( ! entries || entries.length === 0 ) {
			return (
				<div className="wisdm-learndash-reports-quiz-reports-no-data">
					{ noDataMessage ||
						__( 'No quiz reports data available.', 'learndash-reports-pro' ) }
				</div>
			);
		}

		const quizLabel = this.getQuizLabel();
		const reportCount = totalCount || entries.length;

		return (
			<div className="qre-reports-content">
				<div className="qre-reports-header">
					<h2>{ __( 'All Attempts Report', 'learndash-reports-pro' ) }</h2>
					<button className="wrld-bulk-export">
						<span className="dashicons dashicons-download"></span>
						{ __( 'Bulk Export', 'learndash-reports-pro' ) }
					</button>
				</div>
				<div className="results-section">
					<div className="results-section-header">
						<div className="total_count">
							<span>
								<strong>{ reportCount }</strong>{ ' ' }
								{ reportCount === 1
									? __( 'Report', 'learndash-reports-pro' )
									: __( 'Reports', 'learndash-reports-pro' ) }
							</span>
						</div>
						<div className="entries-shown">
							<select
								className="limit"
								value={ limit }
								onChange={ this.handleLimitChange }
							>
								<option value="5">{ __( 'Show 5', 'learndash-reports-pro' ) }</option>
								<option value="10">{ __( 'Show 10', 'learndash-reports-pro' ) }</option>
								<option value="20">{ __( 'Show 20', 'learndash-reports-pro' ) }</option>
								<option value="50">{ __( 'Show 50', 'learndash-reports-pro' ) }</option>
							</select>
						</div>
					</div>
					<table id="qre_summarized_data" className="qre_summarized_data row-border" style={ { width: '100%' } }>
						<thead>
							<tr>
								<th>{ __( 'No.', 'learndash-reports-pro' ) }</th>
								<th>
									{ sprintf(
										__( '%s Title', 'learndash-reports-pro' ),
										quizLabel
									) }
								</th>
								<th>{ __( 'Student Name', 'learndash-reports-pro' ) }</th>
								<th>{ __( 'Date of Attempt', 'learndash-reports-pro' ) }</th>
								<th>{ __( 'Score', 'learndash-reports-pro' ) }</th>
								<th>{ __( 'Time Taken', 'learndash-reports-pro' ) }</th>
								<th>{ __( 'Download', 'learndash-reports-pro' ) }</th>
							</tr>
						</thead>
						<tbody>
							{ entries.map( ( entry, index ) => (
								<tr key={ index } className="course-list-table-data-row">
									<td className="dt-center">{ entry.index || index + 1 }</td>
									<td className="dt-left">
										{ this.renderHtml( entry.quiz_title ) }
									</td>
									<td className="dt-left">
										{ this.renderHtml( entry.user_name ) }
									</td>
									<td className="dt-center">{ entry.date_attempt }</td>
									<td className="dt-center">{ entry.score }</td>
									<td className="dt-center">{ entry.time_taken }</td>
									<td className="dt-center download-links">
										{ this.renderHtml( entry.link ) }
									</td>
								</tr>
							) ) }
						</tbody>
					</table>
					{ this.renderPagination() }
				</div>
			</div>
		);
	}

	/**
	 * Renders the main content area (loading, error, or table).
	 *
	 * @return {JSX.Element} The content element.
	 */
	renderContent() {
		if ( ! this.state.isLoaded ) {
			return (
				<div className="wisdm-ld-loading">
					<div>{ __( 'Loading…', 'learndash-reports-pro' ) }</div>
				</div>
			);
		}

		if ( this.state.error ) {
			return (
				<div className="error">
					<div>{ this.state.error.message }</div>
				</div>
			);
		}

		return this.renderTable();
	}

	render() {
		const quizLabel = this.getQuizLabel();
		const showNotice =
			this.state.hasReportTools && ! this.state.isQuizReportsTabActive;

		// Always render the block content, but show notice at top when Quiz Reports tab is not active
		return (
			<div
				ref={ this.blockWrapperRef }
				className="wisdm-learndash-reports-chart-block wisdm-learndash-reports-quiz-reports-editor-wrapper"
			>
				<div
					ref={ this.quizReportsRef }
					id="wisdm-learndash-reports-quiz-report-view"
					className="wisdm-learndash-reports-quiz-reports wisdm-learndash-reports-quiz-reports-editor"
				>
					{ showNotice && (
						<ChartSummarySection
							wrapper_class="chart-summary-quiz-reports"
							graph_summary={ { left: [], right: [] } }
							error={ {
								message: sprintf(
									// Translators: %1$s: Quiz label.
									__(
										'This %1$s Reports block will be visible when the "%1$s Reports" tab is selected in the Report Tools Block.',
										'learndash-reports-pro'
									),
									quizLabel
								),
							} }
						/>
					) }
					{ this.renderContent() }
				</div>
			</div>
		);
	}
}

registerBlockType( 'wisdm-learndash-reports/quiz-reports', {
	title: sprintf(
		// Translators: %s: Quiz label.
		__( '%s Reports', 'learndash-reports-pro' ),
		getCustomLabel( 'Quiz' )
	),
	description: sprintf(
		// Translators: %s: courses label.
		__( 'A table containing a list of the %s', 'learndash-reports-pro' ),
		getCustomLabel( 'Courses', 'lower' )
	),
	category: 'wisdm-learndash-reports',
	className: 'learndash-reports-by-wisdmlabs-quiz-reports',
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
				<QuizReport></QuizReport>
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
				<div
					id="wisdm-learndash-reports-quiz-report-view"
					className="wisdm-learndash-reports-quiz-reports"
				>
					[ldrp_quiz_reports]
				</div>
			</div>
		);
	},
} );
