import React, { Component } from 'react';
import { __ } from '@wordpress/i18n';

class DummyReports extends React.Component {
	constructor( props ) {
		super( props );
		this.image = undefined != props.image_path ? props.image_path : '#';
		this.url =
			undefined != props.url
				? props.url
				: 'https://go.learndash.com/ppaddon';
	}

	render() {
		let dummyContent = '';
		let upgrade_button = '';
		let or_txt = '';
		if ( wisdm_ld_reports_common_script_data.is_admin_user ) {
			upgrade_button = (
				<div>
					<a
						className="wrld-upgrade-btn"
						target="__blank"
						href={ this.url }
					>
						{ __( 'Upgrade to PRO', 'learndash-reports-pro' ) }
					</a>
				</div>
			);
			or_txt = <span>{ __( 'OR', 'learndash-reports-pro' ) }</span>;
		}
		dummyContent = (
			<div
				className={
					'wisdm-learndash-reports-chart-block wrld-dummy-images'
				}
			>
				<div className="wisdm-learndash-reports-time-spent-on-a-course graph-card-container">
					<div className="wrld-upgrade-container">
						<div className="wrld-upgrade-content">
							<span>
								{ __(
									'Available in ProPanel',
									'learndash-reports-pro'
								) }
							</span>
							{ upgrade_button } { or_txt }
							<div>
								<a
									className="wrld-learn-more"
									target="__blank"
									href={ this.url }
								>
									{ __(
										'Learn More',
										'learndash-reports-pro'
									) }
								</a>
							</div>
						</div>
					</div>
					<img
						src={
							wisdm_ld_reports_common_script_data.plugin_asset_url +
							'/images/' +
							this.image
						}
					></img>
				</div>
			</div>
		);
		return dummyContent;
	}
}

export default DummyReports;
