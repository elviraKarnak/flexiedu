import './index.scss';
import 'daterangepicker/daterangepicker.css';
const daterangepicker = require( 'daterangepicker' );
import { __ } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
import React, { Component, CSSProperties } from 'react';

// To assign event

class Datepickers extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			start: moment(
				new Date( wisdm_ld_reports_common_script_data.start_date )
			),
			end: moment(
				new Date( wisdm_ld_reports_common_script_data.end_date )
			),
		};

		this.durationUpdated = this.durationUpdated.bind( this );
	}

	componentDidMount() {
		const lbl_today = __( 'Today', 'learndash-reports-pro' );
		const lbl_yesterday = __( 'Yesterday', 'learndash-reports-pro' );
		const lbl_last_7_days = __( 'Last 7 Days', 'learndash-reports-pro' );
		const lbl_last_30_days = __( 'Last 30 Days', 'learndash-reports-pro' );
		const lbl_this_month = __( 'This Month', 'learndash-reports-pro' );
		const lbl_last_month = __( 'Last Month', 'learndash-reports-pro' );
		const lbl_last_year = __( 'Last 12 Months', 'learndash-reports-pro' );
		const lbl_last_two_year = __( 'Last 2 Years', 'learndash-reports-pro' );
		const lbl_last_three_year = __(
			'Last 3 Years',
			'learndash-reports-pro'
		);
		const custom_ranges = {};
		( custom_ranges[ lbl_today ] = [ moment(), moment() ] ),
			( custom_ranges[ lbl_yesterday ] = [
				moment().subtract( 1, 'days' ),
				moment().subtract( 1, 'days' ),
			] ),
			( custom_ranges[ lbl_last_7_days ] = [
				moment().subtract( 6, 'days' ),
				moment(),
			] ),
			( custom_ranges[ lbl_last_30_days ] = [
				moment().subtract( 29, 'days' ),
				moment(),
			] ),
			( custom_ranges[ lbl_this_month ] = [
				moment().startOf( 'month' ),
				moment().endOf( 'month' ),
			] ),
			( custom_ranges[ lbl_last_year ] = [
				moment().subtract( 12, 'month' ).startOf( 'month' ),
				moment().subtract( 1, 'month' ).endOf( 'month' ),
			] );
		custom_ranges[ lbl_last_two_year ] = [
			moment().subtract( 24, 'month' ).startOf( 'month' ),
			moment().subtract( 1, 'month' ).endOf( 'month' ),
		];
		custom_ranges[ lbl_last_three_year ] = [
			moment().subtract( 36, 'month' ).startOf( 'month' ),
			moment().subtract( 1, 'month' ).endOf( 'month' ),
		];

		const locale_config = {
			applyLabel: __( 'Apply', 'learndash-reports-pro' ),
			cancelLabel: __( 'Cancel', 'learndash-reports-pro' ),
			fromLabel: __( 'From', 'learndash-reports-pro' ),
			toLabel: __( 'To', 'learndash-reports-pro' ),
			customRangeLabel: __( 'Custom Range', 'learndash-reports-pro' ),
			weekLabel: __( 'W', 'learndash-reports-pro' ),
			daysOfWeek: [
				__( 'Su', 'learndash-reports-pro' ),
				__( 'Mo', 'learndash-reports-pro' ),
				__( 'Tu', 'learndash-reports-pro' ),
				__( 'We', 'learndash-reports-pro' ),
				__( 'Th', 'learndash-reports-pro' ),
				__( 'Fr', 'learndash-reports-pro' ),
				__( 'Sa', 'learndash-reports-pro' ),
			],
			monthNames: [
				__( 'January', 'learndash-reports-pro' ),
				__( 'February', 'learndash-reports-pro' ),
				__( 'March', 'learndash-reports-pro' ),
				__( 'April', 'learndash-reports-pro' ),
				__( 'May', 'learndash-reports-pro' ),
				__( 'June', 'learndash-reports-pro' ),
				__( 'July', 'learndash-reports-pro' ),
				__( 'August', 'learndash-reports-pro' ),
				__( 'September', 'learndash-reports-pro' ),
				__( 'October', 'learndash-reports-pro' ),
				__( 'November', 'learndash-reports-pro' ),
				__( 'December', 'learndash-reports-pro' ),
			],
		};

		jQuery( '#js-daterangepicker-predefined' ).daterangepicker(
			{
				locale: locale_config,
				startDate: this.state.start,
				endDate: this.state.end,
				ranges: custom_ranges,
				maxDate: moment(),
			},
			durationUpdatedCallback
		);

		jQuery( '#js-daterangepicker-predefined' ).on(
			'apply.daterangepicker',
			function ( ev, picker ) {
				const start = picker.startDate;
				const end = picker.endDate;

				const durationEvent = new CustomEvent( 'duration_updated', {
					detail: {
						startDate: start.unix(),
						endDate: end.unix(),
						startDateObject: start,
						endDateObject: end,
					},
				} );
				document.dispatchEvent( durationEvent );
			}
		);

		durationUpdatedCallback( this.state.start, this.state.end );
		document.addEventListener( 'duration_updated', this.durationUpdated );
		const element = document.getElementsByClassName(
			'edit-post-visual-editor__content-area'
		);
		if ( element.length ) {
			const width = element[ 0 ].clientWidth;
			if ( width > 1199 ) {
				for ( const el of element ) {
					el.classList.add( 'wrld-xl' );
				}
			} else if ( width > 992 ) {
				for ( const el of element ) {
					el.classList.add( 'wrld-lg' );
				}
			} else if ( width > 768 ) {
				for ( const el of element ) {
					el.classList.add( 'wrld-m' );
				}
			} else if ( width > 584 ) {
				for ( const el of element ) {
					el.classList.add( 'wrld-s' );
				}
			} else {
				for ( const el of element ) {
					el.classList.add( 'wrld-xs' );
				}
			}
		}
	}

	durationUpdated( event ) {
		this.setState( {
			start: moment( new Date( event.detail.startDateObject ) ),
			end: moment( new Date( event.detail.endDateObject ) ),
		} );
	}

	render() {
		return (
			<div className="wisdm-learndash-reports-date-filters-container">
				<div className="wisdm-ld-reports-title">
					<div className="report-title">
						<h2>
							{ __(
								'ProPanel Dashboard',
								'learndash-reports-pro'
							) }
						</h2>
						<span>
							{ this.state.start.format( 'MMM D, YYYY' ) } -{ ' ' }
							{ this.state.end.format( 'MMM D, YYYY' ) }
						</span>
					</div>
				</div>
				<div className="wisdm-ld-reports-date-range-picker">
					<div className="date-filter-label">
						<i className="dashicons dashicons-calendar-alt"></i>
						<span>
							{ __(
								'SHOWING DATA FOR ',
								'learndash-reports-pro'
							) }
						</span>
					</div>
					<div id="js-daterangepicker-predefined">
						<div className="dashicons dashicons-calendar-alt">
							{ ' ' }
						</div>
						<span>
							{ this.state.start.format( 'MMM D, YYYY' ) } -{ ' ' }
							{ this.state.end.format( 'MMM D, YYYY' ) }
						</span>
					</div>
				</div>
			</div>
		);
	}
}

export default Datepickers;

function durationUpdatedCallback( start, end ) {
	// To trigger the Event
	const durationEvent = new CustomEvent( 'duration_updated', {
		detail: {
			startDate: start.unix(),
			endDate: end.unix(),
			startDateObject: start,
			endDateObject: end,
		},
	} );
	//document.dispatchEvent(durationEvent);
}

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	function durationUpdatedOldCallback( start, end ) {
		// To trigger the Event
		jQuery( '#js-daterangepicker-predefined span' ).html(
			start.format( 'MMM D, YYYY' ) + ' - ' + end.format( 'MMM D, YYYY' )
		);
		jQuery( '.wisdm-ld-reports-title .report-title>span' ).text(
			start.format( 'MMM D, YYYY' ) + ' - ' + end.format( 'MMM D, YYYY' )
		);

		const durationEvent = new CustomEvent( 'duration_updated', {
			detail: {
				startDate: start.unix(),
				endDate: end.unix(),
				startDateObject: start,
				endDateObject: end,
			},
		} );
		document.dispatchEvent( durationEvent );
	}

	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-date-filters front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( Datepickers ) );
	} else {
		//Backward compatibility with version < 1.0.3
		const start = moment(
			new Date( wisdm_ld_reports_common_script_data.start_date )
		);
		const end = moment(
			new Date( wisdm_ld_reports_common_script_data.end_date )
		);
		jQuery( '.report-title > span' ).text(
			start.format( 'MMM D, YYYY' ) + ' - ' + end.format( 'MMM D, YYYY' )
		);
		// To trigger the Event

		jQuery( '#js-daterangepicker-predefined' ).daterangepicker(
			{
				startDate: start,
				endDate: end,
				ranges: {
					Today: [ moment(), moment() ],
					Yesterday: [
						moment().subtract( 1, 'days' ),
						moment().subtract( 1, 'days' ),
					],
					'Last 7 Days': [ moment().subtract( 6, 'days' ), moment() ],
					'Last 30 Days': [
						moment().subtract( 29, 'days' ),
						moment(),
					],
					'This Month': [
						moment().startOf( 'month' ),
						moment().endOf( 'month' ),
					],
					'Last Month': [
						moment().subtract( 1, 'month' ).startOf( 'month' ),
						moment().subtract( 1, 'month' ).endOf( 'month' ),
					],
				},
				maxDate: moment(),
			},
			durationUpdatedOldCallback
		);

		durationUpdatedOldCallback( start, end );
	}
} );
