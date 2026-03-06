// cspell:ignore globalfilters

import './index.scss';
import ChartSummarySection from '../commons/chart-summary/index.js';
import WisdmFilters from '../commons/filters/index.js';
import WisdmLoader from '../commons/loader/index.js';
import React, { Component } from 'react';
import Chart from '../commons/chart-wrapper/index.js';
import moment from 'moment';
import { __, sprintf } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
import { hasReportToolsBlock } from '../commons/has-report-tools-block.js';

class AverageQuizAttempts extends Component {
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
			graph_type: 'bar',
			series: [],
			options: {},
			graph_summary: [],
			reportTypeInUse:
				wisdm_ld_reports_common_script_data.report_type,
			request_data: null,
			hidden: false,
			show_supporting_text: false,
			start_date: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			).unix(),
			end_date: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			).unix(),
			chart_title:
				__( 'Average', 'learndash-reports-pro' ) +
				' ' +
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ) + // cspell:disable-line
				' ' +
				__( 'Attempts', 'learndash-reports-pro' ),
			help_text:
				undefined == global.reportTypeForTooltip ||
				global.reportTypeForTooltip == 'default-course-reports'
					? sprintf(
						// Translators: %1$s: quizzes label, %2$s: course label.
						__(
							'This report displays the average attempts on the %1$s of this %2$s.',
							'learndash-reports-pro'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes', 'lower' ),
						wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course', 'lower' )
					)
					: sprintf(
						// Translators: %s: quiz label.
						__(
							'This report shows the number of %s attempts for a particular learner.',
							'learndash-reports-pro'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz', 'lower' )
					),
		};

		this.durationUpdated = this.durationUpdated.bind( this );
		this.applyFilters = this.applyFilters.bind( this );
		this.handleReportTypeChange = this.handleReportTypeChange.bind( this );
		this.plotChartTypeBy = this.plotChartTypeBy.bind( this );
		this.updateChart = this.updateChart.bind( this );
	}

	isValidGraphData() {
		// Check if options exists and is an object (not empty)
		if (
			undefined == this.state.options ||
			null == this.state.options ||
			typeof this.state.options !== 'object' ||
			Object.keys( this.state.options ).length === 0
		) {
			return false;
		}
		// Check if series exists, is an array, and has at least one element with data
		if (
			undefined == this.state.series ||
			null == this.state.series ||
			! Array.isArray( this.state.series ) ||
			this.state.series.length === 0 ||
			undefined == this.state.series[ 0 ] ||
			undefined == this.state.series[ 0 ].data ||
			! Array.isArray( this.state.series[ 0 ].data ) ||
			this.state.series[ 0 ].data.length === 0
		) {
			return false;
		}

		return true;
	}

	/**
	 * Based on the current user roles array this function decides wether a user is a group
	 * leader or an Administrator and returns the same.
	 */
	getUserType() {
		let userRoles =
			wisdm_ld_reports_common_script_data.user_roles;
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
		this.updateChart(
			'/rp/v1/average-quiz-attempts/?start_date=' +
				this.state.start_date +
				'&end_date=' +
				this.state.end_date
		);
		document.addEventListener( 'duration_updated', this.durationUpdated );
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
			this.manageBlockVisibility
		);
	}

	componentDidUpdate() {
		jQuery(
			'.wisdm-learndash-reports-average-quiz-attempts .mixed-chart'
		).prepend(
			jQuery(
				'.wisdm-learndash-reports-average-quiz-attempts .apexcharts-toolbar'
			)
		);
		jQuery(
			'.wisdm-learndash-reports-average-quiz-attempts .chart-title .dashicons, .wisdm-learndash-reports-average-quiz-attempts .chart-summary-revenue-figure .dashicons'
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
				'.wp-block-wisdm-learndash-reports-average-quiz-attempts',
				false
			);
		} else {
			// if ( 'learner-specific-course-reports' == event.detail.report_type ) {
			//   this.setState({'hidden':true});
			// }
			if ( ! this.state.hidden ) {
				wisdm_reports_change_block_visibility(
					'.wp-block-wisdm-learndash-reports-average-quiz-attempts',
					true
				);
			}
		}
	}

	manageBlockVisibility( event ) {
		if ( 'learner-specific-course-reports' == event.detail.report_type ) {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-average-quiz-attempts',
				false
			);
		} else {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-average-quiz-attempts',
				true
			);
		}
	}

	applyFilters( event ) {
		const start_date = event.detail.start_date;
		const end_date = event.detail.end_date;
		const category = event.detail.selected_categories;
		const group = event.detail.selected_groups;
		const course = event.detail.selected_courses;
		const lesson = event.detail.selected_lessons;
		const topic = event.detail.selected_topics;
		const learner = event.detail.selected_learners;
		if ( null != learner && learner > 0 ) {
			this.setState( {
				help_text:
					undefined == global.reportTypeForTooltip ||
					global.reportTypeForTooltip == 'default-course-reports'
						? sprintf(
							// Translators: %s: quizzes label.
							__(
								'This report displays the average attempts on the %s of this learner.',
								'learndash-reports-pro'
							),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes', 'lower' )
						)
						: sprintf(
							// Translators: %s: quiz label.
							__(
								'This report shows the number of %s attempts for a particular learner.',
								'learndash-reports-pro'
							),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz', 'lower' )
						),
			} );
		} else if ( null != topic && topic > 0 ) {
			this.setState( {
				help_text:
					undefined == global.reportTypeForTooltip ||
					global.reportTypeForTooltip == 'default-course-reports'
						? sprintf(
							// Translators: %1$s: quizzes label, %2$s: topic label.
							__(
								'This report displays the average attempts on the %1$s of this %2$s.',
								'learndash-reports-pro'
							),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes', 'lower' ),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Topic', 'lower' )
						)
						: sprintf(
							// Translators: %s: quiz label.
							__(
								'This report shows the number of %s attempts for a particular learner.',
								'learndash-reports-pro'
							),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz', 'lower' )
						),
			} );
		} else if ( null != lesson && lesson > 0 ) {
			this.setState( {
				help_text:
					undefined == global.reportTypeForTooltip ||
					global.reportTypeForTooltip == 'default-course-reports'
						? sprintf(
							// Translators: %1$s: quizzes label, %2$s: lesson label.
							__(
								'This report displays the average attempts on the %1$s of this %2$s.',
								'learndash-reports-pro'
							),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes', 'lower' ),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Lesson', 'lower' )
						)
						: sprintf(
							// Translators: %s: quiz label.
							__(
								'This report shows the number of %s attempts for a particular learner.',
								'learndash-reports-pro'
							),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz', 'lower' )
						),
			} );
		} else {
			this.setState( {
				help_text:
					undefined == global.reportTypeForTooltip ||
					global.reportTypeForTooltip == 'default-course-reports'
						? sprintf(
							// Translators: %1$s: quizzes label, %2$s: course label.
							__(
								'This report displays the average attempts on the %1$s of this %2$s.',
								'learndash-reports-pro'
							),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes', 'lower' ),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course', 'lower' )
						)
						: sprintf(
							// Translators: %s: quiz label.
							__(
								'This report shows the number of %s attempts for a particular learner.',
								'learndash-reports-pro'
							),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz', 'lower' )
						),
			} );
		}
		if ( undefined != course ) {
			this.setState( { show_supporting_text: true } );
		} else {
			this.setState( { show_supporting_text: false } );
		}
		const request_url =
			'/rp/v1/average-quiz-attempts/?start_date=' +
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

		this.updateChart( request_url );
	}

	durationUpdated( event ) {
		this.setState(
			{
				isLoaded: false,
				start_date: event.detail.startDate,
				end_date: event.detail.endDate,
			},
			() => {
				let requestUrl = '/rp/v1/average-quiz-attempts/';

				if ( event.type === 'duration_updated' ) {
					requestUrl =
						'/rp/v1/average-quiz-attempts/?start_date=' +
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

				this.updateChart( requestUrl );
			}
		);
	}

	updateChart( requestUrl ) {
		this.setState( { isLoaded: false, error: null, request_data: null } );

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
							if ( response.requestData ) {
								self.setState( {
									request_data: response.requestData,
								} );
							}
							wisdm_reports_change_block_visibility(
								'.wp-block-wisdm-learndash-reports-average-quiz-attempts',
								true
							);
							self.setState( { hidden: false } );
							self.plotChartTypeBy( response );
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
								graph_summary: [],
								series: [],
								isLoaded: true,
							} );
							// Always show the block, even on error - let the error message be displayed.
							wisdm_reports_change_block_visibility(
								'.wp-block-wisdm-learndash-reports-average-quiz-attempts',
								true
							);
							self.setState( { hidden: false } );
						} );
				}
			}, 500 );
		};
		checkIfEmpty();
	}

	plotChartTypeBy( response ) {
		// Handle error responses (when API returns { code: 'course-selection-required', message: '...' }).
		if ( response.code === 'course-selection-required' ) {
			window.callStack.pop();

			// Only hide the component if Report Tools Block exists on the page.
			// Otherwise, treat it like 'no-data' and show the error message.
			if ( hasReportToolsBlock() ) {
				// Hide the component when course selection is required and Report Tools Block exists
				this.setState( {
					hidden: true,
					isLoaded: true,
					graph_summary: [],
					series: [],
				} );

				if ( response.data && response.data.requestData ) {
					this.setState( {
						request_data: response.data.requestData,
					} );
				}

				wisdm_reports_change_block_visibility(
					'.wp-block-wisdm-learndash-reports-average-quiz-attempts',
					false
				);

				return;
			}

			// If Report Tools Block doesn't exist, treat it like 'no-data' and show error message
			this.setState( {
				error: {
					message: response.message,
				},
				graph_summary: [],
				series: [],
				isLoaded: true,
			} );

			if ( response.data && response.data.requestData ) {
				this.setState( {
					request_data: response.data.requestData,
				} );
			}

			return;
		}

		// Handle other error responses (when API returns { code: 'no-data', message: '...' }).
		if ( response.code === 'no-data' ) {
			window.callStack.pop();
			this.setState( {
				error: {
					message: response.message,
				},
				graph_summary: [],
				series: [],
				isLoaded: true,
			} );
			if ( response.data && response.data.requestData ) {
				this.setState( {
					request_data: response.data.requestData,
				} );
			}
			return;
		}

		// cspell:disable-next-line
		if ( undefined != response.quizwise_data ) {
			// cspell:disable-next-line
			const quizzes = Object.values( response.quizwise_data )
				.map( ( obj ) => obj.title )
				.filter( ( obj ) => obj != null );
			// cspell:disable-next-line
			const attempts = Object.values( response.quizwise_data )
				.map( ( obj ) => parseFloat( obj.count ) )
				.filter( ( obj ) => obj != null );

			this.plotBarChart(
				quizzes,
				attempts,
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes' ), // cspell:disable-line
				__( 'Attempts', 'learndash-reports-pro' )
			);
			const summary_right = Array();

			summary_right.push( {
				title:
					__( 'Total ', 'learndash-reports-pro' ) +
					wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes' ) + // cspell:disable-line
					': ',
				// cspell:disable-next-line
				value: Object.keys( response.quizwise_data ).length,
			} );
			if (
				response.requestData.learner == null ||
				response.requestData.learner.length == 0
			) {
				summary_right.push( {
					title: __( 'Learners', 'learndash-reports-pro' ) + ': ',
					value: response.student_count,
				} );
			}

			summary_right.push( {
				title: __( 'Total Attempts: ', 'learndash-reports-pro' ),
				value: response.total_attempts,
			} );

			this.setState( {
				isLoaded: true,
				graph_summary: {
					left: [
						{
							title:
								__(
									'AVG ATTEMPTS PER',
									'learndash-reports-pro'
								) +
								' ' +
								// cspell:disable-next-line
								wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Quiz'
								),
							value:
								'??' != response.average_attempts
									? Number(
											parseFloat(
												response.average_attempts
											).toFixed( 2 )
									  )
									: response.average_attempts,
						},
					],

					right: summary_right,
					inner_help_text: sprintf(
						// Translators: %1$s: Quiz label, %2$s: quizzes label, %3$s: quizzes label.
						__(
							'Avg %1$s Attempts = Avg attempts made to pass all %2$s/No. of %3$s',
							'learndash-reports-pro'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' ),
						wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes', 'lower' ),
						wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes', 'lower' )
					),
				},
			} );
		}
	}

	plotBarChart( dataX, dataY, nameX, nameY ) {
		if ( wisdm_ld_reports_common_script_data.is_rtl ) {
			dataX = dataX.reverse();
			dataY = dataY.reverse();
		}
		const chart_options = {
			chart: {
				type: 'bar',
				width: dataX.length * 75 < 645 ? '100%' : dataX.length * 75,
				height: 400,
				zoom: {
					enabled: false,
				},
				toolbar: {
					show: true,
					export: {
						csv: {
							filename: sprintf(
								// Translators: Quiz label.
								__(
									'Avg %s Attempts.csv',
									'learndash-reports-pro'
								),
								wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' )
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
			},
			zoom: {
				enabled: true,
				type: 'x',
			},
			fill: {
				colors: [
					function ( { value, seriesIndex, w } ) {
						return '#444444';
					},
				],
			},
			dataLabels: {
				enabled: true,
				formatter( val ) {
					return val;
				},
				offsetY: -25,
				style: {
					fontSize: '12px',
					colors: [ '#304758' ],
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
					rotate: wisdm_ld_reports_common_script_data.is_rtl
						? 45
						: -45,
				},
				tickPlacement: 'on',
				min: 1,
			},
			yaxis: {
				max:
					Math.max.apply( Math, dataY ) > 0
						? Math.max.apply( Math, dataY ) +
						  Math.max.apply( Math, dataY ) / 10
						: 10,
				axisBorder: {
					show: false,
				},
				title: {
					text: nameY,
					offsetX: -20,
				},
				opposite: wisdm_ld_reports_common_script_data.is_rtl,
				tickAmount: 5,
				min: 0,
				max: 100,
				labels: {
					show: true,
					formatter: ( value ) => {
						return value;
					},
					align: wisdm_ld_reports_common_script_data.is_rtl
						? 'right'
						: 'left',
					offsetX: 30,
				},
			},
		};
		this.setState( {
			graph_type: 'bar',
			series: [ { name: nameY, data: dataY } ],
			options: chart_options,
		} );
	}

	render() {
		let body = <div></div>;
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
			// console.log(this.state.error);

			let graph = '';
			if ( ! this.state.error && this.isValidGraphData() ) {
				graph = (
					<div className="app">
						<div className="row">
							<div className="mixed-chart">
								<Chart
									options={ this.state.options }
									series={ this.state.series }
									type={ this.state.graph_type }
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
								/>
							</div>
						</div>
					</div>
				);
			}
			body = (
				<div
					className={
						'wisdm-learndash-reports-chart-block ' + data_validation
					}
				>
					<div className="wisdm-learndash-reports-average-quiz-attempts graph-card-container">
						<WisdmFilters
							request_data={ this.state.request_data }
						/>
						<div className="chart-header average-quiz-attempts-chart-header">
							<div className="chart-title">
								<span>{ this.state.chart_title }</span>
								<span
									className="dashicons dashicons-info-outline widm-ld-reports-info"
									data-title={ this.state.help_text }
								></span>
							</div>
							<ChartSummarySection
								wrapper_class="chart-summary-average-quiz-attempts"
								graph_summary={ this.state.graph_summary }
								error={ this.state.error }
							/>
						</div>
						<div>{ graph }</div>
					</div>
				</div>
			);
		}

		return body;
	}
}

export default AverageQuizAttempts;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-average-quiz-attempts front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( AverageQuizAttempts ) );
	}
} );
