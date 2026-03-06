import { __ } from '@wordpress/i18n';
import { createElement } from '@wordpress/element';
import React, { Component, CSSProperties } from 'react';
import Select from '../commons/emotion-styles-provider/select.js';

class DurationFilter extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			value: props.duration,
		};
		this.handleValueChange = this.handleValueChange.bind( this );
		document.addEventListener(
			'click',
			function handleClickOutsideBox( event ) {
				const box = document.getElementsByClassName( 'download_csv' );
				const box2 = document.getElementsByClassName( 'ellipses' );
				// console.log(box);
				if (
					box[0]
					&& box2[0]
					&& ! box[ 0 ].contains( event.target )
					&& ! box2[ 0 ].contains( event.target )
				) {
					box[ 0 ].classList.add( 'wrld-hidden' );
				}
			}
		);
	}

	handleValueChange( event ) {
		this.setState( { value: event } );
		const durationEvent = new CustomEvent(
			'local_progress_duration_change',
			{
				detail: { value: event },
			}
		);
		document.dispatchEvent( durationEvent );
	}

	downloadLink( event ) {
		jQuery( event.target ).next().toggleClass( 'wrld-hidden' );
	}

	startCSVDownload( event ) {
		const durationEvent = new CustomEvent( 'start_csv_download_cp', {
			detail: { value: event },
		} );
		document.dispatchEvent( durationEvent );
	}

	render() {
		const options = [
			{ value: 'all', label: __( 'All time', 'learndash-reports-pro' ) },
			{
				value: '6 months',
				label: __( 'Last 6 months', 'learndash-reports-pro' ),
			},
			{
				value: '3 months',
				label: __( 'Last 3 months', 'learndash-reports-pro' ),
			},
			{
				value: '30 days',
				label: __( 'Last 30 days', 'learndash-reports-pro' ),
			},
			{
				value: '7 days',
				label: __( 'Last 7 days', 'learndash-reports-pro' ),
			},
			{
				value: '1 day',
				label: __( 'Last 1 day', 'learndash-reports-pro' ),
			},
		];
		return (
			<div className="wisdm-learndash-reports-duration-filter-completion">
				<div className="selector-label">
					{ __( 'Enrollment date', 'learndash-reports-pro' ) }
				</div>
				<div className="select-control">
					<Select
						options={ options }
						onChange={ this.handleValueChange }
						value={ {
							value: this.state.value.value,
							label: this.state.value.label,
						} }
					/>
				</div>
				<span
					className="ellipses"
					onClick={ this.downloadLink.bind( this ) }
				></span>
				<span
					className="wrld-hidden download_csv"
					onClick={ this.startCSVDownload.bind( this ) }
				>
					{ __( 'Download CSV', 'learndash-reports-pro' ) }
				</span>
			</div>
		);
	}
}

export default DurationFilter;
