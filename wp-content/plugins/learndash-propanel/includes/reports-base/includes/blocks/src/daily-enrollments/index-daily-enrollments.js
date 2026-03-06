import './index.scss';
import ChartSummarySection from '../commons/chart-summary/index.js';
import WisdmFilters from '../commons/filters/index.js';
import WisdmLoader from '../commons/loader/index.js';
import React, { Component } from 'react';
import Chart from '../commons/chart-wrapper/index.js';
import moment from 'moment';
import { __ } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
class DailyEnrollments extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			isLoaded: false,
			error: null,
			series: [],
			options: [],
			graph_summary: [],
			reportTypeInUse:
				wisdm_ld_reports_common_script_data.report_type,
			request_data: null,
			start_date: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			).unix(),
			end_date: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			).unix(),
			chart_title: __( 'Daily Enrollments', 'learndash-reports-pro' ),
			help_text: __(
				'This Report displays the total number of daily learner enrollments for the selected date range.',
				'learndash-reports-pro'
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
			'/rp/v1/daily-enrollments/?start_date=' +
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
		jQuery( '.chart-daily-enrollments #chart-line' ).prepend(
			jQuery( '.chart-daily-enrollments .apexcharts-toolbar' )
		);
		jQuery(
			'.wisdm-learndash-reports-daily-enrollments .chart-title .dashicons'
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
		const group = event.detail.selected_groups;
		const course = event.detail.selected_courses;
		const lesson = event.detail.selected_lessons;
		const topic = event.detail.selected_topics;
		const learner = event.detail.selected_learners;

		const request_url =
			'/rp/v1/daily-enrollments/?start_date=' +
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
				let requestUrl = '/rp/v1/daily-enrollments/';

				if ( event.type === 'duration_updated' ) {
					requestUrl =
						'/rp/v1/daily-enrollments/?start_date=' +
						event.detail.startDate +
						'&&end_date=' +
						event.detail.endDate;
				}

				this.updateChart( requestUrl );
			}
		);
	}

	updateChart( requestUrl ) {
		this.setState( { isLoaded: false, error: false, request_data: null } );
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			requestUrl +=
				'&wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		wp.apiFetch( {
			path: requestUrl, //Replace with the correct API
		} )
			.then( ( response ) => {
				let dates = Object.values( response.enrollments ).map(
					( obj ) => obj.date
				);
				let count = Object.values( response.enrollments ).map(
					( obj ) => parseInt( obj.count )
				);
				let totalEnrollments = 0;
				if ( response.requestData ) {
					this.setState( { request_data: response.requestData } );
				}
				count.forEach( ( enrollments ) => {
					totalEnrollments = totalEnrollments + enrollments;
				} );
				if ( wisdm_ld_reports_common_script_data.is_rtl ) {
					dates = dates.reverse();
					count = count.reverse();
				}

				this.setState( {
					isLoaded: true,
					series: [
						{
							name: __(
								'Enrolled Learners',
								'learndash-reports-pro'
							),
							data: count,
						},
					],
					options: {
						stroke: {
							width: 2,
							curve: 'smooth',
							dashArray: 3,
						},
						markers: {
							size: 5,
							strokeColors: '#565656',
							strokeWidth: 3,
							strokeOpacity: 0.9,
							fillColor: '#fff',
							fillOpacity: 0.1,
							shape: 'circle',
							hover: {
								size: undefined,
								sizeOffset: 2,
							},
						},
						chart: {
							id: 'daily-enrollments-chart',
							group: 'social',
							type: 'line',
							height: 400,
							width:
								count.length * 55 < 645
									? '100%'
									: count.length * 55,
							zoom: {
								enabled: false,
							},
							toolbar: {
								export: {
									csv: {
										filename: __(
											'Daily Enrollments.csv',
											'learndash-reports-pro'
										),
										columnDelimiter: ',',
										headerCategory: __(
											'Dates',
											'learndash-reports-pro'
										),
										headerValue: __(
											'Enrollments',
											'learndash-reports-pro'
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
						colors: [ '#008FFB' ],
						yaxis: {
							axisBorder: {
								show: true,
							},
							labels: {
								minWidth: 40,
								align: wisdm_ld_reports_common_script_data.is_rtl
									? 'right'
									: 'left',
							},
							title: {
								text: __(
									'Learner Enrollments',
									'learndash-reports-pro'
								),
								offsetX:
									wisdm_ld_reports_common_script_data.is_rtl
										? -50
										: 0,
							},
							opposite:
								wisdm_ld_reports_common_script_data.is_rtl,
						},
						xaxis: {
							tooltip: {
								enabled: false,
							},
							categories: dates,
							title: {
								text: __( 'Date', 'learndash-reports-pro' ),
							},
							labels: {
								hideOverlappingLabels: false,
								trim: true,
								rotate: wisdm_ld_reports_common_script_data.is_rtl
									? 45
									: -45,
							},
							tickPlacement: 'on',
							min: 1,
							// max:dates.length>7?7:dates.length,
						},
					},
					graph_summary: {
						left: [
							{
								title: __(
									'AVG DAILY ENROLLMENTS',
									'learndash-reports-pro'
								),
								value: response.averageEnrollment,
							},
						],

						right: [
							{
								title: __(
									'Duration: ',
									'learndash-reports-pro'
								),
								value:
									dates.length +
									__( ' Days', 'learndash-reports-pro' ),
							},
							{
								title: __(
									'Total Enrollments: ',
									'learndash-reports-pro'
								),
								value: totalEnrollments,
							},
						],
					},
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
					<div className="chart-daily-enrollments">
						<div id="chart-line">
							<Chart
								options={ this.state.options }
								series={ this.state.series }
								width={ this.state.options.chart.width }
								height={ this.state.options.chart.height }
								type="line"
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
					<div className="wisdm-learndash-reports-daily-enrollments graph-card-container">
						<WisdmFilters
							request_data={ this.state.request_data }
						/>
						<div className="chart-header daily-enrollments-chart-header">
							<div className="chart-title">
								<span>{ this.state.chart_title }</span>
								<span
									className="dashicons dashicons-info-outline widm-ld-reports-info"
									data-title={ this.state.help_text }
								></span>
							</div>
							<ChartSummarySection
								wrapper_class="chart-summary-daily-enrollments"
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

export default DailyEnrollments;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-daily-enrollments front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( DailyEnrollments ) );
	}
} );
