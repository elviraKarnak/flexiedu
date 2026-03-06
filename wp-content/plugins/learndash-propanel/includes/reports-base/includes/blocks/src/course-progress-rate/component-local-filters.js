import { __ } from '@wordpress/i18n';
import { createElement } from '@wordpress/element';
import React, { Component, CSSProperties } from 'react';
import { Tab, Tabs, TabList, TabPanel } from 'react-tabs';
import Select, {
	AsyncSelect,
} from '../commons/emotion-styles-provider/select.js';

/**
 * If user is the group admin this function returns an array of unique
 * user ids which are enrolled in the groups accessible to the current user.
 */
function wrldGetGroupAdminUsers() {
	const user_accessible_groups =
		wisdm_ld_reports_common_script_data.course_groups;

	const allGroupUsers = Array();
	const includedUserIds = Array();
	if ( user_accessible_groups.length < 1 ) {
		return allGroupUsers;
	}

	user_accessible_groups.forEach( function ( group ) {
		if ( ! ( 'group_users' in group ) ) {
			return;
		}
		const groupUsers = group.group_users;
		groupUsers.forEach( function ( user ) {
			if ( ! includedUserIds.includes( user.id ) ) {
				allGroupUsers.push( user );
				includedUserIds.push( user.id );
			}
		} );
	} );

	return allGroupUsers;
}

/**
 * Based on the current user roles array this function decides wether a user is a group
 * leader or an Administrator and returns the same.
 */
function wisdmLdReportsGetUserType() {
	let userRoles = wisdm_ld_reports_common_script_data.user_roles;
	if ( 'object' === typeof userRoles ) {
		userRoles = Object.keys( userRoles ).map( ( key ) => userRoles[ key ] );
	}
	if ( undefined == userRoles || userRoles.length == 0 ) {
		return null;
	}
	if ( userRoles.includes( 'administrator' ) ) {
		return 'administrator';
	} else if ( userRoles.includes( 'group_leader' ) ) {
		return 'group_leader';
	} else if ( userRoles.includes( 'wdm_instructor' ) ) {
		return 'instructor';
	}
	return null;
}

class LocalFilters extends Component {
	constructor( props ) {
		super( props );
		let learners_disabled = true;
		let categories_disabled = true;
		let groups_disabled = true;
		if (
			1 /*false!=wisdm_ld_reports_common_script_data.is_pro_version_active*/
		) {
			learners_disabled = false;
			categories_disabled = false;
			groups_disabled = false;
		}
		this.state = {
			group: props.group,
			category: props.category,
			course: props.course,
			groups: props.groups,
			categories: props.categories,
			courses: props.courses,
			learner: null,
			loading_groups: false,
			learners_disabled,
			categories_disabled,
			groups_disabled,
			active_tab: props.active_tab,
		};
		this.changeGroups = this.changeGroups.bind( this );
		this.changeCategory = this.changeCategory.bind( this );
		this.changeCourses = this.changeCourses.bind( this );
		this.handleTabSelection = this.handleTabSelection.bind( this );
	}

	changeGroups( event ) {
		this.setState( { groups: event.detail.value } );
	}

	changeCategory( event ) {
		this.setState( { category: event.detail.value } );
	}

	changeCourses( event ) {
		this.setState( { course: event.detail.value } );
	}

	handleTabSelection( tab_key ) {
		const durationEvent = new CustomEvent( 'local_tab_change_progress', {
			detail: { value: tab_key },
		} );
		document.dispatchEvent( durationEvent );
		this.setState( { active_tab: tab_key } );
		// let tabSwitchEvent = new CustomEvent("wisdm-ld-local-report-type-selected", {
		//     "detail": {'active_reports_tab': 'local-course-reports','report_type':this.state.report_type_selected,}});
		// if(1==tab_key) {
		//     tabSwitchEvent = new CustomEvent("wisdm-ld-local-report-type-selected", {
		//         "detail": {'active_reports_tab': 'local-learner-reports',}});
		//     document.dispatchEvent( new CustomEvent("wisdm-ld-custom-report-type-select", {
		//             "detail": {'report_selector': ''}}));
		// }
		// document.dispatchEvent(tabSwitchEvent);
		// if ( 1 == tab_key ) {
		//     jQuery( '.ld-course-field' ).hide();
		// } else {
		//     jQuery( '.ld-course-field' ).css('display', 'flex');
		// }
	}

	handleGroupChange = ( selectedGroup ) => {
		const durationEvent = new CustomEvent( 'local_group_change_progress', {
			detail: { value: selectedGroup },
		} );
		document.dispatchEvent( durationEvent );
		this.setState( { group: selectedGroup } );
	};

	handleCategoryChange = ( selectedCourse ) => {
		const durationEvent = new CustomEvent(
			'local_category_change_progress',
			{
				detail: { value: selectedCourse },
			}
		);
		document.dispatchEvent( durationEvent );
		this.setState( { category: selectedCourse } );
	};

	handleCourseChange = ( selectedCourse ) => {
		const durationEvent = new CustomEvent( 'local_course_change_progress', {
			detail: { value: selectedCourse },
		} );
		document.dispatchEvent( durationEvent );
		this.setState( { course: selectedCourse } );
	};

	handleLearnerChange = ( selectedLearner ) => {
		if ( null == selectedLearner ) {
			this.setState( {
				learner: null,
				courses_disabled: false,
				categories_disabled: false,
			} );
			// this.updateSelectorsFor('learner', null);
		} else {
			const durationEvent = new CustomEvent(
				'local_learner_change_progress',
				{
					detail: { value: selectedLearner },
				}
			);
			document.dispatchEvent( durationEvent );
			/*const categoryEvent = new CustomEvent("local_category_change_progress", {
              "detail": {value:null, label:__('All', 'learndash-reports-pro')}
            });
            document.dispatchEvent(categoryEvent);
            const groupEvent = new CustomEvent("local_group_change_progress", {
              "detail": {value:null, label:__('All', 'learndash-reports-pro')}
            });
            document.dispatchEvent(groupEvent);*/
			this.setState( { learner: selectedLearner } );
			this.setState( {
				category: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
				group: {
					value: null,
					label: __( 'All', 'learndash-reports-pro' ),
				},
			} ); //Clear category, course , lesson, topics selected.
			// this.updateSelectorsFor('learner', selectedLearner.value);
		}
	};

	handleLearnerSearch = ( inputString, callback ) => {
		// perform a request
		const requestResults = [];
		if ( inputString ) {
			// if (3>inputString.length) {
			//     return callback(requestResults);
			// }
			if ( 'group_leader' == wisdmLdReportsGetUserType() ) {
				const groupUsers = wrldGetGroupAdminUsers();
				groupUsers.forEach( ( user ) => {
					if (
						user.display_name
							.toLowerCase()
							.includes( inputString.toLowerCase() ) ||
						user.user_nicename
							.toLowerCase()
							.includes( inputString.toLowerCase() )
					) {
						requestResults.push( {
							value: user.id,
							label: user.display_name,
						} );
					}
				} );
				callback( requestResults );
			} else {
				let callback_path = '/rp/v1/learners?search=' + inputString;
				if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
					callback_path +=
						'&wpml_lang=' +
						wisdm_ld_reports_common_script_data.wpml_lang;
				}
				wp.apiFetch( {
					path: callback_path,
				} )
					.then( ( response ) => {
						if ( false != response && response.posts.length > 0 ) {
							response.posts.forEach( ( element ) => {
								requestResults.push( {
									value: element.ID,
									label: element.name,
								} );
							} );
						}
						callback( requestResults );
					} )
					.catch( ( error ) => {
						callback( requestResults );
					} );
			}
		} else if ( 'group_leader' == wisdmLdReportsGetUserType() ) {
			const groupUsers = wrldGetGroupAdminUsers();
			requestResults.push( {
				value: groupUsers[ 0 ].id,
				label: groupUsers[ 0 ].display_name,
			} );
			this.setState( { learner: requestResults[ 0 ] } );
			callback( requestResults );
		} else {
			let callback_path = '/rp/v1/learners?page=1&per_page=1';
			if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
				callback_path +=
					'&wpml_lang=' +
					wisdm_ld_reports_common_script_data.wpml_lang;
			}
			wp.apiFetch( {
				path: callback_path,
			} )
				.then( ( response ) => {
					if ( false != response && response.posts.length > 0 ) {
						requestResults.push( {
							value: response.posts[ 0 ].ID,
							label: response.posts[ 0 ].name,
						} );
					}
					this.setState( { learner: requestResults[ 0 ] } );
					callback( requestResults );
				} )
				.catch( ( error ) => {
					callback( requestResults );
				} );
		}
	};

	getDefaultOptions = () => {
		const requestResults = [];
		// if (3>inputString.length) {
		//     return callback(requestResults);
		// }
		if ( 'group_leader' == wisdmLdReportsGetUserType() ) {
			const groupUsers = wrldGetGroupAdminUsers();
			groupUsers.forEach( ( user ) => {
				// if (user.display_name.toLowerCase().includes(inputString.toLowerCase()) || user.user_nicename.toLowerCase().includes(inputString.toLowerCase())) {
				//     requestResults.push({value:user.id, label:user.display_name});
				// }

				requestResults.push( {
					value: user.id,
					label: user.display_name,
				} );
			} );
			// return requestResults;
			this.setState( { default_options: requestResults } );
		} else {
			let callback_path = '/rp/v1/learners?page=1&per_page=5';
			if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
				callback_path +=
					'&wpml_lang=' +
					wisdm_ld_reports_common_script_data.wpml_lang;
			}
			wp.apiFetch( {
				path: callback_path,
			} )
				.then( ( response ) => {
					if ( false != response && response.posts.length > 0 ) {
						response.posts.forEach( ( element ) => {
							requestResults.push( {
								value: element.id,
								label: element.name,
							} );
						} );
					}
					this.setState( { default_options: requestResults } );
				} )
				.catch( ( error ) => {
					return requestResults;
				} );
		}
	};

	componentDidMount() {
		//Patch logic for react state update on browser refresh bug.
		document.addEventListener(
			'progress-parent-groups-changed',
			this.changeGroups
		);
		document.addEventListener(
			'progress-parent-category-changed',
			this.changeCategory
		);
		document.addEventListener(
			'progress-parent-course-changed',
			this.changeCourses
		);
		let lock_icon = '';
		if ( false ) {
			lock_icon = (
				<span
					title={ __(
						'Please upgrade the plugin to access this feature',
						'learndash-reports-pro'
					) }
					className="dashicons dashicons-lock ld-reports"
				></span>
			);
		}
		this.setState( { lock_icon } );
		this.getDefaultOptions();
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
		if ( props.courses !== state.courses ) {
			//Change in props
			return {
				courses: props.courses,
			};
		}
		if ( props.course !== state.course ) {
			//Change in props
			return {
				course: props.course,
			};
		}
		if ( props.category !== state.category ) {
			//Change in props
			return {
				category: props.category,
			};
		}
		if ( props.learner !== state.learner ) {
			//Change in props
			return {
				learner: props.learner,
			};
		}
		if ( props.active_tab !== state.active_tab ) {
			//Change in props
			return {
				active_tab: props.active_tab,
			};
		}
		return null; // No change to state
	}

	render() {
		let proClass = 'select-control';
		const wrldPlaceholder = __( 'Search', 'learndash-reports-pro' );
		if (
			true != wisdm_ld_reports_common_script_data.is_pro_version_active
		) {
			proClass = 'ldr-pro';
		}
		return (
			<div className="wisdm-learndash-reports-local-filters">
				<Tabs
					selectedIndex={ this.state.active_tab }
					onSelect={ this.handleTabSelection }
				>
					<TabList>
						<Tab>
							<span className="wrld-labels">
								{ /* cspell:disable-next-line */ }
								{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Courses'
								) }
							</span>
						</Tab>
						<Tab>
							<span className="wrld-labels">
								{ __( 'Learners', 'learndash-reports-pro' ) }
							</span>
						</Tab>
					</TabList>
					<TabPanel>
						<div className="selector lr-learner">
							<div className="selector-label">
								{ __( 'Categories', 'learndash-reports-pro' ) }
								{ this.state.lock_icon }
							</div>
							<div className="select-control">
								<Select
									onChange={ this.handleCategoryChange.bind(
										this
									) }
									isDisabled={
										this.state.categories_disabled
									}
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
								{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Groups'
								) }
								{ this.state.lock_icon }
							</div>
							<div className="select-control">
								<Select
									onChange={ this.handleGroupChange.bind(
										this
									) }
									isDisabled={ this.state.groups_disabled }
									options={ this.state.groups }
									value={ {
										value: this.state.group.value,
										label: this.state.group.label,
									} }
								/>
							</div>
						</div>
						<div className="selector lr-learner">
							<div className="selector-label">
								{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Courses'
								) }
								{ this.state.lock_icon }
							</div>
							<div className="select-control">
								<Select
									onChange={ this.handleCourseChange.bind(
										this
									) }
									options={ this.state.courses }
									value={ {
										value: this.state.course.value,
										label: this.state.course.label,
									} }
								/>
							</div>
						</div>
					</TabPanel>
					<TabPanel>
						<div className="selector lr-learner learner-dd">
							<div className="selector-label">
								{ __( 'Learners', 'learndash-reports-pro' ) }
								{ this.state.lock_icon }
							</div>
							<div className={ proClass }>
								<AsyncSelect
									components={ {
										DropdownIndicator: () => null,
										IndicatorSeparator: () => null,
										NoOptionsMessage: ( element ) => {
											return element.selectProps
												.inputValue.length > 2
												? __(
														" No learners found for the search string '" +
															element.selectProps
																.inputValue +
															"'",
														'learndash-reports-pro'
												  )
												: __(
														' Type 3 or more letters to search',
														'learndash-reports-pro'
												  );
										},
									} }
									placeholder={ wrldPlaceholder }
									isDisabled={ this.state.learners_disabled }
									value={ this.state.learner }
									loadOptions={ this.handleLearnerSearch }
									onChange={ this.handleLearnerChange }
									defaultOptions={
										this.state.default_options
									}
									// defaultOptions="true"
									isClearable="true"
								/>
							</div>
						</div>
					</TabPanel>
				</Tabs>
			</div>
		);
	}
}

export default LocalFilters;
