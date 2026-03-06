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
class LearnerPassFailRate extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			isLoaded: false,
			error: null,
			graph_type: 'bar',
			lock_icon: '',
			series: [],
			options: {},
			graph_summary: [],
			start_date: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			).unix(),
			end_date: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			).unix(),
			reportTypeInUse: wisdm_ld_reports_common_script_data.report_type,
			request_data: null,
			show_supporting_text: false,
			chart_title: __( 'Learner Pass Rate', 'learndash-reports-pro' ),
			help_text: sprintf(
				// Translators: %1$s: quiz label, %2$s: courses label.
				__(
					'This report displays the Pass %% of %1$s attempts in %2$s.',
					'learndash-reports-pro'
				),
				wisdm_reports_get_ld_custom_lebel_if_avaiable(
					'Quiz',
					'lower'
				),
				wisdm_reports_get_ld_custom_lebel_if_avaiable(
					'Courses',
					'lower'
				)
			),
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

		this.durationUpdated = this.durationUpdated.bind( this );
		this.applyFilters = this.applyFilters.bind( this );
		this.handleReportTypeChange = this.handleReportTypeChange.bind( this );
	}

	isValidGraphData() {
		if (
			undefined == this.state.options ||
			0 == this.state.options.length
		) {
			return false;
		}
		if (
			undefined == this.state.series ||
			undefined == this.state.series[ 0 ]
		) {
			return false;
		}

		return true;
	}

	componentDidMount() {
		this.updateChart(
			'/rp/v1/learner-pass-fail-rate-per-course/?start_date=' +
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
			'.wisdm-learndash-reports-learner-pass-fail-rate-per-course .mixed-chart'
		).prepend(
			jQuery(
				'.wisdm-learndash-reports-learner-pass-fail-rate-per-course .apexcharts-toolbar'
			)
		);
		jQuery(
			'.wisdm-learndash-reports-learner-pass-fail-rate-per-course .chart-title .dashicons, .wisdm-learndash-reports-learner-pass-fail-rate-per-course .chart-summary-revenue-figure .dashicons'
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
				'.wp-block-wisdm-learndash-reports-learner-pass-fail-rate-per-course',
				false
			);
		} else if (
			'learner-specific-course-reports' != event.detail.report_type
		) {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-learner-pass-fail-rate-per-course',
				true
			);
		}
	}

	manageBlockVisibility( event ) {
		if ( 'learner-specific-course-reports' == event.detail.report_type ) {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-learner-pass-fail-rate-per-course',
				false
			);
		} else {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-learner-pass-fail-rate-per-course',
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
		if ( undefined != course ) {
			this.setState( { show_supporting_text: true } );
		} else {
			this.setState( { show_supporting_text: false } );
		}
		const request_url =
			'/rp/v1/learner-pass-fail-rate-per-course/?start_date=' +
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
				let requestUrl = '/rp/v1/learner-pass-fail-rate-per-course/';

				if ( event.type === 'duration_updated' ) {
					requestUrl =
						'/rp/v1/learner-pass-fail-rate-per-course/?start_date=' +
						event.detail.startDate +
						'&end_date=' +
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

		// Initialize callStack if not present (when block is used without report-filters).
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
								self.setState(
									{
										request_data: response.requestData,
									},
									() => {
										self.plotChartTypeBy( response );
									}
								);
							} else {
								self.plotChartTypeBy( response );
							}
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
						} );
				}
			}, 500 );
		};
		checkIfEmpty();
	}

	plotChartTypeBy( response ) {
		// Handle error responses (when API returns { code: 'no-data', message: '...' }).
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
		if ( undefined != response.coursewise_data ) {
			// cspell:disable-next-line
			const courses = Object.values( response.coursewise_data )
				.map( ( obj ) => obj.title )
				.filter( ( obj ) => obj != null );
			// cspell:disable-next-line
			const percentages = Object.values( response.coursewise_data )
				.map( ( obj ) => parseFloat( obj.percentage ) )
				.filter( ( obj ) => obj != null );

			this.plotBarChart(
				courses,
				percentages,
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses' ),
				__( 'Learner Pass Rate - ', 'learndash-reports-pro' ) +
					' ' +
					// cspell:disable-next-line
					wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes' )
			);
			this.setState( {
				isLoaded: true,
				graph_summary: {
					left: [
						{
							title:
								__(
									'Avg Pass Rate per',
									'learndash-reports-pro'
								) +
								' ' +
								// cspell:disable-next-line
								wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Course'
								),
							value:
								Math.round( response.average_pass_rate * 100 ) /
									100 +
								'%',
						},
					],

					right: [
						{
							title:
								__( 'Total', 'learndash-reports-pro' ) +
								' ' +
								// cspell:disable-next-line
								wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Quizzes'
								) +
								': ',
							value: response.quiz_count,
						},
						{
							title:
								__(
									'Total (Pass & Fail) Attempts',
									'learndash-reports-pro'
								) + ': ',
							value: response.quiz_attempt_count,
						},
						{
							title:
								__(
									'Total Pass Attempts',
									'learndash-reports-pro'
								) + ': ',
							value: response.quiz_pass_attempt_count,
						},
						{
							title: __( 'Learners: ', 'learndash-reports-pro' ),
							value: response.learner_count,
						},
					],
					inner_help_text: sprintf(
						// Translators: %1$s: courses label, %2$s: courses label.
						__(
							'Avg Pass Rate = Ratio of (Pass Attempts/Total Attempts) in all %1$s/No. of %2$s',
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
					// Translators: %1$s: quiz label, %2$s: courses label.
					__(
						'This report displays the Pass %% of %1$s attempts in %2$s.',
						'learndash-reports-pro'
					),
					wisdm_reports_get_ld_custom_lebel_if_avaiable(
						'Quiz',
						'lower'
					),
					wisdm_reports_get_ld_custom_lebel_if_avaiable(
						'Courses',
						'lower'
					)
				),
			} );
			// cspell:disable-next-line
		} else if ( undefined != response.quizwise_data ) {
			let pass = 0;
			let fail = 0;
			// cspell:disable-next-line
			jQuery.each( response.quizwise_data, function ( i, value ) {
				pass = pass + value.pass;
				fail = fail + ( value.total - value.pass );
			} );

			let data = [];
			if ( 0 !== pass || 0 !== fail ) {
				data = [ pass, fail ];
			} else {
				this.setState( {
					error: {
						message: __(
							'No data found for the selected filters',
							'learndash-reports-pro'
						),
					},
				} );
			}

			this.plotPieChart( data, [
				__( 'Passed', 'learndash-reports-pro' ),
				__( 'Failed', 'learndash-reports-pro' ),
			] );
			if ( this.state.request_data.lesson == '' ) {
				this.setState( {
					isLoaded: true,
					graph_summary: {
						left: [
							{
								title: __(
									'PASS RATE',
									'learndash-reports-pro'
								),
								value:
									parseFloat(
										( pass /
											response.total_quiz_attempts ) *
											100
									).toFixed( 2 ) + '%',
							},
						],

						right: [
							{
								title:
									__( 'Total ', 'learndash-reports-pro' ) +
									' ' +
									// cspell:disable-next-line
									wisdm_reports_get_ld_custom_lebel_if_avaiable(
										'Quizzes'
									) +
									': ',
								// cspell:disable-next-line
								value: Object.keys( response.quizwise_data )
									.length,
							},
							{
								title: __(
									'No. of Attempts: ',
									'learndash-reports-pro'
								),
								value: response.total_quiz_attempts,
							},
							{
								title: __(
									'No. of Attempts - Passed: ',
									'learndash-reports-pro'
								),
								value: response.pass_quiz_attempts,
							},
							{
								title: __(
									'No. of Attempts - Not Passed: ',
									'learndash-reports-pro'
								),
								value:
									parseInt( response.total_quiz_attempts ) -
									parseInt( response.pass_quiz_attempts ),
							},
						],
						inner_help_text: __(
							'Pass Rate = 100 * Pass Attempts/Total Attempts',
							'learndash-reports-pro'
						),
					},
					help_text: sprintf(
						// Translators: %1$s: quizzes label, %2$s: course label.
						__(
							'This report displays the pass %% of the total attempts on the %1$s in this %2$s.',
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
					),
					graphData: {
						averageResult: response.avg_quiz_attempts + '%',
						// cspell:disable-next-line
						noOfLearnersCompletedTheQuizes: '-',
						// cspell:disable-next-line
						completedQuizes: '-',
						// cspell:disable-next-line
						totalQuizes: response.total_quiz_attempts,
					},
				} );
			} else if (
				this.state.request_data.lesson != '' &&
				this.state.request_data.topic != ''
			) {
				this.setState( {
					isLoaded: true,
					graph_summary: {
						left: [
							{
								title: __(
									'PASS RATE',
									'learndash-reports-pro'
								),
								value:
									parseFloat(
										( pass /
											response.total_quiz_attempts ) *
											100
									).toFixed( 2 ) + '%',
							},
						],

						right: [
							{
								title:
									__( 'Total ', 'learndash-reports-pro' ) +
									' ' +
									// cspell:disable-next-line
									wisdm_reports_get_ld_custom_lebel_if_avaiable(
										'Quizzes'
									) +
									': ',
								// cspell:disable-next-line
								value: Object.keys( response.quizwise_data )
									.length,
							},
							{
								title: __(
									'No. of Attempts: ',
									'learndash-reports-pro'
								),
								value: response.total_quiz_attempts,
							},
						],
						inner_help_text: __(
							'Pass Rate = 100 * Pass Attempts/Total Attempts',
							'learndash-reports-pro'
						),
					},
					help_text: sprintf(
						// Translators: %1$s: quizzes label, %2$s: topic label.
						__(
							'This report displays the pass %% of the total attempts on the %1$s in this %2$s.',
							'learndash-reports-pro'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'Quizzes',
							'lower'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'Topic',
							'lower'
						)
					),
					graphData: {
						averageResult: response.avg_quiz_attempts + '%',
						// cspell:disable-next-line
						noOfLearnersCompletedTheQuizes: '-',
						// cspell:disable-next-line
						completedQuizes: '-',
						// cspell:disable-next-line
						totalQuizes: response.total_quiz_attempts,
					},
				} );
			} else if ( this.state.request_data.topic == '' ) {
				this.setState( {
					isLoaded: true,
					graph_summary: {
						left: [
							{
								title: __(
									'PASS RATE',
									'learndash-reports-pro'
								),
								value:
									parseFloat(
										( pass /
											response.total_quiz_attempts ) *
											100
									).toFixed( 2 ) + '%',
							},
						],

						right: [
							{
								title:
									__( 'Total ', 'learndash-reports-pro' ) +
									' ' +
									// cspell:disable-next-line
									wisdm_reports_get_ld_custom_lebel_if_avaiable(
										'Quizzes'
									) +
									': ',
								// cspell:disable-next-line
								value: Object.keys( response.quizwise_data )
									.length,
							},
							{
								title: __(
									'No. of Attempts: ',
									'learndash-reports-pro'
								),
								value: response.total_quiz_attempts,
							},
						],
						inner_help_text: __(
							'Pass Rate = 100 * Pass Attempts/Total Attempts',
							'learndash-reports-pro'
						),
					},
					help_text: sprintf(
						// Translators: %1$s: quizzes label, %2$s: lesson label.
						__(
							'This report displays the pass %% of the total attempts on the %1$s in this %2$s.',
							'learndash-reports-pro'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'Quizzes',
							'lower'
						),
						wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'Lesson',
							'lower'
						)
					),
					graphData: {
						averageResult: response.avg_quiz_attempts + '%',
						// cspell:disable-next-line
						noOfLearnersCompletedTheQuizes: '-',
						// cspell:disable-next-line
						completedQuizes: '-',
						// cspell:disable-next-line
						totalQuizes: response.total_quiz_attempts,
					},
				} );
			}
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
							filename: __(
								'Learner Pass Fail Rate.csv',
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
					return val + '%';
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
					show: ! wisdm_ld_reports_common_script_data.is_rtl,
				},
				title: {
					text: nameY,
					offsetX: wisdm_ld_reports_common_script_data.is_rtl
						? -150
						: 0,
				},
				tickAmount: 5,

				labels: {
					show: false,
					// formatter: (value) => { return value+'% ' },
					// align: wisdm_ld_reports_common_script_data.is_rtl ? 'right' : 'left',
					// offsetX: wisdm_ld_reports_common_script_data.is_rtl ? -120 : 0,
				},
				opposite: wisdm_ld_reports_common_script_data.is_rtl,
			},
		};
		this.setState( {
			graph_type: 'bar',
			series: [ { name: nameY, data: dataY } ],
			options: chart_options,
		} );
	}

	plotPieChart( data, labels ) {
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
				width: '80%',
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
				},
			},
			stroke: {
				width: 0,
			},
			labels,
			responsive: [
				{
					breakpoint: 480,
					options: {
						chart: {
							width: 200,
						},
						legend: {
							formatter( seriesName, opts ) {
								return [
									seriesName,
									' - ',
									opts.w.globals.series[ opts.seriesIndex ],
									'',
								];
							},
							position: 'bottom',
						},
					},
				},
			],
		};

		this.setState( {
			graph_type: 'donut',
			series: data,
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
			let graph = '';
			if ( ! this.state.error ) {
				graph = (
					<div className="app">
						<div className="row">
							<div className="mixed-chart">
								<Chart
									options={ this.state.options }
									series={ this.state.series }
									type={ this.state.graph_type }
									width={ this.state.options.chart.width }
									height={ this.state.options.chart.height }
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
					<div className="wisdm-learndash-reports-learner-pass-fail-rate-per-course graph-card-container">
						<WisdmFilters
							request_data={ this.state.request_data }
						/>
						<div className="chart-header learner-pass-fail-rate-per-course-chart-header">
							<div className="chart-title">
								<span>{ this.state.chart_title }</span>
								<span
									className="dashicons dashicons-info-outline widm-ld-reports-info"
									data-title={ this.state.help_text }
								></span>
							</div>
							<ChartSummarySection
								pro_upgrade_option={ this.upgdare_to_pro } // cspell:disable-line
								wrapper_class="chart-summary-learner-pass-fail-rate-per-course"
								graph_summary={ this.state.graph_summary }
								error={ this.state.error }
								pro_link="https://go.learndash.com/ppaddon"
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

export default LearnerPassFailRate;

// cspell:disable-next-line
function wrld_learner_pass_fail_rate_block_visibile( visible = true ) {
	if ( visible ) {
		jQuery(
			'.wp-block-wisdm-learndash-reports-learner-pass-fail-rate-per-course'
		).css( 'display', 'block' );
	} else {
		jQuery(
			'.wp-block-wisdm-learndash-reports-learner-pass-fail-rate-per-course'
		).css( 'display', 'none' );
	}
}

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-learner-pass-fail-rate-per-course front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( LearnerPassFailRate ) );
	}
} );
