// cspell:ignore udup uddown udsrc udtxt

import './index.scss';
import React, { Component } from 'react';
import WisdmLoader from '../commons/loader/index.js';
import moment from 'moment';
import { __ } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';

class TotalCourses extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			isLoaded: false,
			error: null,
			start_date: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			).unix(),
			end_date: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			).unix(),
		};

		this.durationUpdated = this.durationUpdated.bind( this );
		this.updateBlock = this.updateBlock.bind( this );
	}

	durationUpdated( event ) {
		this.setState(
			{
				start_date: event.detail.startDate,
				end_date: event.detail.endDate,
			},
			() => {
				this.updateBlock();
			}
		);
	}

	componentDidMount() {
		document.addEventListener( 'duration_updated', this.durationUpdated );
		this.updateBlock();
	}

	updateBlock( callback = '/rp/v1/total-courses' ) {
		let requestUrl =
			'/rp/v1/total-courses?start_date=' +
			this.state.start_date +
			'&end_date=' +
			this.state.end_date;
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			requestUrl +=
				'&wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		wp.apiFetch( {
			path: requestUrl,
		} )
			.then( ( response ) => {
				const percentChange = response.percentChange;
				let changeDirectionClass = 'udup';
				let percentValueClass = 'change-value';
				let hideChange = '';
				let udtxt = '';
				let udsrc = '';
				if ( 0 < percentChange ) {
					changeDirectionClass = 'udup';
					percentValueClass = 'change-value-positive';
					udtxt = __( 'Up', 'learndash-reports-pro' );
					udsrc =
						wisdm_ld_reports_common_script_data.plugin_asset_url +
						'/images/up.png';
				} else if ( 0 > percentChange ) {
					changeDirectionClass = 'uddown';
					percentValueClass = 'change-value-negative';
					udtxt = __( 'Down', 'learndash-reports-pro' );
					udsrc =
						wisdm_ld_reports_common_script_data.plugin_asset_url +
						'/images/down.png';
				} else if ( 0 == percentChange ) {
					hideChange = 'wrld-hidden';
					udtxt = __( 'Up', 'learndash-reports-pro' );
					udsrc =
						wisdm_ld_reports_common_script_data.plugin_asset_url +
						'/images/up.png';
				}
				this.setState( {
					isLoaded: true,
					graphData: {
						totalCourses: response.totalCourses,
						percentChange: percentChange + '%',
						chnageDirectionClass: changeDirectionClass, // cspell:disable-line
						percentValueClass,
						hideChange,
						udtxt,
						udsrc,
					},
					startDate: moment
						.unix( response.requestData.start_date )
						.format( 'MMM, DD YYYY' ),
					endDate: moment
						.unix( response.requestData.end_date )
						.format( 'MMM, DD YYYY' ),
				} );
			} )
			.catch( ( error ) => {
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
		if ( ! this.state.isLoaded ) {
			// yet loading
			body = <WisdmLoader />;
		} else if ( this.state.error ) {
			// error
			body = (
				<div className="wisdm-learndash-reports-chart-block error">
					<div>{ this.state.error.message }</div>
				</div>
			);
		} else {
			body = (
				<div className="wisdm-learndash-reports-chart-block">
					<div className="total-courses-container top-card-container">
						<div className="wrld-date-filter">
							<span className="dashicons dashicons-calendar-alt"></span>
							<div className="wdm-tooltip">
								{ __(
									'Date filter applied:',
									'learndash-reports-pro'
								) }
								<br />
								{ this.state.startDate } -{ ' ' }
								{ this.state.endDate }
							</div>
						</div>
						<div className="total-courses-icon">
							<img
								src={
									wisdm_ld_reports_common_script_data.plugin_asset_url +
									'/images/icon_course_counter.png'
								}
							></img>
						</div>
						<div className="total-courses-details">
							<div className="total-courses-text top-label-text">
								<span>
									{ __( 'Total', 'learndash-reports-pro' ) +
										' ' +
										// cspell:disable-next-line
										wisdm_reports_get_ld_custom_lebel_if_avaiable(
											'courses'
										) }
								</span>
							</div>
							<div className="total-courses-figure">
								<span>
									{ this.state.graphData.totalCourses }
								</span>
							</div>
							<div
								className={ `total-courses-percent-change ${ this.state.graphData.hideChange }` }
							>
								<span
									className={
										this.state.graphData
											.chnageDirectionClass // cspell:disable-line
									}
								>
									<img
										src={ this.state.graphData.udsrc }
									></img>
								</span>
								<span
									className={
										this.state.graphData.percentValueClass
									}
								>
									{ this.state.graphData.percentChange }
								</span>
								<span className="ud-txt">
									{ this.state.graphData.udtxt }
								</span>
							</div>
						</div>
					</div>
				</div>
			);
		}
		return body;
	}
}

export default TotalCourses;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-total-courses front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( TotalCourses ) );
	}
} );
