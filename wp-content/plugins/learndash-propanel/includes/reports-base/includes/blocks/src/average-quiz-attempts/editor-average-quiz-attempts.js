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
import { createElement } from '@wordpress/element';

import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';
import AverageQuizAttempts from './index-average-quiz-attempts.js';
import ApexCharts from 'apexcharts';
import { createHooks } from '@wordpress/hooks';
import ChartSummarySection from '../commons/chart-summary/index.js';
import WisdmFilters from '../commons/filters/index.js';

const globalHooks = createHooks();

import React, { Component } from 'react';
import Chart from 'react-apexcharts';
import { subscribe } from '@wordpress/data';
import {
	hasReportToolsBlock,
	getReportToolsBlockSettings,
} from '../commons/has-report-tools-block.js';

const icon = (
	<svg
		version="1.0"
		xmlns="http://www.w3.org/2000/svg"
		width="24.000000pt"
		height="24.000000pt"
		viewBox="0 0 24.000000 24.000000"
		preserveAspectRatio="xMidYMid meet"
	>
		<g>
			<path
				d="M9.5,12.1c-0.3-0.2-1,0-1.1,0.2L7,13.6l-1.3-1.3c-0.2-0.3-0.9-0.4-1.2-0.1c-0.3,0.4-0.1,1,0.1,1.1l1.3,1.3L4.6,16
                  c-0.2,0.2-0.4,0.7,0,1.1c0.3,0.4,0.9,0.1,1.1-0.1L7,15.8l1.3,1.3c0.2,0.2,0.8,0.3,1.2,0c0.3-0.3,0.1-1-0.1-1.1l-1.3-1.3l1.3-1.3
                  C9.7,13.2,9.9,12.5,9.5,12.1z M5.4,16.9L5.4,16.9L5.4,16.9L5.4,16.9z"
			/>
			<path
				d="M10.2,4.7C10,4.6,9.9,4.5,9.5,4.5C9.2,4.5,9,4.7,8.9,4.8L6,7.7l-1-1c-0.3-0.3-0.9-0.3-1.2,0C3.4,7,3.4,7.5,3.8,7.9l1.7,1.6
                  c0.2,0.2,0.4,0.3,0.6,0.3s0.4-0.1,0.6-0.3L10.2,6c0.2-0.2,0.3-0.4,0.3-0.6S10.4,4.9,10.2,4.7z"
			/>
		</g>
		<path
			d="M12,19.3H3.5c-1,0-1.8-0.8-1.8-1.8V4.8c0-1,0.8-1.8,1.8-1.8h14.4c1,0,1.8,0.8,1.8,1.8v4.7c0,0.4,0.3,0.8,0.8,0.8
                s0.8-0.3,0.8-0.8V4.8c-0.1-1.7-1.4-3-2.9-3.1l0,0h-15C1.6,1.8,0.2,3.2,0.2,4.8v12.8c0,1.8,1.5,3.2,3.2,3.2h8.5
                c0.4,0,0.8-0.3,0.8-0.8C12.7,19.7,12.4,19.3,12,19.3z"
		/>
		<g>
			<path
				d="M15.9,19.6L15.9,19.6c-0.2-0.1-0.4-0.4-0.1-0.6l3.4-6c0.1-0.2,0.4-0.4,0.6-0.1l0,0c0.2,0.1,0.4,0.4,0.1,0.6l-3.4,5.9
                  C16.4,19.6,16.2,19.7,15.9,19.6z"
			/>
			<path d="M15.3,13.5c-0.8,0-1.5,0.7-1.5,1.5s0.7,1.5,1.5,1.5c0.8,0,1.5-0.7,1.5-1.5C16.9,14.2,16.2,13.5,15.3,13.5z" />
			<path d="M20.3,16.2c-0.8,0-1.5,0.7-1.5,1.5s0.7,1.5,1.5,1.5s1.5-0.7,1.5-1.5S21.2,16.2,20.3,16.2z" />
		</g>
		<path
			d="M17.8,22.1c-3.3,0-6-2.7-6-6c0-3.3,2.7-6,6-6s6,2.7,6,6C23.7,19.5,21.1,22.1,17.8,22.1z M17.8,11c-2.9,0-5.2,2.3-5.2,5.2
                s2.3,5.2,5.2,5.2S23,19,23,16.2S20.6,11,17.8,11z"
		/>
	</svg>
);

/**
 * Helper function to check if a course is selected in Report Tools Block.
 *
 * @return {boolean} True if a course is selected, false otherwise.
 */
function hasCourseSelected() {
	const settings = getReportToolsBlockSettings();
	return (
		settings.selected_courses !== null &&
		settings.selected_courses.value !== null &&
		settings.selected_courses.value !== undefined &&
		settings.selected_courses.value > 0
	);
}

/**
 * Wrapper component for Average Quiz Attempts editor.
 * Handles Report Tools block detection and displays appropriate message.
 */
class AverageQuizAttemptsEditor extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			hasReportTools: hasReportToolsBlock(),
			hasCourseSelected: hasCourseSelected(),
		};
		this.unsubscribe = null;
		this.handleFiltersApplied = this.handleFiltersApplied.bind( this );
	}

	componentDidMount() {
		// Subscribe to block changes to detect when Report Tools block is added/removed
		this.unsubscribe = subscribe( () => {
			const hasReportTools = hasReportToolsBlock();
			const hasCourse = hasCourseSelected();
			if (
				hasReportTools !== this.state.hasReportTools ||
				hasCourse !== this.state.hasCourseSelected
			) {
				this.setState( {
					hasReportTools,
					hasCourseSelected: hasCourse,
				} );
			}
		} );

		// Listen for filter changes to detect course selection
		document.addEventListener(
			'wisdm-ld-reports-filters-applied',
			this.handleFiltersApplied
		);

		// Check initial state
		this.checkCourseSelection();
	}

	componentWillUnmount() {
		if ( this.unsubscribe ) {
			this.unsubscribe();
		}
		document.removeEventListener(
			'wisdm-ld-reports-filters-applied',
			this.handleFiltersApplied
		);
	}

	handleFiltersApplied( event ) {
		const hasCourse =
			event.detail &&
			event.detail.selected_courses !== null &&
			event.detail.selected_courses !== undefined &&
			event.detail.selected_courses > 0;
		if ( hasCourse !== this.state.hasCourseSelected ) {
			this.setState( { hasCourseSelected: hasCourse } );
		}
	}

	checkCourseSelection() {
		const hasCourse = hasCourseSelected();
		if ( hasCourse !== this.state.hasCourseSelected ) {
			this.setState( { hasCourseSelected: hasCourse } );
		}
	}

	render() {
		// Get custom labels
		const quizLabel =
			typeof wisdm_reports_get_ld_custom_lebel_if_avaiable === 'function'
				? wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' )
				: __( 'Quiz', 'learndash-reports-pro' );
		const courseLabel =
			typeof wisdm_reports_get_ld_custom_lebel_if_avaiable === 'function'
				? wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' )
				: __( 'Course', 'learndash-reports-pro' );

		// Get chart title and help text (same as frontend)
		const chartTitle =
			__( 'Average', 'learndash-reports-pro' ) +
			' ' +
			wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) + // cspell:disable-line
			' ' +
			__( 'Attempts', 'learndash-reports-pro' );
		const helpText =
			undefined == global.reportTypeForTooltip ||
			global.reportTypeForTooltip == 'default-course-reports'
				? sprintf(
						// Translators: %1$s: quizzes label, %2$s: course label.
						__(
							'This report displays the average attempts on the %1$s of this %2$s.',
							'learndash-reports-pro'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'Quizzes',
							'lower'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'Course',
							'lower'
						)
				  )
				: sprintf(
						// Translators: %s: quiz label.
						__(
							'This report shows the number of %s attempts for a particular learner.',
							'learndash-reports-pro'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'Quiz',
							'lower'
						)
				  );

		// Render logic:
		// - If Report Tools Block exists AND Course is selected → render <AverageQuizAttempts>
		// - If Report Tools Block exists AND Course is NOT selected → render custom notice
		// - If Report Tools Block does NOT exist → render <AverageQuizAttempts>
		if ( this.state.hasReportTools && ! this.state.hasCourseSelected ) {
			// Report Tools Block exists but no course selected - show notice
			const noticeMessage = sprintf(
				// Translators: %1$s: Quiz label, %2$s: Course label.
				__(
					'This Average %1$s Attempts block will be visible when a %2$s is selected in the Report Tools Block.',
					'learndash-reports-pro'
				),
				quizLabel,
				courseLabel
			);

			return (
				<div className="wisdm-learndash-reports-chart-block">
					<div className="wisdm-learndash-reports-average-quiz-attempts graph-card-container">
						<WisdmFilters request_data={ null } />
						<div className="chart-header average-quiz-attempts-chart-header">
							<div className="chart-title">
								<span>{ chartTitle }</span>
								<span
									className="dashicons dashicons-info-outline widm-ld-reports-info"
									data-title={ helpText }
								></span>
							</div>
							<ChartSummarySection
								wrapper_class="chart-summary-average-quiz-attempts"
								graph_summary={ { left: [], right: [] } }
								error={ { message: noticeMessage } }
							/>
						</div>
						<div></div>
					</div>
				</div>
			);
		}

		// Render <AverageQuizAttempts> when:
		// - Report Tools Block exists AND Course is selected, OR
		// - Report Tools Block does NOT exist
		return <AverageQuizAttempts></AverageQuizAttempts>;
	}
}

registerBlockType( 'wisdm-learndash-reports/average-quiz-attempts', {
	title: sprintf(
		// Translators: %s: Quiz label.
		__( 'Average %s Attempts', 'learndash-reports-pro' ),
		wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' )
	),
	description: __(
		'Graph of the learner pass/fail rate',
		'learndash-reports-pro'
	),
	category: 'wisdm-learndash-reports',
	className: 'learndash-reports-by-wisdmlabs-average-quiz-attempts',
	icon,
	apiVersion: '3',
	attributes: {
		categories: {
			type: 'object',
		},
		selectedCategory: {
			type: 'string',
		},
		chartContent: {
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
				<AverageQuizAttemptsEditor></AverageQuizAttemptsEditor>
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
				<div className="wisdm-learndash-reports-average-quiz-attempts front"></div>
			</div>
		);
	},
} );
