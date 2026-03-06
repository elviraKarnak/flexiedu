import {__, sprintf} from '@wordpress/i18n';
import { createElement } from '@wordpress/element';
import React, { Component, CSSProperties } from 'react';
// import WisdmLoader from '../commons/loader/index.js';

class ProgressDetailsTable extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			type: props.type,
			data_point: props.data_point,
			table: props.table,
			course: props.course,
			learner: props.learner,
		};
	}

	static getDerivedStateFromProps( props, state ) {
		if ( props.type !== state.type ) {
			//Change in props
			return {
				type: props.type,
			};
		}
		if ( props.data_point !== state.data_point ) {
			//Change in props
			return {
				data_point: props.data_point,
			};
		}
		if ( props.table !== state.table ) {
			//Change in props
			return {
				table: props.table,
			};
		}
		if ( props.course !== state.course ) {
			//Change in props
			return {
				course: props.course,
			};
		}
		if ( props.learner !== state.learner ) {
			//Change in props
			return {
				learner: props.learner,
			};
		}
		return null; // No change to state
	}

	render() {
		const table = (
			<table>
				<tbody>
					<tr>
						<th>
							{ this.state.type == 'learner'
								? wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses' )
								: __(
										'Active Learners',
										'learndash-reports-pro'
								  ) }
						</th>
						<th>
							{ __( 'Enrollment Date', 'learndash-reports-pro' ) }
						</th>
						<th>
							{ this.state.type == 'learner'
								? __( 'Progress %', 'learndash-reports-pro' )
								: __( 'Progress %', 'learndash-reports-pro' ) }
						</th>
					</tr>
					{ Object.keys( this.state.table ).map( ( key, index ) => (
						<tr>
							<td width="45%">
								<span className="course-name">{ key }</span>
							</td>
							<td>
								<span>
									{ this.state.table[ key ].enrolled_on }
								</span>
							</td>
							<td>
								<span>
									{ this.state.table[ key ].progress }%
								</span>
							</td>
						</tr>
					) ) }
					{ 0 == this.state.table.length ? (
						<tr>
							{ this.state.type == 'learner'
								? sprintf(
									// Translators: %s: courses label.
									__(
										'No %s in this progress range.',
										'learndash-reports-pro'
									),
									wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses', 'lower' )
								)
								: __(
										'No Learners in this progress range.',
										'learndash-reports-pro'
								  ) }
						</tr>
					) : (
						''
					) }
				</tbody>
			</table>
		);
		const header = (
			<div className="heading_wrapper">
				<h1>
					{ this.state.type == 'learner'
						? this.state.learner + "'s progress"
						: this.state.course + ' course' }
				</h1>
				<div>
					{ this.state.type == 'learner' ? (
						<>
							<span>
								{ sprintf(
									// Translators: %s: courses label.
									__(
										'Following are the %s for which completion percentage rate is ',
										'learndash-reports-pro'
									),
									wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses', 'lower' )
								) }
							</span>
							<strong>{ this.state.data_point }</strong>
						</>
					) : (
						<>
							<span>
								{ sprintf(
									// Translators: %s: course label.
									__(
										'Following are the learners in this %s for which completion percentage rate is ',
										'learndash-reports-pro'
									),
									wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course', 'lower' )
								) }
							</span>
							<strong>{ this.state.data_point }</strong>
						</>
					) }
				</div>
			</div>
		);
		return (
			<div>
				<div className="header">{ header }</div>
				<div className="wisdm-learndash-reports-course-completion-table">
					{ table }
				</div>
			</div>
		);
	}
}

export default ProgressDetailsTable;
