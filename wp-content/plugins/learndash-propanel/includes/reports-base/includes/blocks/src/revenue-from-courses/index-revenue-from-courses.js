import './index.scss';
import ChartSummarySection from '../commons/chart-summary/index.js';
import WisdmFilters from '../commons/filters/index.js';
import WisdmLoader from '../commons/loader/index.js';
import React, { Component } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
import Chart from '../commons/chart-wrapper/index.js';
import moment from 'moment';

class RevenueFromCourses extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			isLoaded: false,
			error: null,
			reportTypeInUse:
				wisdm_ld_reports_common_script_data.report_type,
			options: [],
			series: [],
			graph_summary: [],
			request_data: null,
			start_date: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			).unix(),
			end_date: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			).unix(),
			chart_title:
				__( 'Revenue From', 'learndash-reports-pro' ) +
				' ' +
				// cspell:disable-next-line
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses' ),
			help_text: sprintf(
				// Translators: %s: course label.
				__(
					'This report displays the revenue earned from each %s for the selected date range.',
					'learndash-reports-pro'
				),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'course', 'lower' )
			),
		};

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
		if (
			undefined == this.state.series[ 0 ].data ||
			0 == this.state.series[ 0 ].data.length
		) {
			return false;
		}
		return true;
	}

	componentDidMount() {
		this.updateChart(
			'/rp/v1/revenue-from-courses/?start_date=' +
				this.state.start_date +
				'&&end_date=' +
				this.state.end_date
		);
		document.addEventListener( 'duration_updated', this.durationUpdated );
		document.addEventListener(
			'wisdm-ld-reports-report-type-selected',
			this.handleReportTypeChange
		);
	}
	componentDidUpdate() {
		jQuery( '.revenue-from-courses .mixed-chart' ).prepend(
			jQuery( '.revenue-from-courses .apexcharts-toolbar' )
		);
		jQuery(
			'.wisdm-learndash-reports-revenue-from-courses .chart-title .dashicons'
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
	}

	applyFilters( event ) {
		const start_date = event.detail.start_date;
		const end_date = event.detail.end_date;
		const category = event.detail.selected_categories;
		const course = event.detail.selected_courses;
		const lesson = event.detail.selected_lessons;
		const topic = event.detail.selected_topics;
		const learner = event.detail.selected_learners;

		const request_url =
			'/rp/v1/revenue-from-courses/?start_date=' +
			start_date +
			'&end_date=' +
			end_date;

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
				let requestUrl =
					'/rp/v1/revenue-from-courses/?start_date=' +
					this.state.start_date +
					'&&end_date=' +
					this.state.end_date;

				if ( event.type === 'duration_updated' ) {
					requestUrl =
						'/rp/v1/revenue-from-courses/?start_date=' +
						event.detail.startDate +
						'&&end_date=' +
						event.detail.endDate;
				}

				this.updateChart( requestUrl );
			}
		);
	}

	updateChart( requestUrl ) {
		wisdm_reports_change_block_visibility(
			'.wisdm-learndash-reports-revenue-from-courses',
			true,
			'.wp-block-wisdm-learndash-reports-revenue-from-courses'
		);
		this.setState( { isLoaded: false, error: null, request_data: null } );
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			requestUrl +=
				'&wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		wp.apiFetch( {
			path: requestUrl, //Replace with the correct API
		} )
			.then( ( response ) => {
				let revenue = 0;
				let previousRevenue = 0;
				let courses = [];
				const formattedRevenueData =
					wisdm_reports_format_course_revenue_response(
						response.currentRevenueEarned,
						response.previousRevenueEarned
					);
				if ( response.requestData ) {
					this.setState( { request_data: response.requestData } );
				}
				if (
					undefined != formattedRevenueData.titles &&
					formattedRevenueData.titles.length > 0
				) {
					courses = formattedRevenueData.titles;
					previousRevenue = formattedRevenueData.past_revenues;
					revenue = formattedRevenueData.current_revenues;
				}

				let currency = response.currency;
				currency = currency.length > 1 ? currency + ' ' : currency;
				let dataNotFoundText = '';
				if (
					0 == response.currentRevenueEarned.length &&
					0 == response.previousRevenueEarned.length
				) {
					revenue = [];
					previousRevenue = [];
					courses = [];
					dataNotFoundText = __(
						'Revenue data not found',
						'learndash-reports-pro'
					);
				}
				if ( wisdm_ld_reports_common_script_data.is_rtl ) {
					previousRevenue = previousRevenue.reverse();
					// dataY = dataY.reverse();
				}
				this.setState( {
					isLoaded: true,
					series: [
						{
							name: __( 'Current', 'learndash-reports-pro' ),
							type: 'area',
							data: revenue,
							style: {
								direction:
									wisdm_ld_reports_common_script_data.is_rtl
										? 'rtl'
										: 'ltr',
							},
						},
						{
							name: __( 'Past', 'learndash-reports-pro' ),
							type: 'line',
							data: previousRevenue,
						},
					],
					options: {
						noData: {
							text: dataNotFoundText,
							align: 'center',
							verticalAlign: 'middle',
						},
						chart: {
							height: 400,
							// width:revenue.length*55 < 645 ? '100%' : revenue.length*55,
							zoom: {
								enabled: false,
							},
							toolbar: {
								export: {
									csv: {
										filename: sprintf(
											// Translators: %: Courses label.
											__(
												'Revenue From %s.csv',
												'learndash-reports-pro'
											),
											wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses' )
										),
										columnDelimiter: ',',
										headerCategory:
											// cspell:disable-next-line
											wisdm_reports_get_ld_custom_lebel_if_avaiable(
												'Courses'
											),
										headerValue:
											__(
												'Revenue per',
												'learndash-reports-pro'
											) +
											' ' +
											// cspell:disable-next-line
											wisdm_reports_get_ld_custom_lebel_if_avaiable(
												'course'
											),
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
						dataLabels: {
							enabled: false,
						},
						stroke: {
							curve: 'smooth',
							width: 1,
						},
						fill: {
							type: 'gradient',
							gradient: {
								shadeIntensity: 1,
								opacityFrom: 0.7,
								opacityTo: 0.9,
								stops: [ 0, 90, 100 ],
							},
						},
						colors: [ '#2E93fA', '#080808' ],
						xaxis: {
							tooltip: {
								enabled: false,
							},
							labels: {
								hideOverlappingLabels: false,
								trim: true,
								rotate: wisdm_ld_reports_common_script_data.is_rtl
									? 45
									: -45,
							},
							categories: courses,
							title: {
								// cspell:disable-next-line
								text: wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'courses'
								),
							},
						},
						yaxis: {
							axisBorder: {
								show: ! wisdm_ld_reports_common_script_data.is_rtl,
							},
							title: {
								text: sprintf(
									// Translators: %s: Course label.
									__(
										'%s revenue',
										'learndash-reports-pro'
									),
									wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' )
								),
								offsetX:
									wisdm_ld_reports_common_script_data.is_rtl
										? -80
										: 0,
							},
							labels: {
								align: wisdm_ld_reports_common_script_data.is_rtl
									? 'right'
									: 'left',
								offsetX:
									wisdm_ld_reports_common_script_data.is_rtl
										? 25
										: 0,
							},
							opposite:
								wisdm_ld_reports_common_script_data.is_rtl,
						},
						tooltip: {
							x: {
								format: 'dd/MM/yy HH:mm',
							},
						},
					},
					graph_summary: {
						left: [
							{
								title:
									__(
										'AVG REVENUE PER',
										'learndash-reports-pro'
									) +
									' ' +
									// cspell:disable-next-line
									wisdm_reports_get_ld_custom_lebel_if_avaiable(
										'course'
									).toUpperCase(),
								value: currency + response.averageRevenue,
							},
						],

						right: [
							{
								title: __(
									'Total Revenue: ',
									'learndash-reports-pro'
								),
								value: currency + response.totalRevenue,
							},
							{
								title:
									__( 'Total ', 'learndash-reports-pro' ) +
									' ' +
									// cspell:disable-next-line
									wisdm_reports_get_ld_custom_lebel_if_avaiable(
										'courses'
									) +
									': ',
								value: response.totalCourses,
							},
						],
					},
				} );
			} )
			.catch( ( error ) => {
				if ( error.data && error.data.requestData ) {
					this.setState( { request_data: error.data.requestData } );
				}
				if ( 'rest_forbidden' == error.code ) {
					wisdm_reports_change_block_visibility(
						'.wisdm-learndash-reports-revenue-from-courses',
						false,
						'.wp-block-wisdm-learndash-reports-revenue-from-courses'
					);
				}
				this.setState( {
					error,
					graph_summary: [],
					isLoaded: true,
					series: [],
				} );
			} );
	}

	render() {
		let body = <div></div>;
		let data_validation = '';
		if ( ! this.isValidGraphData() ) {
			data_validation = 'invalid-or-empty-data';
		}
		if ( ! this.state.isLoaded ) {
			// yet loading
			body = <WisdmLoader />;
		} else {
			let graph = '';
			if ( ! this.state.error ) {
				graph = (
					<div className="revenue-from-courses">
						<div className="mixed-chart">
							<Chart
								options={ this.state.options }
								series={ this.state.series }
								width={ this.state.options.chart.width }
								height={ this.state.options.chart.height }
								type="area"
							/>
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
					<div className="wisdm-learndash-reports-revenue-from-courses graph-card-container">
						<WisdmFilters
							request_data={ this.state.request_data }
						/>
						<div className="chart-header revenue-from-courses-chart-header">
							<div className="chart-title">
								<span>{ this.state.chart_title }</span>
								<span
									className="dashicons dashicons-info-outline widm-ld-reports-info"
									data-title={ this.state.help_text }
								></span>
							</div>
							<ChartSummarySection
								wrapper_class="chart-summary-revenue-from-courses"
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

export default RevenueFromCourses;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-revenue-from-courses front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( RevenueFromCourses ) );
	}
} );
