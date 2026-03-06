import {__, sprintf} from '@wordpress/i18n';
import { createElement } from '@wordpress/element';
import React, { Component, CSSProperties } from 'react';
// import WisdmLoader from '../commons/loader/index.js';

class TimeSpentTable extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			type: props.type,
			data_point: props.data_point,
			table: props.table,
			course: props.course,
			learner: props.learner,
			selectedTimePeriod: props.timeperiod,
		};
	}

	formatDate = ( timestamp ) => {
		if (
			parseInt( timestamp ) === 0 ||
			timestamp === null ||
			timestamp === undefined
		) {
			return '-';
		}
		const date = new Date( parseInt( timestamp ) * 1000 );

		// cspell:disable-next-line
		// Format the date as "ddth Month, yyyy"
		const day = date.getDate();
		const month = date.toLocaleString( 'default', { month: 'short' } );
		const year = date.getFullYear();
		const suffixes = [ 'th', 'st', 'nd', 'rd' ];
		const remainder = day % 100;
		const ordinalSuffix =
			suffixes[ ( remainder - 20 ) % 10 ] ||
			suffixes[ remainder ] ||
			suffixes[ 0 ];
		const formattedDate = `${ day }${ ordinalSuffix } ${ month }, ${ year }`;

		return formattedDate;
	};

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

	wisdmLDRConvertTime( seconds ) {
		// Handle invalid input: NaN, null, undefined, or non-numeric values
		if ( seconds === undefined || seconds === null ) {
			return '00:00:00';
		}
		// Convert to number and check for NaN
		const numSeconds = Number( seconds );
		if ( isNaN( numSeconds ) || ! isFinite( numSeconds ) ) {
			return '00:00:00';
		}
		let hours = Math.floor( numSeconds / 3600 );
		let minutes = Math.floor( ( numSeconds % 3600 ) / 60 );
		var seconds = Math.floor( ( numSeconds % 3600 ) % 60 );
		if ( hours < 10 ) {
			hours = '0' + hours;
		}
		if ( minutes < 10 ) {
			minutes = '0' + minutes;
		}
		if ( seconds < 10 ) {
			seconds = '0' + seconds;
		}
		if ( !! hours ) {
			if ( !! minutes ) {
				return `${ hours }:${ minutes }:${ seconds }`;
			}
			return `${ hours }:00:${ seconds }`;
		}
		if ( !! minutes ) {
			return `00:${ minutes }:${ seconds }`;
		}
		return `00:00:${ seconds }`;
	}

	render() {
		const table = (
			<table>
				<tbody>
					<tr>
						<th>
							{ this.state.type == 'learner'
								? wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses' )
								: __( 'Learners', 'learndash-reports-pro' ) }
						</th>
						<th>
							{ __( 'Enrollment Date', 'learndash-reports-pro' ) }
						</th>
						<th>
							{ this.state.type == 'learner'
								? __( 'Progress %', 'learndash-reports-pro' )
								: __( 'Time spent', 'learndash-reports-pro' ) }
						</th>
					</tr>
					{ Object.keys( this.state.table ).map( ( key, index ) => (
						<tr>
							<td width="45%">
								<span className="course-name">
									{ this.state.table[ key ].display_name }
								</span>
							</td>
							<td>
								<span>
									{ this.formatDate(
										this.state.table[ key ].enrollment_date
									) }
								</span>
							</td>
							<td>
								<span>
									{ this.wisdmLDRConvertTime(
										this.state.table[ key ].total_time_spent
									) }
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
										'No %s in this range.',
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
						: __( 'Time spent in the ', 'learndash-reports-pro' ) +
						  this.state.course +
						  '' }
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
							<strong>
								{ this.state.data_point }{ ' ' }
								{ this.state.selectedTimePeriod == 1
									? 'minutes.'
									: 'hours.' }
							</strong>
						</>
					) : (
						<>
							<span>
								{ sprintf(
									// Translators: %s: course label.
									__(
										'Following are the learners in this %s for which time spent is ',
										'learndash-reports-pro'
									),
									wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course', 'lower' )
								) }
							</span>
							<strong>
								{ this.state.data_point }{ ' ' }
								{ this.state.selectedTimePeriod == 1
									? 'minutes.'
									: 'hours.' }
							</strong>
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

export default TimeSpentTable;
