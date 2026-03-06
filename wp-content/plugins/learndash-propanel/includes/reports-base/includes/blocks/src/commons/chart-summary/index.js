import './index.scss';
import React, { Component } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * @param    wrapper_class                   - The string passed as an attribute 'wrapper_class' will be used as an additional class for the wrapper having class 'chart-summary'
 * @param    graph_summary                   - The Data argument graph_summary will be required in the following format.
 *                                           graph_summary: {
                                             left: [{
                                             title : __('AVG TIME SPENT ON THE QUIZZES', 'learndash-reports-pro'),
                                             value: wisdmLDRConvertTime(response.average_time_spent),
                                             },],

                                             right:[{
                                             title : __('Total Time Spent: ', 'learndash-reports-pro'),
                                             value: wisdmLDRConvertTime(response.total_time),
                                             },
                                             {
                                             title: __('Total Learners: ', 'learndash-reports-pro'),
                                             value:response.total_learners,
                                             }],
                                             },
 * @argument show_pro_version_upgrade_option bool
 */
class ChartSummarySection extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			upgrade_anchor: '',
		};
		let upgrade_link = '';
		if (
			wisdm_ld_reports_common_script_data.is_admin_user
		) {
			let pro_link =
				wisdm_ld_reports_common_script_data.upgrade_link;
			if ( undefined != props.pro_link ) {
				pro_link = props.pro_link;
			}
			upgrade_link = (
				<a
					className="overlay pro-upgrade"
					href={ pro_link }
					target="__blank"
				>
					<div className="description">
						<span className="upgrade-text">
							{ __(
								'Available in PRO version',
								'learndash-reports-pro'
							) }
						</span>
						<button className="upgrade-button">
							{ __( 'UPGRADE TO PRO', 'learndash-reports-pro' ) }
						</button>
					</div>
				</a>
			);
		}
		// console.log(props.wrapper_class);
		// console.log(props.graph_summary);
		// console.log(props.graph_summary.left);
		// Create a copy of graph_summary to avoid mutating props (which are read-only)
		let graphSummary = props.graph_summary;
		
		// Ensure graph_summary is an object, not an array
		if ( ! graphSummary || Array.isArray( graphSummary ) ) {
			graphSummary = { left: [], right: [] };
		} else {
			// Create a shallow copy to avoid mutating props
			graphSummary = {
				left: graphSummary.left || [],
				right: graphSummary.right || [],
				inner_help_text: graphSummary.inner_help_text,
			};
		}

		// if (undefined==props.error) {
		//   props.error = false;
		// }

		if ( undefined == graphSummary.left[ 0 ] ) {
			graphSummary.left = [
				{
					title:
						__( 'AVG', 'learndash-reports-pro' ) +
						' ' +
						// cspell:disable-next-line
						wisdm_reports_get_ld_custom_lebel_if_avaiable(
							'Course'
						) +
						' ' +
						__( 'COMPLETION', 'learndash-reports-pro' ),
					value: '??',
				},
			];
			graphSummary.right = [];
		}
		if (
			undefined != props.pro_upgrade_option &&
			'' != props.pro_upgrade_option &&
			! props.error
		) {
			graphSummary.left[ 0 ].value = '??';
		}
		this.error = props.error;
		this.summary = graphSummary;
		this.summary_class = props.wrapper_class;
		this.show_pro_version_upgrade_option =
			undefined == props.pro_upgrade_option ||
			'' == props.pro_upgrade_option
				? ''
				: 'wisdm-ld-reports-upgrade-to-pro-front';
		this.lock_icon =
			undefined == props.pro_upgrade_option ? (
				''
			) : (
				<span
					title={ __(
						'Please upgrade the plugin to access this feature',
						'learndash-reports-pro'
					) }
					className="dashicons dashicons-lock ld-reports top-corner"
				></span>
			);
		this.inner_tooltip =
			undefined != this.summary.inner_help_text ? (
				<span
					className="dashicons dashicons-info-outline widm-ld-reports-info"
					data-title={ this.summary.inner_help_text }
				></span>
			) : (
				''
			);
		const upgrade_anchor =
			undefined == props.pro_upgrade_option ? '' : upgrade_link;
		this.state.upgrade_anchor = upgrade_anchor;
	}

	// refreshUpdateTime(event) {
	//   jQuery(event.target).addClass('rotate');
	//   const averageReloadEvent = new CustomEvent('refresh-course-completion-average', {
	//     "detail": {"url": this.props.graph_summary.refresh_url }
	//   });
	//   document.dispatchEvent(averageReloadEvent);
	// }

	render() {
		let summary = '';
		let upgrade_link = '';
		if (
			wisdm_ld_reports_common_script_data.is_admin_user
		) {
			let pro_link =
				wisdm_ld_reports_common_script_data.upgrade_link;
			if ( undefined != this.props.pro_link ) {
				pro_link = this.props.pro_link;
			}
			if ( ! this.error ) {
				this.error = this.props.error;
			}
			upgrade_link = (
				<a
					className="overlay pro-upgrade"
					href={ pro_link }
					target="__blank"
				>
					<div className="description">
						<span className="upgrade-text">
							{ __(
								'Available in PRO version',
								'learndash-reports-pro'
							) }
						</span>
						<button className="upgrade-button">
							{ __( 'UPGRADE TO PRO', 'learndash-reports-pro' ) }
						</button>
					</div>
				</a>
			);
		}
		const upgrade_anchor =
			undefined == this.props.pro_upgrade_option ? '' : upgrade_link;
		if ( this.error ) {
			summary = (
				<div className={ this.summary_class + ' chart-summary error' }>
					<div className="error-message">
						<span>
							{ this.error
								? this.error.message
								: __(
										'Invalid data or no data found',
										'learndash-reports-pro'
								  ) }
						</span>
					</div>
				</div>
			);
		} else {
			// let last_updated = '';
			// if ( ! this.props.graph_summary.rotate && jQuery('.update_time .dashicons').hasClass('rotate') ) {
			//   jQuery('.update_time .dashicons').removeClass('rotate');
			// }
			// if ( undefined != this.props.graph_summary.last ) {
			//   last_updated = <div className="update_time"><span>{__( 'Last updated: ', 'learndash-reports-pro' )}</span><span>{this.props.graph_summary.last}</span><span className="dashicons dashicons-image-rotate" data-title={__('Click this to refresh the average value', 'learndash-reports-pro')} onClick={this.refreshUpdateTime.bind(this)}></span></div>
			// }
			summary = (
				<div
					className={
						this.summary_class +
						' chart-summary ' +
						this.show_pro_version_upgrade_option
					}
				>
					<div className={ 'revenue-figure-wrapper ' }>
						{ this.lock_icon }
							<div className="chart-summary-revenue-figure">
								<div className="revenue-figure">
									<span className="summary-amount">
										{ this.props.graph_summary.left &&
										Array.isArray( this.props.graph_summary.left ) &&
										undefined != this.props.graph_summary.left[ 0 ]
											? this.props.graph_summary.left[ 0 ]
													.value
											: '' }
									</span>
								</div>
								<div className="chart-summary-label">
									<span>
										{ this.props.graph_summary.left &&
										Array.isArray( this.props.graph_summary.left ) &&
										undefined != this.props.graph_summary.left[ 0 ]
											? this.props.graph_summary.left[ 0 ]
													.title
											: '' }
									</span>
									{ this.inner_tooltip }
								</div>
								{ /*{last_updated}*/ }
							</div>
						{ upgrade_anchor }
					</div>
					<div className="revenue-particulars-wrapper">
						<div className="chart-summary-revenue-particulars">
							{ this.props.graph_summary.right &&
							Array.isArray( this.props.graph_summary.right )
								? this.props.graph_summary.right.map(
										( summary_data, i ) => {
											return (
												<div
													className="summery-right-entry"
													key={ i }
												>
													<span className="summary-label">
														{ summary_data.title }
													</span>
													<span className="summary-amount">
														{ summary_data.value }
													</span>
												</div>
											);
										}
								  )
								: null }
						</div>
					</div>
				</div>
			);
		}
		return summary;
	}
}

export default ChartSummarySection;
