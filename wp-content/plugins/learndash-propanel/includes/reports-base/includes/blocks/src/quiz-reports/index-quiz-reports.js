import './index.scss';
import { __ } from '@wordpress/i18n';
import React, { Component } from 'react';
import WisdmLoader from '../commons/loader/index.js';
import { createRoot } from '@wordpress/element';
import { QuizFilters } from '../report-filters/index-report-filters.js';
import { hasReportToolsBlock } from '../commons/has-report-tools-block.js';

document.addEventListener(
	'wisdm-ld-reports-report-type-selected',
	function ( event ) {
		if ( 'quiz-reports' == event.detail.active_reports_tab ) {
			if ( ! wisdm_ld_reports_common_script_data.is_pro_version_active ) {
				let upgrade_button = '';
				if ( wisdm_ld_reports_common_script_data.is_admin_user ) {
					upgrade_button =
						'<div><a className="wrld-upgrade-btn" target="__blank" href="https://go.learndash.com/ppaddon">' +
						__( 'Upgrade to PRO', 'learndash-reports-pro' ) +
						'</a></div> <span>' +
						__( 'OR', 'learndash-reports-pro' ) +
						'</span>';
				}
				const img =
					'<img src=' +
					wisdm_ld_reports_common_script_data.plugin_asset_url +
					"'/images/qrt.png'}></img>";
				const overlay_data =
					'<div className="wrld-upgrade-container"><div className="wrld-upgrade-content"><span>' +
					__( 'Available in ProPanel', 'learndash-reports-pro' ) +
					'</span>' +
					upgrade_button +
					'<div><a className="wrld-learn-more" target="__blank" href="https://go.learndash.com/ppaddon">' +
					__( 'Learn More', 'learndash-reports-pro' ) +
					'</a></div></div></div>';
				jQuery( '.wisdm-learndash-reports-quiz-reports' ).html( img );
				jQuery( '.wisdm-learndash-reports-quiz-reports' ).append(
					overlay_data
				);
				jQuery( '.wisdm-learndash-reports-quiz-reports' )
					.parent()
					.addClass( 'wrld-dummy-report' );
			}
			jQuery( '.wisdm-learndash-reports-quiz-reports' ).parent().show();
			const elem = document.getElementsByClassName( 'wrld-loader' );
			if ( elem.length > 0 ) {
				const root = createRoot( elem[ 0 ] );

				root.render( React.createElement( WisdmLoader ) );
			}
			// document.getElementById("wisdm-learndash-report-filters-container").scrollIntoView();
		} else {
			jQuery( '.wisdm-learndash-reports-quiz-reports' ).parent().hide();
			// document.getElementById("wisdm-learndash-report-filters-container").scrollIntoView();
		}
	}
);

document.addEventListener(
	'wisdm-ld-reports-default-quiz-report-filters-applied',
	function ( event ) {
		let url = wisdmAddParamToURL( 'ld_report_type', 'quiz-reports' );
		//url = wisdmAddParamToURL('referer', url);
		url = wisdmAddParamToURL( 'report', 'quiz', url );
		url = wisdmAddParamToURL( 'period', 'year', url );
		url = wisdmAddParamToURL( 'from_date', '', url );
		url = wisdmAddParamToURL( 'to_date', '', url );
		url = wisdmAddParamToURL(
			'qre_search_field',
			event.detail.selection_label,
			url
		);
		url = wisdmAddParamToURL(
			'search_result_id',
			event.detail.selection_id,
			url
		);
		url = wisdmAddParamToURL(
			event.detail.selection_type,
			event.detail.selection_id,
			url
		);
		let searchType = 'post';
		if ( 'quiz' == event.detail.selection_type ) {
			searchType = 'post';
			// url = wisdmAddParamToURL('screen', event.detail.selection_type, url);
		} else if ( 'user' == event.detail.selection_type ) {
			searchType = 'user';
		}
		//window.location.href = url;
		let page_number = 1;
		const pn = jQuery( 'div.pagination-section > input.page' ).val();
		if ( undefined != pn && pn > 0 ) {
			page_number = pn;
		}
		jQuery(
			'.qre-reports-content, .custom-reports-content, .qre-reports-content'
		).html( '' );
		jQuery( '.wrld-loader' ).show();
		jQuery.get(
			wisdm_ld_reports_common_script_data.ajaxurl,
			{
				action: 'get_quiz_reports_data',
				nonce: wisdm_ld_reports_common_script_data.report_nonce,
				report: event.detail.report_selector,
				search_result_type: searchType,
				search_result_id: event.detail.selection_id,
				qre_search_field: event.detail.selection_label,
				page: page_number,
				pageno: page_number,
				wpml_lang: wisdm_ld_reports_common_script_data.wpml_lang,
			},
			function ( response ) {
				jQuery( '.wisdm-learndash-reports-quiz-reports' ).html(
					response.data.html
				);
				jQuery( '.wrld-loader' ).hide();
				// cspell:disable-next-line
				wisdm_ld_reports_common_script_data.lastCustomDefaultQuiryParams =
					undefined != response.data.entries.query_params
						? response.data.entries.query_params
						: [];
				showDatatable( '#qre_summarized_data', response.data.entries );
				paginateReportsTable(
					'.pagination-form',
					'select.limit',
					'.pagination-section input.page',
					'.pagination-section button.previous-page',
					'.pagination-section button.next-page'
				);
				const elem = document.getElementsByClassName( 'wrld-loader' );
				if ( elem.length > 0 ) {
					const root = createRoot( elem[ 0 ] );

					root.render( React.createElement( WisdmLoader ) );
				}
			}
		);
	}
);

document.addEventListener(
	'wisdm-ld-reports-default-custom-quiz-report-filters-applied',
	function ( event ) {
		const fields = event.detail.fields_selected;
		fields.course_filter = event.detail.selected_courses;
		fields.group_filter = event.detail.selected_groups;
		// cspell:disable-next-line
		fields.quiz_filter = event.detail.selected_quizes;
		fields.start_date = event.detail.start_date;
		fields.end_date = event.detail.end_date;
		jQuery(
			'.qre-reports-content, .custom-reports-content, .qre-reports-content'
		).html( '' );
		jQuery( '.wrld-loader' ).show();
		jQuery.ajax( {
			type: 'POST',
			url: qre_export_obj.ajax_url,
			timeout: 100000,
			retry_count: 0,
			retry_limit: 1,
			data: {
				action: 'qre_save_filters',
				security: qre_export_obj.custom_reports_nonce,
				fields,
			},
			success( result ) {
				jQuery( document ).trigger( 'custom_reports_config_set' );
				getCustomReports( 1 );
				jQuery( 'div.pagination-section > a' ).on(
					'click',
					function ( event ) {
						event.preventDefault();
						console.log( event );
					}
				);
				const elem = document.getElementsByClassName( 'wrld-loader' );
				if ( elem.length > 0 ) {
					const root = createRoot( elem[ 0 ] );

					root.render( React.createElement( WisdmLoader ) );
				}
			},
			error( xhr, status, error_thrown ) {
				if ( status === 'timeout' ) {
					this.retry_count++;
					if ( this.retry_count <= this.retry_limit ) {
						console.log( 'Retrying' );
						jQuery.ajax( this );
					} else {
						console.error( 'request timed out' );
						jQuery( document ).trigger(
							'custom_reports_config_set'
						);
					}
				} else {
					console.log( error_thrown );
					jQuery( document ).trigger( 'custom_reports_config_set' );
				}
			},
		} );
	}
);

/**
 * This callback is used to handle the logic when apply filters is done inside the modal.
 */
document.addEventListener(
	'wisdm-ld-reports-custom-quiz-report-filters-applied',
	function ( event ) {
		const fields = event.detail.fields_selected;
		fields.course_filter = event.detail.selected_courses;
		fields.group_filter = event.detail.selected_groups;
		// cspell:disable-next-line
		fields.quiz_filter = event.detail.selected_quizes;
		fields.start_date = event.detail.start_date;
		fields.end_date = event.detail.end_date;
		jQuery(
			'.qre-reports-content, .custom-reports-content, .qre-reports-content'
		).html( '' );
		jQuery( '.wrld-loader' ).show();
		jQuery.ajax( {
			type: 'POST',
			url: qre_export_obj.ajax_url,
			timeout: 100000,
			retry_count: 0,
			retry_limit: 1,
			data: {
				action: 'qre_save_filters',
				security: qre_export_obj.custom_reports_nonce,
				fields,
			},
			success( result ) {
				getCustomReports( 1 );
			},
			error( xhr, status, error_thrown ) {
				if ( status === 'timeout' ) {
					this.retry_count++;
					if ( this.retry_count <= this.retry_limit ) {
						console.log( 'Retrying' );
						jQuery.ajax( this );
					} else {
						console.error( 'request timed out' );
						jQuery( document ).trigger(
							'custom_reports_config_set'
						);
					}
				} else {
					console.log( error_thrown );
					jQuery( document ).trigger( 'custom_reports_config_set' );
				}
			},
		} );

		document
			.getElementById( 'wisdm-learndash-reports-quiz-report-view' )
			.scrollIntoView();
	}
);

function fetchDataCount( data ) {
	jQuery.ajax( {
		type: 'POST',
		url: qre_export_obj.ajax_url,
		timeout: 100000,
		retry_count: 0,
		retry_limit: 1,
		data: {
			action: 'wrld_export_entries',
			security: qre_export_obj.custom_reports_nonce,
			fields: data,
		},
		success( result ) {
			// getCustomReports(1);
			jQuery( '.bulk-export-heading div span' ).text(
				result.data.count.attempt_count
			);
			// jQuery('.export-attempt-learner-answers .report-export-buttons div span').text(result.data.count.quiz_count);
			// jQuery('.bulk-export-progress label').text('Downloading ' + result.data.count.quiz_count + ' quiz attempts');
		},
	} );
}

document.addEventListener( 'wrld-fetch-export-data-count', function ( event ) {
	const fields = {};
	fields.course_filter = event.detail.selected_courses;
	fields.group_filter = event.detail.selected_groups;
	// cspell:disable-next-line
	fields.quiz_filter = event.detail.selected_quizes;
	fields.start_date = event.detail.start_date;
	fields.end_date = event.detail.end_date;
	fetchDataCount( fields );
} );

document.addEventListener(
	'wrld-bulk-export-attempt-results-success',
	function ( event ) {
		jQuery( '.export-attempt-results .bulk-export-progress' ).addClass(
			'wrld-hidden'
		);
		jQuery( '.export-attempt-results .bulk-export-progress progress' ).val(
			0
		);
		jQuery( '.export-attempt-results .bulk-export-progress span' ).text(
			''
		);
		jQuery( '.export-attempt-results .bulk-export-download' ).append(
			'<a className="button btn btn-primary" href="' +
				event.detail.result.data.link +
				'" download><span className="dashicons dashicons-download"></span>Download ' +
				event.detail.filetype.toUpperCase() +
				'</a>'
		);
		jQuery( '.export-attempt-results .bulk-export-download' ).removeClass(
			'wrld-hidden'
		);
	}
);

document.addEventListener(
	'wrld-bulk-export-learner-results-success',
	function ( event ) {
		jQuery(
			'.export-attempt-learner-answers .bulk-export-progress'
		).addClass( 'wrld-hidden' );
		jQuery(
			'.export-attempt-learner-answers .bulk-export-progress progress'
		).val( 0 );
		jQuery(
			'.export-attempt-learner-answers .bulk-export-progress span'
		).text( '' );
		jQuery(
			'.export-attempt-learner-answers .bulk-export-download'
		).append(
			'<a className="button btn btn-primary" href="' +
				event.detail.result.data.link +
				'" download><span className="dashicons dashicons-download"></span>Download ' +
				event.detail.filetype.toUpperCase() +
				'</a>'
		);
		jQuery(
			'.export-attempt-learner-answers .bulk-export-download'
		).removeClass( 'wrld-hidden' );
	}
);

document.addEventListener(
	'wrld-bulk-export-attempt-results',
	function ( event ) {
		jQuery( '.export-attempt-results .bulk-export-progress label' ).text(
			event.detail.type.toUpperCase() + ' export in progress'
		);
		jQuery( '.export-attempt-results .bulk-export-progress' ).removeClass(
			'wrld-hidden'
		);
		// jQuery('.export-attempt-results .bulk-export-download').addClass('wrld-hidden');

		const max = 100;
		let current = 0;
		const entries = jQuery(
			'.export-attempt-results .report-export-buttons div span'
		).text();
		var interval = setInterval( function () {
			jQuery.ajax( {
				type: 'POST',
				url: qre_export_obj.ajax_url,
				timeout: 100000,
				retry_count: 0,
				retry_limit: 1,
				data: {
					action: 'wrld_export_progress_results',
					security: qre_export_obj.custom_reports_nonce,
				},
				success( result ) {
					current = result.data.percentage;
					if ( current <= max ) {
						jQuery(
							'.export-attempt-results .bulk-export-progress progress'
						).val( current );
						jQuery(
							'.export-attempt-results .bulk-export-progress span'
						).text( current + '% Complete' );
					}
					if ( current >= max ) {
						clearInterval( interval );
					}
				},
				error( xhr, status, error_thrown ) {
					if ( status === 'timeout' ) {
						this.retry_count++;
						if ( this.retry_count <= this.retry_limit ) {
							console.log( 'Retrying' );
							jQuery.ajax( this );
						} else {
							console.error( 'request timed out' );
						}
					} else {
						console.log( error_thrown );
					}
				},
			} );
		}, 1000 );
	}
);

document.addEventListener(
	'wrld-bulk-export-learner-results',
	function ( event ) {
		jQuery(
			'.export-attempt-learner-answers .bulk-export-progress label'
		).text( event.detail.type.toUpperCase() + ' export in progress' );
		jQuery(
			'.export-attempt-learner-answers .bulk-export-progress'
		).removeClass( 'wrld-hidden' );
		// jQuery('.export-attempt-learner-answers .bulk-export-download').addClass('wrld-hidden');

		const max = 100;
		let current = 0;
		const entries = jQuery(
			'.export-attempt-results .report-export-buttons div span'
		).text();
		var interval = setInterval( function () {
			jQuery.ajax( {
				type: 'POST',
				url: qre_export_obj.ajax_url,
				timeout: 100000,
				retry_count: 0,
				retry_limit: 1,
				data: {
					action: 'wrld_export_progress_results',
					security: qre_export_obj.custom_reports_nonce,
				},
				success( result ) {
					current = result.data.percentage;
					if ( current <= max ) {
						jQuery(
							'.export-attempt-learner-answers .bulk-export-progress progress'
						).val( current );
						jQuery(
							'.export-attempt-learner-answers .bulk-export-progress span'
						).text( current + '% Complete' );
					}
					if ( current >= max ) {
						clearInterval( interval );
					}
				},
				error( xhr, status, error_thrown ) {
					if ( status === 'timeout' ) {
						this.retry_count++;
						if ( this.retry_count <= this.retry_limit ) {
							console.log( 'Retrying' );
							jQuery.ajax( this );
						} else {
							console.error( 'request timed out' );
						}
					} else {
						console.log( error_thrown );
					}
				},
			} );
		}, 1000 );
	}
);

/**
 * This callback is used to handle the logic when apply filters is done inside the modal.
 */
document.addEventListener(
	'wrld-bulk-export-attempt-results',
	function ( event ) {
		const fields = {};
		fields.course_filter = event.detail.selected_courses;
		fields.group_filter = event.detail.selected_groups;
		// cspell:disable-next-line
		fields.quiz_filter = event.detail.selected_quizes;
		fields.type = event.detail.type;
		fields.start_date = event.detail.start_date;
		fields.end_date = event.detail.end_date;
		jQuery( '.filter-section' ).css( {
			opacity: 0.5,
			'pointer-events': 'none',
		} );
		jQuery( '.bulk-export-download' ).css( {
			opacity: 0.5,
			'pointer-events': 'none',
		} );
		// jQuery('.wrld-loader').show();
		jQuery.ajax( {
			type: 'POST',
			url: qre_export_obj.ajax_url,
			timeout: 100000,
			retry_count: 0,
			retry_limit: 1,
			data: {
				action: 'wrld_export_attempt_results',
				security: qre_export_obj.custom_reports_nonce,
				fields,
			},
			success( result ) {
				jQuery( '.filter-section' ).css( {
					opacity: 1,
					'pointer-events': 'auto',
				} );
				jQuery( '.bulk-export-download' ).css( {
					opacity: 1,
					'pointer-events': 'auto',
				} );
				const attemptQuizReport = new CustomEvent(
					'wrld-bulk-export-attempt-results-success',
					{
						detail: { result, filetype: event.detail.type },
					}
				);
				document.dispatchEvent( attemptQuizReport );
				// getCustomReports(1);
			},
			error( xhr, status, error_thrown ) {
				if ( status === 'timeout' ) {
					this.retry_count++;
					if ( this.retry_count <= this.retry_limit ) {
						console.log( 'Retrying' );
						jQuery.ajax( this );
					} else {
						console.error( 'request timed out' );
					}
				} else {
					console.log( error_thrown );
				}
			},
		} );

		// document.getElementById("wisdm-learndash-reports-quiz-report-view").scrollIntoView();
	}
);

document.addEventListener(
	'wrld-bulk-export-learner-results',
	function ( event ) {
		const fields = {};
		fields.course_filter = event.detail.selected_courses;
		fields.group_filter = event.detail.selected_groups;
		// cspell:disable-next-line
		fields.quiz_filter = event.detail.selected_quizes;
		fields.type = event.detail.type;
		fields.start_date = event.detail.start_date;
		fields.end_date = event.detail.end_date;
		// jQuery('.wrld-loader').show();
		jQuery( '.filter-section' ).css( {
			opacity: 0.5,
			'pointer-events': 'none',
		} );
		jQuery( '.bulk-export-download' ).css( {
			opacity: 0.5,
			'pointer-events': 'none',
		} );
		jQuery.ajax( {
			type: 'POST',
			url: qre_export_obj.ajax_url,
			timeout: 100000,
			retry_count: 0,
			retry_limit: 1,
			data: {
				action: 'wrld_export_learner_results',
				security: qre_export_obj.custom_reports_nonce,
				fields,
			},
			success( result ) {
				jQuery( '.filter-section' ).css( {
					opacity: 1,
					'pointer-events': 'auto',
				} );
				jQuery( '.bulk-export-download' ).css( {
					opacity: 1,
					'pointer-events': 'auto',
				} );
				const attemptQuizReport = new CustomEvent(
					'wrld-bulk-export-learner-results-success',
					{
						detail: { result, filetype: event.detail.type },
					}
				);
				document.dispatchEvent( attemptQuizReport );
				// getCustomReports(1);
			},
			error( xhr, status, error_thrown ) {
				if ( status === 'timeout' ) {
					this.retry_count++;
					if ( this.retry_count <= this.retry_limit ) {
						console.log( 'Retrying' );
						jQuery.ajax( this );
					} else {
						console.error( 'request timed out' );
					}
				} else {
					console.log( error_thrown );
				}
			},
		} );

		// document.getElementById("wisdm-learndash-reports-quiz-report-view").scrollIntoView();
	}
);

document.addEventListener(
	'wisdm-ld-custom-report-type-select',
	function ( event ) {
		// let url = wisdmAddParamToURL('ld_report_type', 'quiz-reports');
		// url     = wisdmAddParamToURL('report', event.detail.report_selector, url);
		jQuery(
			'.qre-reports-content, .custom-reports-content, .qre-reports-content'
		).html( '' );
		jQuery( '.wrld-loader' ).show();
		if ( 'custom' == event.detail.report_selector ) {
			getCustomReports( 1 );
		} else {
			const page_number = 1;
			jQuery.get(
				wisdm_ld_reports_common_script_data.ajaxurl,
				{
					action: 'get_quiz_reports_data',
					nonce: wisdm_ld_reports_common_script_data.report_nonce,
					report: event.detail.report_selector,
					page: page_number,
					pageno: page_number,
					wpml_lang: wisdm_ld_reports_common_script_data.wpml_lang,
				},
				function ( response ) {
					jQuery( '.wisdm-learndash-reports-quiz-reports' ).html(
						response.data.html
					);
					showDatatable(
						'#qre_summarized_data',
						response.data.entries
					);
					if ( event.detail.report_selector == '' ) {
						paginateReportsTable(
							'.pagination-form',
							'select.limit',
							'.pagination-section input.page',
							'.pagination-section button.previous-page',
							'.pagination-section button.next-page'
						);
					}
					const elem =
						document.getElementsByClassName( 'wrld-loader' );
					if ( elem.length > 0 ) {
						const root = createRoot( elem[ 0 ] );

						root.render( React.createElement( WisdmLoader ) );
					}
				}
			);
		}
		// window.location.href = url;
	}
);

/**
 * This method is used to change number of entries shown per page.
 *
 * @param string          pagination_form Pagination Form Selector.
 * @param string          limit_selector  Limit Dropdown Selector.
 * @param pagination_form
 * @param limit_selector
 */
window.change_entry_count = function ( pagination_form, limit_selector ) {
	const self = this;

	jQuery( limit_selector ).on( 'change', function () {
		const page_number = 1;
		const limit = jQuery( this ).find( 'option:selected' ).val();

		const data = {
			action: 'get_quiz_reports_data',
			nonce: wisdm_ld_reports_common_script_data.report_nonce,
			page: page_number,
			pageno: page_number,
			limit,
			wpml_lang: wisdm_ld_reports_common_script_data.wpml_lang,
		};

		const queryParams =
			undefined !=
			wisdm_ld_reports_common_script_data.lastCustomDefaultQuiryParams // cspell:disable-line
				? wisdm_ld_reports_common_script_data.lastCustomDefaultQuiryParams // cspell:disable-line
				: [];
		jQuery.each( queryParams, function ( key, value ) {
			if ( null != data[ key ] ) {
				return true;
			}
			if ( null !== value ) {
				data[ key ] = value;
			}
		} );

		jQuery(
			'.qre-reports-content, .custom-reports-content, .qre-reports-content'
		).html( '' );

		jQuery( '.wrld-loader' ).show();
		jQuery.get(
			wisdm_ld_reports_common_script_data.ajaxurl,
			data,
			function ( response ) {
				jQuery( '.wisdm-learndash-reports-quiz-reports' ).html(
					response.data.html
				);
				showDatatable( '#qre_summarized_data', response.data.entries );
				paginateReportsTable(
					'.pagination-form',
					'select.limit',
					'.pagination-section input.page',
					'.pagination-section button.previous-page',
					'.pagination-section button.next-page'
				);
				const elem = document.getElementsByClassName( 'wrld-loader' );
				if ( elem.length > 0 ) {
					const root = createRoot( elem[ 0 ] );

					root.render( React.createElement( WisdmLoader ) );
				}
			}
		);
	} );
};

/**
 * This method is used to control pagination inputs.
 *
 * @param string               pagination_form      Pagination form Selector
 * @param string               page_number_selector Page Number Input Selector
 * @param string               previous_page_btn    Previous page button Selector
 * @param string               next_page_button     Next page button Selector
 * @param pagination_form
 * @param page_number_selector
 * @param previous_page_btn
 * @param next_page_button
 */
window.change_page_number = function (
	pagination_form,
	page_number_selector,
	previous_page_btn,
	next_page_button
) {
	const self = this;
	jQuery( page_number_selector ).on( 'change', function () {
		const page_number = parseInt( jQuery( this ).val() );
		const max_page_number = parseInt( jQuery( this ).attr( 'data-max' ) );
		if ( isNaN( page_number ) ) {
			return false;
		}
		if ( page_number > max_page_number ) {
			return false;
		}
		const limit = jQuery( 'select.limit' ).find( 'option:selected' ).val();
		const data = {
			action: 'get_quiz_reports_data',
			nonce: wisdm_ld_reports_common_script_data.report_nonce,
			page: page_number,
			pageno: page_number,
			limit,
			wpml_lang: wisdm_ld_reports_common_script_data.wpml_lang,
		};

		const queryParams =
			undefined !=
			wisdm_ld_reports_common_script_data.lastCustomDefaultQuiryParams // cspell:disable-line
				? wisdm_ld_reports_common_script_data.lastCustomDefaultQuiryParams // cspell:disable-line
				: [];
		jQuery.each( queryParams, function ( key, value ) {
			if ( null != data[ key ] ) {
				return true;
			}
			if ( null !== value ) {
				data[ key ] = value;
			}
		} );

		jQuery.get(
			wisdm_ld_reports_common_script_data.ajaxurl,
			data,
			function ( response ) {
				jQuery( '.wisdm-learndash-reports-quiz-reports' ).html(
					response.data.html
				);
				showDatatable( '#qre_summarized_data', response.data.entries );
				paginateReportsTable(
					'.pagination-form',
					'select.limit',
					'.pagination-section input.page',
					'.pagination-section button.previous-page',
					'.pagination-section button.next-page'
				);
				jQuery( page_number_selector ).val( page_number );
			}
		);
	} );
	jQuery( next_page_button ).on( 'click', function () {
		const page_number = parseInt( jQuery( page_number_selector ).val() );
		const max_page_number = parseInt(
			jQuery( page_number_selector ).attr( 'data-max' )
		);
		if ( page_number === max_page_number ) {
			jQuery( this ).attr( 'disabled', 'disabled' );
			return false;
		}
		const limit = jQuery( 'select.limit' ).find( 'option:selected' ).val();
		const data = {
			action: 'get_quiz_reports_data',
			nonce: wisdm_ld_reports_common_script_data.report_nonce,
			page: page_number + 1,
			pageno: page_number + 1,
			limit,
			wpml_lang: wisdm_ld_reports_common_script_data.wpml_lang,
		};

		const queryParams =
			undefined !=
			wisdm_ld_reports_common_script_data.lastCustomDefaultQuiryParams // cspell:disable-line
				? wisdm_ld_reports_common_script_data.lastCustomDefaultQuiryParams // cspell:disable-line
				: [];
		jQuery.each( queryParams, function ( key, value ) {
			if ( null != data[ key ] ) {
				return true;
			}
			if ( null !== value ) {
				data[ key ] = value;
			}
		} );
		jQuery(
			'.qre-reports-content, .custom-reports-content, .qre-reports-content'
		).html( '' );
		jQuery( '.wrld-loader' ).show();
		jQuery.get(
			wisdm_ld_reports_common_script_data.ajaxurl,
			data,
			function ( response ) {
				jQuery( '.wisdm-learndash-reports-quiz-reports' ).html(
					response.data.html
				);
				showDatatable( '#qre_summarized_data', response.data.entries );
				paginateReportsTable(
					'.pagination-form',
					'select.limit',
					'.pagination-section input.page',
					'.pagination-section button.previous-page',
					'.pagination-section button.next-page'
				);
				jQuery( page_number_selector ).val( page_number + 1 );
				const elem = document.getElementsByClassName( 'wrld-loader' );
				if ( elem.length > 0 ) {
					const root = createRoot( elem[ 0 ] );

					root.render( React.createElement( WisdmLoader ) );
				}
			}
		);
	} );
	jQuery( previous_page_btn ).on( 'click', function () {
		let page_number = parseInt( jQuery( page_number_selector ).val() );
		if ( page_number === 1 ) {
			jQuery( this ).attr( 'disabled', 'disabled' );
			return false;
		}
		page_number = page_number - 1;
		if ( page_number < 1 ) {
			return false;
		}
		const limit = jQuery( 'select.limit' ).find( 'option:selected' ).val();
		const data = {
			action: 'get_quiz_reports_data',
			nonce: wisdm_ld_reports_common_script_data.report_nonce,
			page: page_number,
			pageno: page_number,
			limit,
			wpml_lang: wisdm_ld_reports_common_script_data.wpml_lang,
		};

		const queryParams =
			undefined !=
			wisdm_ld_reports_common_script_data.lastCustomDefaultQuiryParams // cspell:disable-line
				? wisdm_ld_reports_common_script_data.lastCustomDefaultQuiryParams // cspell:disable-line
				: [];
		jQuery.each( queryParams, function ( key, value ) {
			if ( null != data[ key ] ) {
				return true;
			}
			if ( null !== value ) {
				data[ key ] = value;
			}
		} );
		jQuery(
			'.qre-reports-content, .custom-reports-content, .qre-reports-content'
		).html( '' );
		jQuery( '.wrld-loader' ).show();
		jQuery.get(
			wisdm_ld_reports_common_script_data.ajaxurl,
			data,
			function ( response ) {
				jQuery( '.wisdm-learndash-reports-quiz-reports' ).html(
					response.data.html
				);
				showDatatable( '#qre_summarized_data', response.data.entries );
				paginateReportsTable(
					'.pagination-form',
					'select.limit',
					'.pagination-section input.page',
					'.pagination-section button.previous-page',
					'.pagination-section button.next-page'
				);
				jQuery( page_number_selector ).val( page_number );
				const elem = document.getElementsByClassName( 'wrld-loader' );
				if ( elem.length > 0 ) {
					const root = createRoot( elem[ 0 ] );

					root.render( React.createElement( WisdmLoader ) );
				}
			}
		);
	} );
};

function paginateReportsTable(
	pagination_form,
	limit_selector,
	page_number_selector,
	previous_page_btn,
	next_page_button
) {
	change_entry_count( pagination_form, limit_selector );
	change_page_number(
		pagination_form,
		page_number_selector,
		previous_page_btn,
		next_page_button
	);
}

function showDatatable( selector, quiz_statistics_data ) {
	let quiz_reports_table;
	if ( typeof quiz_statistics_data === 'undefined' ) {
		return;
	}
	if ( quiz_statistics_data.hasOwnProperty( 'length' ) ) {
		return;
	}
	let $quiz_title = null;
	let $quiz_title_text = '';
	let $user_name = null;
	let $user_name_text = '';
	const index_offset =
		quiz_statistics_data.limit * ( quiz_statistics_data.page - 1 );
	for ( let i = 0; i < quiz_statistics_data.data.length; i++ ) {
		if ( jQuery( window ).width() < 1500 ) {
			$quiz_title = jQuery( quiz_statistics_data.data[ i ].quiz_title );
			$quiz_title_text = $quiz_title.text().substring( 0, 30 );
			if ( $quiz_title.text().length > 30 ) {
				$quiz_title_text += '...';
			}
			$quiz_title.text( $quiz_title_text );
			quiz_statistics_data.data[ i ].quiz_title = $quiz_title.outerHTML();
			$user_name = jQuery( quiz_statistics_data.data[ i ].user_name );
			$user_name_text = $user_name.text().substring( 0, 30 );
			if ( $user_name.text().length > 30 ) {
				$user_name_text += '...';
			}
			$user_name.text( $user_name_text );
			quiz_statistics_data.data[ i ].user_name = $user_name.outerHTML();
		}
		quiz_statistics_data.data[ i ].index = index_offset + ( i + 1 );
	}
	quiz_reports_table = jQuery( selector ).DataTable( {
		paging: false,
		ordering: false,
		searching: false,
		info: false,
		responsive: false,
		data: quiz_statistics_data.data,
		columns: [
			{ data: 'index' },
			{ data: 'quiz_title' },
			{ data: 'user_name' },
			{ data: 'date_attempt' },
			{ data: 'score' },
			{ data: 'time_taken' },
			{ data: 'link' },
		],
		columnDefs: [
			{
				targets: [ 0, 3, 4, 5, 6 ],
				className: 'dt-center',
			},
			{
				targets: [ 1, 2 ],
				className: 'dt-left',
			},
		],
		language: {
			emptyTable: quiz_statistics_data.no_data,
		},
	} );
}

function wisdmAddParamToURL(
	param,
	value,
	url = qre_export_obj.first_custom_url
) {
	const hash = {};
	const parser = document.createElement( 'a' );

	parser.href = url;
	const parameters = parser.search.split( /\?|&/ );

	for ( let i = 0; i < parameters.length; i++ ) {
		if ( ! parameters[ i ] ) {
			continue;
		}

		const ary = parameters[ i ].split( '=' );
		hash[ ary[ 0 ] ] = ary[ 1 ];
	}

	hash[ param ] = value;

	const list = [];
	Object.keys( hash ).forEach( function ( key ) {
		list.push( key + '=' + hash[ key ] );
	} );

	parser.search = '?' + list.join( '&' );
	return parser.href;
}

jQuery( '.mb-40' ).each( function ( ind, ele ) {
	if ( jQuery( ele ).text().trim().length == 0 ) {
		jQuery( ele ).css( { 'margin-bottom': '0px' } );
	}
} );

function getCustomReports( page_number ) {
	//jQuery('#wisdm-learndash-reports-quiz-report-view').html('');
	if ( page_number < 1 ) {
		return false;
	}
	jQuery.get(
		wisdm_ld_reports_common_script_data.ajaxurl,
		{
			action: 'get_quiz_reports_data',
			nonce: wisdm_ld_reports_common_script_data.report_nonce,
			report: 'custom',
			page: page_number,
			pageno: page_number,
			wpml_lang: wisdm_ld_reports_common_script_data.wpml_lang,
		},
		function ( response ) {
			jQuery( '.wisdm-learndash-reports-quiz-reports' ).html(
				response.data.html
			);
			showDatatable( '#qre_summarized_data', response.data.entries );
			//Disable link redirection in the datatable retrieved & implement ajax based filtering.
			jQuery( 'div.pagination-section > a' ).on(
				'click',
				function ( event ) {
					event.preventDefault();
					const link = jQuery( this ).attr( 'href' );
					const page = getParameterByName( 'paged', link );
					getCustomReports( page );
				}
			);
			const elem = document.getElementsByClassName( 'wrld-loader' );
			if ( elem.length > 0 ) {
				const root = createRoot( elem[ 0 ] );

				root.render( React.createElement( WisdmLoader ) );
			}
		}
	);
}

function getParameterByName( name, url ) {
	if ( ! url ) {
		url = window.location.href;
	}
	name = name.replace( /[\[\]]/g, '\\$&' );
	const regex = new RegExp( '[?&]' + name + '(=([^&#]*)|&|#|$)' ),
		results = regex.exec( url );
	if ( ! results ) {
		return null;
	}
	if ( ! results[ 2 ] ) {
		return '';
	}
	return decodeURIComponent( results[ 2 ].replace( /\+/g, ' ' ) );
}

document.addEventListener( 'DOMContentLoaded', function ( event ) {
	if ( ! hasReportToolsBlock() ) {
		jQuery( '.wisdm-learndash-reports-quiz-reports' ).parent().show();

		// Render QuizFilters component for Bulk Export modal functionality
		// It will be hidden but provides the modal when .wrld-bulk-export is clicked
		if (
			typeof wisdm_ld_reports_common_script_data !== 'undefined' &&
			wisdm_ld_reports_common_script_data.is_pro_version_active
		) {
			const quizFiltersContainer = document.createElement( 'div' );
			quizFiltersContainer.id = 'wrld-standalone-quiz-filters';
			quizFiltersContainer.className = 'wrld-standalone-quiz-filters';
			document.body.appendChild( quizFiltersContainer );

			const quizFiltersRoot = createRoot( quizFiltersContainer );
			quizFiltersRoot.render( React.createElement( QuizFilters ) );
		}

		// Initialize pagination handlers for standalone mode
		// The shortcode renders the HTML with pagination controls, but we need to attach the JS handlers
		paginateReportsTable(
			'.pagination-form',
			'select.limit',
			'.pagination-section input.page',
			'.pagination-section button.previous-page',
			'.pagination-section button.next-page'
		);

		return;
	}

	if ( wisdm_ld_reports_common_script_data.report_type == 'quiz-reports' ) {
		jQuery( '.wisdm-learndash-reports-quiz-reports' ).parent().show();
		jQuery( '.ld-course-field' ).hide();
		document
			.getElementById( 'wisdm-learndash-reports-quiz-report-view' )
			.scrollIntoView();
	} else {
		jQuery( '.wisdm-learndash-reports-quiz-reports' ).parent().hide();
		jQuery( '.ld-course-field' ).css( 'display', 'flex' );
	}

	jQuery( '.qre-reports-content .back-button' ).on( 'click', function ( e ) {
		e.preventDefault();
		open( location, '_self' ).close();
	} );
} );
