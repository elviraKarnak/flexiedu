// cspell:ignore eporting

import React, { Component, CSSProperties } from 'react';
import {__, sprintf} from '@wordpress/i18n';
import './style.scss';
import Select from '../commons/emotion-styles-provider/select.js';

class DummyFilters extends Component {
	constructor( props ) {
		super( props );
	}

	render() {
		let body = '';
		body = (
			<div className="quiz-report-filters-wrapper wrld-dummy-filters">
				<div className="wrld-pro-note">
					<div className="wrld-pro-note-content">
						<span>
							<b>{ __( 'Note: ', 'learndash-reports-pro' ) }</b>
							{ sprintf(
								// Translators: %s: Quiz label.
								__(
									'Below is the dummy representation of the %s Reports available in ProPanel.',
									'learndash-reports-pro'
								),
								wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' )
							) }
						</span>
					</div>
				</div>
				<div className="select-view">
					<span>
						{ __( 'Select View', 'learndash-reports-pro' ) }
					</span>
				</div>
				<div className="quiz-report-types">
					<input
						id="dfr"
						type="radio"
						name="quiz-report-types"
						defaultValue="default-quiz-reports"
						defaultChecked=""
					/>
					<label htmlFor="dfr" className="">
						{ sprintf(
							// Translators: %s: Quiz label.
							__(
								'Default %s Report View',
								'learndash-reports-pro'
							),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' )
						) }
					</label>
					<input
						id="cqr"
						type="radio"
						name="quiz-report-types"
						defaultValue="custom-quiz-reports"
						checked
					/>
					<label htmlFor="cqr" className="checked">
						{ ' ' }
						{ sprintf(
							// Translators: %s: Quiz label.
							__(
								'Customized %s Report View',
								'learndash-reports-pro'
							),
							wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quiz' )
						) }
					</label>
				</div>
				<div>
					<div className="quiz-eporting-filter-section custom-filters">
						<div className="quiz-reporting-custom-filters">
							<div className="selector">
								<div className="selector-label">
									{ wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses' ) }
								</div>
								<div className="select-control">
									<Select
										isDisabled={ true }
										value={ {
											value: null,
											label: __(
												'All',
												'learndash-reports-pro'
											),
										} }
										isClearable={ true }
									/>
								</div>
							</div>
							<div className="selector">
								<div className="selector-label">
									{ wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Groups' ) }
								</div>
								<div className="select-control">
									<Select
										isDisabled={ true }
										value={ {
											value: null,
											label: __(
												'All',
												'learndash-reports-pro'
											),
										} }
										isClearable={ true }
									/>
								</div>
							</div>
							<div className="selector">
								<div className="selector-label">
									{ wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Quizzes' ) }
								</div>
								<Select
									isDisabled={ true }
									value={ {
										value: null,
										label: __(
											'All',
											'learndash-reports-pro'
										),
									} }
									isClearable={ true }
								/>
							</div>
						</div>
						<div className="filter-buttons">
							<div className="filter-button-container">
								<button className="button-customize-preview">
									{ __(
										'CUSTOMIZE REPORT',
										'learndash-reports-pro'
									) }
								</button>
								<button className="button-quiz-preview">
									{ __(
										'APPLY FILTERS',
										'learndash-reports-pro'
									) }
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		);
		return body;
	}
}

export default DummyFilters;
