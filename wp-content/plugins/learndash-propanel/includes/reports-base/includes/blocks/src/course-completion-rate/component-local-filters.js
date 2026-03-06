import { __ } from '@wordpress/i18n';
import { createElement } from '@wordpress/element';
import React, { Component, CSSProperties } from 'react';
import Select from '../commons/emotion-styles-provider/select.js';

class LocalFilters extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			group: props.group,
			category: props.category,
			groups: props.groups,
			categories: props.categories,
			loading_groups: false,
		};
		this.changeGroups = this.changeGroups.bind( this );
		this.changeCategory = this.changeCategory.bind( this );
	}

	changeGroups( event ) {
		this.setState( { groups: event.detail.value } );
	}

	changeCategory( event ) {
		this.setState( { category: event.detail.value } );
	}

	handleGroupChange = ( selectedGroup ) => {
		const durationEvent = new CustomEvent(
			'local_group_change_completion',
			{
				detail: { value: selectedGroup },
			}
		);
		document.dispatchEvent( durationEvent );
		this.setState( { group: selectedGroup } );
	};

	handleCategoryChange = ( selectedCourse ) => {
		const durationEvent = new CustomEvent(
			'local_category_change_completion',
			{
				detail: { value: selectedCourse },
			}
		);
		document.dispatchEvent( durationEvent );
		this.setState( { category: selectedCourse } );
	};

	componentDidMount() {
		//Patch logic for react state update on browser refresh bug.
		document.addEventListener(
			'completion-parent-groups-changed',
			this.changeGroups
		);
		document.addEventListener(
			'completion-parent-category-changed',
			this.changeCategory
		);
	}

	static getDerivedStateFromProps( props, state ) {
		if ( props.categories !== state.categories ) {
			//Change in props
			return {
				categories: props.categories,
			};
		}
		if ( props.groups !== state.groups ) {
			//Change in props
			return {
				groups: props.groups,
			};
		}
		if ( props.group !== state.group ) {
			//Change in props
			return {
				group: props.group,
			};
		}
		if ( props.category !== state.category ) {
			//Change in props
			return {
				category: props.category,
			};
		}
		return null; // No change to state
	}

	render() {
		return (
			<div className="wisdm-learndash-reports-local-filters">
				<div className="selector lr-learner">
					<div className="selector-label">
						{ __( 'Categories', 'learndash-reports-pro' ) }
						{ this.state.lock_icon }
					</div>
					<div className="select-control">
						<Select
							onChange={ this.handleCategoryChange.bind( this ) }
							options={ this.state.categories }
							value={ {
								value: this.state.category.value,
								label: this.state.category.label,
							} }
						/>
					</div>
				</div>
				<div className="selector lr-learner">
					<div className="selector-label">
						{ wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Groups' ) }
						{ this.state.lock_icon }
					</div>
					<div className="select-control">
						<Select
							onChange={ this.handleGroupChange.bind( this ) }
							options={ this.state.groups }
							value={ {
								value: this.state.group.value,
								label: this.state.group.label,
							} }
						/>
					</div>
				</div>
			</div>
		);
	}
}

export default LocalFilters;
