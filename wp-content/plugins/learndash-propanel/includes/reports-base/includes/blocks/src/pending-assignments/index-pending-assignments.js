// cspell:ignore udup uddown udsrc udtxt

import './index.scss';
import React, { Component } from 'react';
import WisdmLoader from '../commons/loader/index.js';
import { __ } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
import moment from 'moment';
const ld_api_settings =
	wisdm_ld_reports_common_script_data.ld_api_settings;

class PendingAssignments extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			isLoaded: false,
			error: null,
			start_date: null,
			end_date: null,
			lock_icon: '',
			start_date: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			).unix(),
			end_date: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			).unix(),
			upgrade_class: 'wisdm-class',
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
				if (
					// eslint-disable-next-line camelcase, no-undef
					wisdm_ld_reports_common_script_data.is_pro_version_active
				) {
					this.updateBlock();
				}
			}
		);
	}

	componentDidMount() {
		this.updateBlock();
	}

	updateBlock() {
		if ( undefined == ld_api_settings[ 'sfwd-assignment' ] ) {
			ld_api_settings[ 'sfwd-assignment' ] = 'sfwd-assignment';
		}
		let requestUrl =
			'/rp/v1/pending-assignments?start_date=' +
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
				if (
					true !=
					wisdm_ld_reports_common_script_data.is_pro_version_active
				) {
					const lock_icon = (
						<span
							title={ __(
								'Please upgrade the plugin to access this feature',
								'learndash-reports-pro'
							) }
							className="dashicons dashicons-lock ld-reports top-corner"
						></span>
					);
					const hideChange = '';
					this.setState( {
						graphData: {
							pendingAssignments: '??',
							percentChange: '--' + '%',
							chnageDirectionClass: 'udup', // cspell:disable-line
							percentValueClass: 'change-value',
							hideChange,
						},
						upgrade_class: 'wisdm-upgrade-to-pro',
						isLoaded: true,
						lock_icon,
					} );
				} else {
					const pendingAssignments = response.pendingAssignments;
					const percentChange = 0;
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
							pendingAssignments,
							percentChange: percentChange + '%',
							chnageDirectionClass: changeDirectionClass, // cspell:disable-line
							percentValueClass,
							hideChange,
							udtxt,
							udsrc,
						},
					} );
				}
			} )
			.catch( ( error ) => {
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
			let upgrade_notice = '';
			if (
				true ==
				wisdm_ld_reports_common_script_data.is_admin_user
			) {
				upgrade_notice = (
					<a
						className="overlay pro-upgrade"
						href="https://go.learndash.com/ppaddon"
						target="__blank"
					>
						<div className="description">
							<span className="upgrade-text">
								{ __( 'Available in PRO version' ) }
							</span>
							<button className="upgrade-button">
								{ __(
									'UPGRADE TO PRO',
									'learndash-reports-pro'
								) }
							</button>
						</div>
					</a>
				);
			}
			body = (
				<div
					className={
						'wisdm-learndash-reports-chart-block ' +
						this.state.upgrade_class
					}
				>
					{ this.state.lock_icon }
					<div className="pending-assignments-container top-card-container ">
						<div className="pending-assignments-icon">
							<img
								src={
									wisdm_ld_reports_common_script_data.plugin_asset_url +
									'/images/icon_pending_assignment_counter.png'
								}
							></img>
						</div>
						<div className="pending-assignments-details">
							<div className="pending-assignments-text top-label-text">
								<span>
									{ __(
										'Assignments Pending',
										'learndash-reports-pro'
									) }
								</span>
							</div>
							<div className="pending-assignments-figure">
								<span>
									{ this.state.graphData.pendingAssignments }
								</span>
							</div>
							<div
								className={ `pending-assignments-percent-change ${ this.state.graphData.hideChange }` }
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
					{ upgrade_notice }
				</div>
			);
		}
		return body;
	}
}

export default PendingAssignments;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-pending-assignments front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( PendingAssignments ) );
	}
} );
