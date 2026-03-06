import {__, sprintf} from '@wordpress/i18n';
import { createElement } from '@wordpress/element';
import React, { Component, CSSProperties } from 'react';
// import WisdmLoader from '../commons/loader/index.js';

class CompletionRateModal extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			table: props.table ?? {},
			category: props.category,
			group: props.group,
			sort: props.sort,
		};
		this.changeSort = this.changeSort.bind( this );
	}

	componentDidMount() {
		jQuery(
			'.wisdm-learndash-reports-course-completion-table progress, .wisdm-learndash-reports-course-completion-table .progress-percentage'
		)
			.on( 'mouseenter', function () {
				jQuery( this ).parent().css( { position: 'relative' } );
				const $div = jQuery( '<div/>' )
					.addClass( 'wrld-tooltip' )
					.css( {
						position: 'absolute',
						zIndex: 999,
						display: 'none',
					} )
					.appendTo( jQuery( this ).parent() );
				$div.text( jQuery( this ).attr( 'data-title' ) );
				const $font = jQuery( this )
					.parents( '.graph-card-container' )
					.css( 'font-family' );
				$div.css( 'font-family', $font );
				$div.show();
			} )
			.on( 'mouseleave', function () {
				jQuery( this ).parent().find( '.wrld-tooltip' ).remove();
			} );

		jQuery(
			'.wisdm-learndash-reports-course-completion-table span.toggle'
		)
			.on( 'mouseenter', function () {
				jQuery( this ).parent().css( { position: 'relative' } );
				const $div = jQuery( '<div/>' )
					.addClass( 'wrld-tooltip' )
					.css( {
						position: 'absolute',
						zIndex: 999,
						display: 'none',
					} )
					.appendTo( jQuery( this ).parent() );
				$div.text( jQuery( this ).attr( 'data-title' ) );
				const $font = jQuery( this )
					.parents(
						'.wisdm-learndash-reports-course-completion-table'
					)
					.css( 'font-family' );
				$div.css( 'font-family', $font );
				$div.show();
			} )
			.on( 'mouseleave', function () {
				jQuery( this ).parent().find( '.wrld-tooltip' ).remove();
			} );
	}

	componentDidUpdate() {
		jQuery(
			'.wisdm-learndash-reports-course-completion-table progress, .wisdm-learndash-reports-course-completion-table .progress-percentage'
		)
			.on( 'mouseenter', function () {
				jQuery( this ).parent().css( { position: 'relative' } );
				const $div = jQuery( '<div/>' )
					.addClass( 'wrld-tooltip' )
					.css( {
						position: 'absolute',
						zIndex: 999,
						display: 'none',
					} )
					.appendTo( jQuery( this ).parent() );
				$div.text( jQuery( this ).attr( 'data-title' ) );
				const $font = jQuery( this )
					.parents( '.graph-card-container' )
					.css( 'font-family' );
				$div.css( 'font-family', $font );
				$div.show();
			} )
			.on( 'mouseleave', function () {
				jQuery( this ).parent().find( '.wrld-tooltip' ).remove();
			} );

		jQuery(
			'.wisdm-learndash-reports-course-completion-table span.toggle'
		)
			.on( 'mouseenter', function () {
				jQuery( this ).parent().css( { position: 'relative' } );
				const $div = jQuery( '<div/>' )
					.addClass( 'wrld-tooltip' )
					.css( {
						position: 'absolute',
						zIndex: 999,
						display: 'none',
					} )
					.appendTo( jQuery( this ).parent() );
				$div.text( jQuery( this ).attr( 'data-title' ) );
				const $font = jQuery( this )
					.parents(
						'.wisdm-learndash-reports-course-completion-table'
					)
					.css( 'font-family' );
				$div.css( 'font-family', $font );
				$div.show();
			} )
			.on( 'mouseleave', function () {
				jQuery( this ).parent().find( '.wrld-tooltip' ).remove();
			} );
	}

	changeSort() {
		let sort = '';
		if ( this.state.sort == 'ASC' ) {
			sort = 'DESC';
		} else {
			sort = 'ASC';
		}
		this.setState( { sort } );
		const durationEvent = new CustomEvent(
			'local_sort_change_completion_modal',
			{
				detail: { value: sort },
			}
		);
		document.dispatchEvent( durationEvent );
	}

	static getDerivedStateFromProps( props, state ) {
		if ( props.table !== state.table ) {
			//Change in props
			return {
				table: props.table ?? {},
			};
		}
		if ( props.category !== state.category ) {
			//Change in props
			return {
				category: props.category,
			};
		}
		if ( props.group !== state.group ) {
			//Change in props
			return {
				group: props.group,
			};
		}
		if ( props.sort !== state.sort ) {
			//Change in props
			return {
				sort: props.sort,
			};
		}
		return null; // No change to state
	}

	render() {
		let text = '';
		if ( this.state.sort == 'ASC' ) {
			text = sprintf(
				// Translators: %s: courses label.
				__( ' (oldest %s first)', 'learndash-reports-pro' ),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses', 'lower' )
			);
		} else {
			text = sprintf(
				// Translators: %s: courses label.
				__( ' (latest %s first)', 'learndash-reports-pro' ),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses', 'lower' )
			);
		}
		const table = (
			<table>
				<tbody>
					<tr>
						<th>
							{ wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' ) }
							<span
								className="toggle"
								onClick={ this.changeSort }
								data-title={
									sprintf(
										// Translators: %s: Courses label.
										__(
											'%s are sorted by publish date',
											'learndash-reports-pro'
										),
										wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses' )
									)  + text
								}
							></span>
						</th>
						<th>
							{ sprintf(
								// Translators: %s: Course label.
								__(
									'%s Completion Rate',
									'learndash-reports-pro'
								),
								wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' )
							) }
						</th>
					</tr>
					{ this.props.loading ? (
						<tr className="wrld-ccr-more-data-loader">
							<td colSpan="2">
								<img
									src={
										wisdm_ld_reports_common_script_data.plugin_asset_url +
										'/images/loader.svg'
									}
								/>
							</td>
						</tr>
					) : (
						<></>
					) }
					{ Object.keys( this.state.table ).map( ( key, index ) => (
						<tr key={ index }>
							<td width="45%">
								<span className="course-name">{ key }</span>
							</td>
							<td width="55%" className="right-side">
								<progress
									className="progress"
									max="100"
									value={ this.state.table[ key ].percentage }
									data-title={
										this.state.table[ key ].completed +
										__(
											' out of ',
											'learndash-reports-pro'
										) +
										this.state.table[ key ].total +
										__(
											' learners completed',
											'learndash-reports-pro'
										)
									}
								></progress>
								<span
									className="progress-percentage"
									data-title={
										this.state.table[ key ].completed +
										__(
											' out of ',
											'learndash-reports-pro'
										) +
										this.state.table[ key ].total +
										__(
											' learners completed',
											'learndash-reports-pro'
										)
									}
								>
									{ this.state.table[ key ].percentage }%
								</span>
							</td>
						</tr>
					) ) }
				</tbody>
			</table>
		);
		const header = (
			<div className="heading_wrapper">
				<h1>
					{ sprintf(
						// Translators: %s: Course label.
						__( '%s Completion Rate', 'learndash-reports-pro' ),
						wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' )
					) }
				</h1>
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

export default CompletionRateModal;
