import React, { Component } from 'react';
import { __ } from '@wordpress/i18n';

class WisdmLoader extends React.Component {
	constructor( props ) {
		super( props );
	}

	render() {
		let show_text = '';
		if ( true == this.props.text ) {
			show_text = (
				<span className="supporting-text">
					{ __(
						'Your report is being generated.',
						'learndash-reports-pro'
					) }
				</span>
			);
		}
		return (
			<div
				className="wisdm-learndash-reports-chart-block"
				data-wisdm-loader="true"
			>
				<div className="wisdm-learndash-reports-revenue-from-courses graph-card-container">
					<div className="wisdm-graph-loading">
						<img
							src={
								wisdm_ld_reports_common_script_data.plugin_asset_url +
								'/images/loader.svg'
							}
							alt=""
						/>
						{ show_text }
					</div>
				</div>
			</div>
		);
	}
}

export default WisdmLoader;
