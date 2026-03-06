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
	}

	handleValueChange( event ) {
		this.setState( { value: event } );
		const durationEvent = new CustomEvent( 'local_duration_change', {
			detail: { value: event },
		} );
		document.dispatchEvent( durationEvent );
	}

	render() {
		const options = [
			{
				value: '1 day',
				label: __( 'Last 1 day', 'learndash-reports-pro' ),
			},
			{
				value: '7 days',
				label: __( 'Last 7 days', 'learndash-reports-pro' ),
			},
			{
				value: '30 days',
				label: __( 'Last 30 days', 'learndash-reports-pro' ),
			},
			{
				value: '3 months',
				label: __( 'Last 3 months', 'learndash-reports-pro' ),
			},
			{
				value: '6 months',
				label: __( 'Last 6 months', 'learndash-reports-pro' ),
			},
		];
		return (
			<div className="wisdm-learndash-reports-duration-filter">
				<Select
					options={ options }
					onChange={ this.handleValueChange }
					value={ {
						value: this.state.value.value,
						label: this.state.value.label,
					} }
				/>
			</div>
		);
	}
}

export default DurationFilter;
