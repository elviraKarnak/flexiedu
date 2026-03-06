import { __ } from '@wordpress/i18n';
import { createElement } from '@wordpress/element';
import React, { Component, CSSProperties } from 'react';
import Select from '../commons/emotion-styles-provider/select.js';

class LocalFilters extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			learner: props.learner,
		};
	}

	render() {
		if ( ! this.state.learner ) {
			return null;
		}
		return (
			<div className="wisdm-learndash-reports-local-filters">
				<Select
					options={ this.state.learner }
					value={ {
						value: this.state.learner.value,
						label: this.state.learner.label,
					} }
				/>
			</div>
		);
	}
}

export default LocalFilters;
