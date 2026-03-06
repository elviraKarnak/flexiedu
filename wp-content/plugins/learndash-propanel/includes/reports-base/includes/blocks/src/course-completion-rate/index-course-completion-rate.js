import './index.scss';
import ChartSummarySection from '../commons/chart-summary/index.js';
import WisdmLoader from '../commons/loader/index.js';
import React, { Component } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';
import CourseCompletionTable from './component-course-completion-table.js';
import DurationFilter from './component-duration-filter.js';
import LocalFilters from './component-local-filters.js';
import CompletionRateModal from './component-completion-rate-modal.js';
import { Modal } from '@wordpress/components';

class CourseCompletionRate extends Component {
	constructor( props ) {
		super( props );
		const error = null;
		// if(null==this.getUserType()) {
		//   error = {message:__( 'Sorry you are not allowed to access this block, please check if you have proper access permissions','learndash-reports-pro')}
		// }
		this.state = {
			isLoaded: false,
			error,
			moreDataLoading: false,
			reportTypeInUse:
				wisdm_ld_reports_common_script_data.report_type,
			chart_title:
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course' ) + // cspell:disable-line
				' ' +
				__( 'Completion Rate', 'learndash-reports-pro' ),
			lock_icon: '',
			request_data: null,
			tableData: {},
			group: { value: null, label: __( 'All', 'learndash-reports-pro' ) },
			groups: [],
			page: 1,
			category: {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			},
			categories: [],
			graph_summary: [],
			duration: {
				value: 'all',
				label: __( 'All time', 'learndash-reports-pro' ),
			},
			help_text: sprintf(
				// Translators: %s: course label.
				__(
					'This report displays the percentage of learners who have completed a %s.',
					'learndash-reports-pro'
				),
				wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Course', 'lower' ),
			),
			course_report_type: null,
			show_supporting_text: false,
			sort: 'DESC',
			sort_inner: 'DESC',
			show_completion_rate_modal: false,
		};

		if (
			false ==
			wisdm_ld_reports_common_script_data.is_pro_version_active
		) {
			this.upgdare_to_pro = 'wisdm-ld-reports-upgrade-to-pro-front'; // cspell:disable-line
			this.lock_icon = (
				<span
					title={ __(
						'Please upgrade the plugin to access this feature',
						'learndash-reports-pro'
					) }
					className="dashicons dashicons-lock ld-reports top-corner"
				></span>
			);
		}

		this.applyFilters = this.applyFilters.bind( this );
		this.handleReportTypeChange = this.handleReportTypeChange.bind( this );
		this.showDummyImages = this.showDummyImages.bind( this );
		this.updateLocalDuration = this.updateLocalDuration.bind( this );
		this.updateSorting = this.updateSorting.bind( this );
		this.updateSortingAsync = this.updateSortingAsync.bind( this );
		this.updateLocalGroup = this.updateLocalGroup.bind( this );
		this.updateLocalCategory = this.updateLocalCategory.bind( this );
		// // this.updateCompletionAverage    = this.updateCompletionAverage.bind(this);
		this.addMoreData = this.addMoreData.bind( this );
		this.openCompletionRateModal =
			this.openCompletionRateModal.bind( this );
		this.closeCompletionRateModal =
			this.closeCompletionRateModal.bind( this );
		this.startCSVDownload = this.startCSVDownload.bind( this );
		this.defaultFiltersLoaded = this.defaultFiltersLoaded.bind( this );
	}

	/**
	 * Based on the current user roles array this function decides wether a user is a group
	 * leader or an Administrator and returns the same.
	 */
	getUserType() {
		let userRoles =
			wisdm_ld_reports_common_script_data.user_roles;
		if ( 'object' === typeof userRoles ) {
			userRoles = Object.keys( userRoles ).map(
				( key ) => userRoles[ key ]
			);
		}
		if ( undefined == userRoles || userRoles.length == 0 ) {
			return null;
		}
		if ( userRoles.includes( 'administrator' ) ) {
			return 'administrator';
		} else if ( userRoles.includes( 'group_leader' ) ) {
			return 'group_leader';
		}
		return null;
	}

	componentDidMount() {
		document.addEventListener(
			'wisdm-ld-reports-filters-applied',
			this.applyFilters
		);
		document.addEventListener(
			'wisdm-ld-reports-report-type-selected',
			this.handleReportTypeChange
		);
		document.addEventListener(
			'wisdm-ldrp-course-report-type-changed',
			this.showDummyImages
		);
		document.addEventListener(
			'local_completion_duration_change',
			this.updateLocalDuration
		);
		document.addEventListener(
			'local_group_change_completion',
			this.updateLocalGroup
		);
		document.addEventListener(
			'local_category_change_completion',
			this.updateLocalCategory
		);
		document.addEventListener(
			'local_sort_change_completion',
			this.updateSorting
		);
		document.addEventListener(
			'local_sort_change_completion_modal',
			this.updateSortingAsync
		);
		// document.addEventListener('refresh-course-completion-average', this.updateCompletionAverage);
		document.addEventListener(
			'start_csv_download_cc',
			this.startCSVDownload
		);
		document.addEventListener(
			'wrld-default-filters-loaded',
			this.defaultFiltersLoaded
		);
		this.updateChart(
			'/rp/v1/course-completion-rate?duration=' +
				this.state.duration.value
		);

		// Fallback: If this block is used alone without report-filters block,
		// fetch the filter data ourselves after a short delay.
		setTimeout( () => {
			if (
				this.state.categories.length === 0 &&
				this.state.groups.length === 0 &&
				! document.querySelector(
					'.wisdm-learndash-reports-report-filters'
				)
			) {
				// Fetch the filter data like report-filters block does.
				let localized_data_url = '/rp/v1/report-filters-data';
				if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
					localized_data_url +=
						'/?wpml_lang=' +
						wisdm_ld_reports_common_script_data.wpml_lang;
				}
				wp.apiFetch( {
					path: localized_data_url,
				} ).then( ( response ) => {
					if ( response.wisdm_ld_reports_common_script_data ) {
						window.wisdm_ld_reports_common_script_data = {
							...window.wisdm_ld_reports_common_script_data,
							...response.wisdm_ld_reports_common_script_data,
						};
					}
					this.defaultFiltersLoaded();
				} );
			}
		}, 1000 );
	}

	defaultFiltersLoaded() {
		const groups =
			wisdm_ld_reports_common_script_data.course_groups;
		const categories =
			wisdm_ld_reports_common_script_data.course_categories;
		// if ( groups.length > 0 ) {
		//   groups.unshift({value: null, label:__('All', 'learndash-reports-pro')});
		// }
		if ( categories.length > 0 ) {
			categories.unshift( {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			} );
		}
		// this.setState(
		//         {
		//           graph_summary: [],
		//           isLoaded: true,
		//           groups:groups,
		//           categories:categories,
		//       });
		const state_updated = Object.assign( {}, this.state );
		state_updated.isLoaded = true;
		state_updated.groups = groups;
		state_updated.categories = categories;
		this.setState( state_updated );
		// console.log(test);
		// console.log(this.state.graph_summary);
		//Patch logic for react state update on browser refresh bug.
		const groupsLoadEvent = new CustomEvent(
			'completion-parent-groups-changed',
			{
				detail: { value: groups },
			}
		);
		const categoryLoadEvent = new CustomEvent(
			'completion-parent-category-changed',
			{
				detail: { value: categories },
			}
		);
		const sortLoadEvent = new CustomEvent(
			'completion-parent-sort-changed',
			{
				detail: { value: 'DESC' },
			}
		);
		document.dispatchEvent( groupsLoadEvent );
		document.dispatchEvent( categoryLoadEvent );
		document.dispatchEvent( sortLoadEvent );
	}

	openCompletionRateModal() {
		document.body.classList.add( 'wrld-open' );
		this.setState( {
			show_completion_rate_modal: true,
		} );
	}

	closeCompletionRateModal() {
		document.body.classList.remove( 'wrld-open' );
		this.setState( {
			show_completion_rate_modal: false,
			moreDataLoading: false,
		} );
		this.setState( { table_data: [] } );
	}

	showDetailsModal() {
		jQuery( '.button-completion-rate' ).trigger( 'click' );
	}

	detailsModal( sort = false ) {
		const self = this;
		if ( ! sort ) {
			var requestUrl =
				'/rp/v1/course-completion-rate/?duration=' +
				this.state.duration.value +
				'&category=' +
				this.state.category.value +
				'&group=' +
				this.state.group.value +
				'&page=' +
				'all' +
				'&sort=' +
				this.state.sort_inner;
		} else {
			var requestUrl =
				'/rp/v1/course-completion-rate/?duration=' +
				this.state.duration.value +
				'&category=' +
				this.state.category.value +
				'&group=' +
				this.state.group.value +
				'&page=' +
				'all' +
				'&sort=' +
				sort;
		}
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			requestUrl +=
				'&wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		// this.setState({selected_data_point: data_point, table_data: [], more: 'yes'});
		wp.apiFetch( {
			path: requestUrl, //Replace with the correct API
		} )
			.then( ( response ) => {
				// Handle error responses (when API returns { code: 'no-data', message: '...' }).
				if ( response.code === 'no-data' ) {
					self.setState( {
						error: {
							message: response.message,
						},
						table_data: [],
						moreDataLoading: false,
					} );
					if ( response.data && response.data.requestData ) {
						self.setState( {
							request_data: response.data.requestData,
						} );
					}
					return;
				}

				let table = response.tableData;
				if ( undefined == response ) {
					table = [];
				}
				this.setState( {
					table_data: table,
					moreDataLoading: false,
					error: null,
				} );
				self.showDetailsModal();
			} )
			.catch( ( error ) => {
				if ( error.data && error.data.requestData ) {
					self.setState( {
						request_data: error.data.requestData,
						moreDataLoading: false,
					} );
				}
				self.setState( {
					error,
					moreDataLoading: false,
				} );
			} );
	}

	addMoreData( event ) {
		// let next = this.state.page + 1;
		// this.setState({page:next});
		// let request_url = '/rp/v1/course-completion-rate/?duration=' + this.state.duration.value + '&group=' + this.state.group.value + '&category=' + this.state.category.value + '&page=' + 'all' + '&sort=' + this.state.sort;
		// this.updateChart(request_url, true);
		this.setState( { moreDataLoading: true } );
		this.detailsModal();
	}

	updateLocalGroup( event ) {
		this.setState( {
			group: event.detail.value,
			page: 1,
			category: {
				value: null,
				label: __( 'All', 'learndash-reports-pro' ),
			},
		} );
		const request_url =
			'/rp/v1/course-completion-rate?duration=' +
			this.state.duration.value +
			'&group=' +
			event.detail.value.value +
			'&category=' +
			null +
			'&sort=' +
			this.state.sort +
			'&page=1';
		this.updateChart( request_url );
	}

	startCSVDownload( event ) {
		let requestUrl =
			'/rp/v1/course-completion-rate-csv/?duration=' +
			this.state.duration.value +
			'&category=' +
			this.state.category.value +
			'&group=' +
			this.state.group.value +
			'&page=' +
			'all';
		// this.setState({selected_data_point: data_point, table_data: [], more: 'yes'});
		if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
			requestUrl +=
				'&wpml_lang=' + wisdm_ld_reports_common_script_data.wpml_lang;
		}
		wp.apiFetch( {
			path: requestUrl, //Replace with the correct API
		} )
			.then( ( response ) => {
				window.location = response.filename;
				// jQuery('<a href="' + response.filename + '" download className="wrld-hidden"></a>').appendTo('body').trigger('click');
			} )
			.catch( ( error ) => {
				if ( error.data && error.data.requestData ) {
					self.setState( {
						request_data: error.data.requestData,
						moreDataLoading: false,
					} );
				}
				self.setState( {
					error,
					moreDataLoading: false,
				} );
			} );
	}

	updateSorting( event ) {
		this.setState( { sort: event.detail.value, page: 1 } );
		const request_url =
			'/rp/v1/course-completion-rate?duration=' +
			this.state.duration.value +
			'&group=' +
			this.state.group.value +
			'&category=' +
			this.state.category.value +
			'&sort=' +
			event.detail.value +
			'&page=1';
		this.updateChart( request_url );
	}

	updateSortingAsync( event ) {
		this.setState( { sort_inner: event.detail.value, table_data: [] } );
		this.setState( { moreDataLoading: true } );
		this.detailsModal( event.detail.value );
		// let request_url = '/rp/v1/course-completion-rate?duration=' + this.state.duration.value + '&group=' + this.state.group.value + '&category=' + this.state.category.value + '&sort=' + event.detail.value + '&page=all';
		// this.updateChart(request_url);
	}

	// updateCompletionAverage(event) {
	//    wp.apiFetch({
	//      path: event.detail.url //Replace with the correct API
	//   }).then(response => {
	//      let avgTimeSpent = response.averageCourseCompletion;
	//      this.setState({
	//          error: null,
	//          isLoaded:true,
	//          graph_summary: {
	//            left: [{
	//                  // cspell:disable-next-line
	//                  title : __('AVG', 'learndash-reports-pro') + ' ' + wisdm_reports_get_ld_custom_lebel_if_avaiable('Course') + ' ' + __('COMPLETION', 'learndash-reports-pro'),
	//                  value: '??'!=avgTimeSpent?Number(parseFloat(avgTimeSpent).toFixed(2)) + '%':avgTimeSpent,
	//                }],
	//            right:[],
	//            last: response.updated,
	//            refresh_url: '/rp/v1/course-completion-average?duration=' + this.state.duration.value + '&group=' + this.state.group.value + '&category=' + this.state.category.value,
	//            rotate: false,
	//          }
	//      });
	//   });
	// }

	updateLocalCategory( event ) {
		this.setState( {
			category: event.detail.value,
			page: 1,
			group: { value: null, label: __( 'All', 'learndash-reports-pro' ) },
		} );
		const request_url =
			'/rp/v1/course-completion-rate?duration=' +
			this.state.duration.value +
			'&group=' +
			null +
			'&category=' +
			event.detail.value.value +
			'&sort=' +
			this.state.sort +
			'&page=1';
		this.updateChart( request_url );
	}

	updateLocalDuration( event ) {
		this.setState( { duration: event.detail.value, page: 1 } );
		const request_url =
			'/rp/v1/course-completion-rate?duration=' +
			event.detail.value.value +
			'&category=' +
			this.state.category.value +
			'&group=' +
			this.state.group.value +
			'&sort=' +
			this.state.sort +
			'&page=1';
		this.updateChart( request_url );
	}

	componentDidUpdate() {
		jQuery( '.CourseCompletionRate .mixed-chart' ).prepend(
			jQuery( '.CourseCompletionRate .apexcharts-toolbar' )
		);
		jQuery(
			'.wisdm-learndash-reports-course-completion-rate .chart-title .dashicons.dashicons-info-outline, .wisdm-learndash-reports-course-completion-rate .chart-summary-revenue-figure .dashicons.dashicons-info-outline'
		)
			.on( 'mouseenter', function () {
				const $div = jQuery( '<div/>' )
					.addClass( 'wdm-tooltip' )
					.css( {
						position: 'absolute',
						zIndex: 999,
						display: 'none',
					} )
					.appendTo( jQuery( this ) );
				$div.text( jQuery( this ).attr( 'data-title' ) );
				const $font = jQuery( this )
					.parents( '.graph-card-container' )
					.css( 'font-family' );
				$div.css( 'font-family', $font );
				$div.show();
			} )
			.on( 'mouseleave', function () {
				jQuery( this ).find( '.wdm-tooltip' ).remove();
			} );
	}

	handleReportTypeChange( event ) {
		this.setState( { reportTypeInUse: event.detail.active_reports_tab } );
		if ( 'quiz-reports' == event.detail.active_reports_tab ) {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-completion-rate',
				false
			);
		} else {
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-completion-rate',
				true
			);
		}
	}

	showDummyImages( event ) {
		this.setState( { course_report_type: event.detail.report_type } );
	}

	applyFilters( event ) {
		const category = event.detail.selected_categories;
		const group = event.detail.selected_groups;
		const course = event.detail.selected_courses;
		const lesson = event.detail.selected_lessons;
		const topic = event.detail.selected_topics;
		const learner = event.detail.selected_learners;

		const request_url =
			'/rp/v1/course-completion-rate/?duration=' +
			this.state.duration.value +
			'&category=' +
			category +
			'&group=' +
			group +
			'&page=' +
			this.state.page;

		// if ( undefined != course ) {
		//   this.setState({show_supporting_text: true});
		// } else {
		//   this.setState({show_supporting_text: false});
		// }

		//Time spent on a course chart should not display for lesson/topic
		if (
			undefined == topic &&
			undefined == lesson &&
			undefined == course
		) {
			this.updateChart( request_url );
			this.setState( { reportTypeInUse: 'default-ld-reports' } );
			this.setState( {
				group: event.detail.selected_groups_obj,
				category: event.detail.selected_categories_obj,
			} );
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-completion-rate',
				true
			);
		} else {
			//hide this block.
			this.setState( {
				reportTypeInUse: 'default-ld-reports-lesson-topic',
			} );
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-completion-rate',
				false
			);
		}
		if (
			undefined != learner ||
			this.state.course_report_type == 'learner-specific-course-reports'
		) {
			this.setState( { reportTypeInUse: 'default-ld-learner-reports' } );
			wisdm_reports_change_block_visibility(
				'.wp-block-wisdm-learndash-reports-course-completion-rate',
				false
			);
		}
	}

	updateChart( requestUrl, is_paginated = false ) {
		if ( ! is_paginated ) {
			this.setState( {
				isLoaded: false,
			} );
		}

		// Initialize callStack if not already set (normally done by report-filters block).
		if ( ! window.callStack ) {
			window.callStack = [];
		}

		const self = this;
		const checkIfEmpty = function () {
			setTimeout( function () {
				if ( window.callStack.length > 4 ) {
					checkIfEmpty();
				} else {
					window.callStack.push( requestUrl );
					if ( wisdm_ld_reports_common_script_data.wpml_lang ) {
						requestUrl +=
							'&wpml_lang=' +
							wisdm_ld_reports_common_script_data.wpml_lang;
					}
					wp.apiFetch( {
						path: requestUrl, //Replace with the correct API
					} )
						.then( ( response ) => {
							// Handle error responses (when API returns { code: 'no-data', message: '...' }).
							if ( response.code === 'no-data' ) {
								window.callStack.pop();
								self.setState( {
									error: {
										message: response.message,
									},
									tableData: [],
									graph_summary: [],
									more: false,
									isLoaded: true,
								} );
								if ( response.data && response.data.requestData ) {
									self.setState( {
										request_data: response.data.requestData,
									} );
								}
								if ( response.updated_on ) {
									self.setState( {
										updated_on: response.updated_on,
									} );
								} else if ( response.data && response.data.updated_on ) {
									self.setState( {
										updated_on: response.data.updated_on,
									} );
								}
								return;
							}

							// console.log(response);
							if ( response.requestData ) {
								self.setState( {
									request_data: response.requestData,
								} );
							}
							if ( response.updated_on ) {
								self.setState( {
									updated_on: response.updated_on,
								} );
							}
							self.showCompletionChart( response, is_paginated );
							window.callStack.pop();
						} )
						.catch( ( error ) => {
							window.callStack.pop();

							if ( error.data && error.data.requestData ) {
								self.setState( {
									request_data: error.data.requestData,
								} );
							}
							// self.setState({
							//      error:error,
							//      graph_summary:[],
							//      isLoaded: true,
							// });
							const state = Object.assign( {}, self.state );
							state.error = error;
							state.graph_summary = [];
							state.isLoaded = true;
							self.setState( state );
						} );
				}
			}, 500 );
		};
		checkIfEmpty();
	}

	showCompletionChart( response, is_paginated ) {
		let table = response.tableData;
		if ( undefined == response ) {
			table = [];
		}
		if ( is_paginated ) {
			table = {
				...this.state.tableData,
				...table,
			};
			// table = this.state.tableData.concat(table);
		}
		let avgTimeSpent = response.averageCourseCompletion;
		if ( ! wisdm_ld_reports_common_script_data.is_pro_version_active ) {
			avgTimeSpent = '??';
		}
		this.setState( {
			tableData: table,
			error: null,
			isLoaded: true,
			more: response.more_data,
			graph_summary: {
				left: [
					{
						title:
							__( 'AVG', 'learndash-reports-pro' ) +
							' ' +
							// cspell:disable-next-line
							wisdm_reports_get_ld_custom_lebel_if_avaiable(
								'Course'
							) +
							' ' +
							__( 'COMPLETION', 'learndash-reports-pro' ),
						value:
							'??' != avgTimeSpent
								? Number(
										parseFloat( avgTimeSpent ).toFixed( 2 )
								  ) + '%'
								: avgTimeSpent,
					},
				],
				right: [],
				last: response.updated,
				refresh_url:
					'/rp/v1/course-completion-average?duration=' +
					this.state.duration.value +
					'&group=' +
					this.state.group.value +
					'&category=' +
					this.state.category.value,
				rotate: false,
			},
		} );
	}

	refreshUpdateTime() {
		this.setState( { isLoaded: false } );
		const requestUrl =
			'/rp/v1/course-completion-rate?duration=' +
			this.state.duration.value +
			'&group=' +
			this.state.group.value +
			'&category=' +
			this.state.category.value +
			'&disable_cache=true';
		this.updateChart( requestUrl );
	}

	render() {
		// console.log(this.state.graph_summary);
		let body = <div></div>;
		if (
			'' != this.state.reportTypeInUse &&
			'default-ld-reports' != this.state.reportTypeInUse
		) {
			body = '';
		} else if ( ! this.state.isLoaded ) {
			// yet loading
			body = <WisdmLoader text={ this.state.show_supporting_text } />;
		} else {
			let graph = '';
			if ( ! this.state.error ) {
				graph = (
					<div className="CourseCompletionRate">
						<div className="row">
							<div className="mixed-chart">
								<CourseCompletionTable
									tableData={ this.state.tableData }
									sort={ this.state.sort }
								/>
								{ 'yes' == this.state.more ? (
									<span
										className="load-more-ajax"
										onClick={
											this.state.moreDataLoading
												? () => {}
												: this.addMoreData
										}
									>
										{ this.state.moreDataLoading ? (
											<div className="wrld-ccr-more-data-loader">
												<img
													src={
														wisdm_ld_reports_common_script_data.plugin_asset_url +
														'/images/loader.svg'
													}
												></img>
											</div>
										) : (
											sprintf(
												// Translators: %s: Courses label.
												__(
													'Show All %s',
													'learndash-reports-pro'
												),
												wisdm_reports_get_ld_custom_lebel_if_avaiable( 'Courses' )
											)
										) }{ ' ' }
									</span>
								) : (
									<span></span>
								) }
							</div>
						</div>
					</div>
				);
			}
			this.state.pro_link =
				'https://go.learndash.com/ppaddon';
			if (
				this.state.course_report_type ==
				'learner-specific-course-reports'
			) {
				this.state.pro_link =
					'https://go.learndash.com/ppaddon';
			}
			body = (
				<div className={ 'wisdm-learndash-reports-chart-block' }>
					<div className="wisdm-learndash-reports-course-completion-rate graph-card-container">
						<div className="chart-header course-completion-rate-chart-header">
							<div className="chart-title">
								<span>{ this.state.chart_title }</span>
								<span
									className="dashicons dashicons-info-outline widm-ld-reports-info"
									data-title={ this.state.help_text }
								></span>
								<DurationFilter
									pro_upgrade_option={ this.upgdare_to_pro } // cspell:disable-line
									wrapper_class="chart-summary-inactive-users"
									duration={ this.state.duration }
								/>
							</div>
							<div className="chart_update_time">
								<span>
									{ __(
										'Last updated: ',
										'learndash-reports-pro'
									) }
								</span>
								<span>{ this.state.updated_on }</span>
								<div className="chart-refresh-icon">
									<span
										className="dashicons dashicons-image-rotate"
										data-title={ __(
											'Click this to refresh the chart',
											'learndash-reports-pro'
										) }
										onClick={ this.refreshUpdateTime.bind(
											this
										) }
									></span>
								</div>
							</div>
							<LocalFilters
								group={ this.state.group }
								category={ this.state.category }
								groups={ this.state.groups }
								categories={ this.state.categories }
							/>
							<ChartSummarySection
								pro_upgrade_option={ this.upgdare_to_pro } // cspell:disable-line
								wrapper_class="chart-summary-course-completion-rate"
								graph_summary={ this.state.graph_summary }
								error={ this.state.error }
								pro_link={ this.state.pro_link }
							/>
							{ /*<SummarySection />*/ }
						</div>
						<div>{ graph }</div>
					</div>
					{this.state.show_completion_rate_modal && (
						<Modal
							onRequestClose={ this.closeCompletionRateModal }
							className={ 'learndash-propanel-modal completion_rate_modal' }
						>
							<CompletionRateModal
								loading={ this.state.moreDataLoading }
								table={ this.state.table_data }
								group={ this.state.group.label }
								category={ this.state.category.label }
								sort={ this.state.sort_inner }
							/>
						</Modal>
					)}
					<button
						className="button-completion-rate wrld-hidden"
						onClick={ this.openCompletionRateModal }
					></button>
				</div>
			);
		}
		return body;
	}
}

export default CourseCompletionRate;

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	const elem = document.getElementsByClassName(
		'wisdm-learndash-reports-course-completion-rate front'
	);
	if ( elem.length > 0 ) {
		const root = createRoot( elem[ 0 ] );
		root.render( React.createElement( CourseCompletionRate ) );
	}
} );
