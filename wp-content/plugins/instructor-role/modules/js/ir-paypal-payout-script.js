jQuery(document).ready(function () {
    // Display instructor paypal email on toggling payment method to paypal payouts.
    jQuery('.ir-commission-payment-method').on('change', function () {
        if ('paypal-payout' == jQuery(this).val()) {
            jQuery('.ir-payout-email').show();
            jQuery('.ir-commission-notes').hide();
        } else {
            jQuery('.ir-payout-email').hide();
            jQuery('.ir-commission-notes').show();

        }
    });

    
    jQuery('.ir-bulk-commission').on('click', function (e) {
        jQuery('.ir-bulk-commission .icon-tabler-loader-2').show();
        var commission = jQuery('#ir-bulk-commission').val();
        jQuery.ajax({
            url: ir_loc.ajax_url,
            data: {
                action: 'update_admin_settings',
                key: 'wdm_bulk_commission',
                value: commission,
            },
            type: 'POST',
            success: function (data) {
                console.log('success');
                jQuery('.ir-bulk-commission .icon-tabler-loader-2').hide();
                jQuery('.ir-bulk-commission .icon-tabler-check').show();
                setTimeout(function(){
                    jQuery('.ir-bulk-commission .icon-tabler-check').fadeOut();
                },2000)
            },
            error: function (data) {
            showNotification({
                title: sprintf(__("Something wrong!", "wdm_instructor_role")),
                message: sprintf(
                __("This user has successfully been added as an instructor", "wdm_instructor_role")
                ),
                color: 'red'
            });
            }
        })
    })

    jQuery('.ir-ajax > input[type=checkbox]').on('change', function (e) {
        var key = jQuery(this).attr('name');
        var value = jQuery(this)[0].checked ? 1 : 0;
        saveAdminSetting(key, value);
    })

    function saveAdminSetting(key, value) {
        jQuery.ajax({
            url: ir_loc.ajax_url,
            data: {
                action: 'update_admin_settings',
                key: key,
                value: value,
            },
            type: 'POST',
            success: function (data) {
                if(key === 'instructor_commission'){
                    if(value === 1){
                        jQuery('.allow-commission-section').show();
                        jQuery('.allow-commission-save').show();
                    }
                    else{
                        jQuery('.allow-commission-section').hide();
                        jQuery('.allow-commission-save').hide();
                    }
                }
            },
            error: function (data) {
            showNotification({
                title: sprintf(__("Something wrong!", "wdm_instructor_role")),
                message: sprintf(
                __("This user has successfully been added as an instructor", "wdm_instructor_role")
                ),
                color: 'red'
            });
            }
        })
    }
    
    jQuery('.ir-back-settings').on('click', function (e) {
        jQuery('.ir-hide-menu-setting').show();
        jQuery('a[href=#menu_settings]').trigger('click');
    })
    
    jQuery('.ir-edit-das-settings').on('click', function (e) {
        jQuery('.ir-hide-menu-setting').hide();
        jQuery('a[href=#appearance_settings]').trigger('click');
    })

    jQuery('.ir-overview-settings').on('click', function (e) {
        jQuery('.ir-hide-menu-setting').hide();
        jQuery('a[href=#overview_page]').trigger('click');
    })

    jQuery('.ir-search-instructor > button').on('click', function (e) {
        jQuery('.ir-onboarding-container').hide();
        jQuery('.step_existing_user').css('display', 'flex');
    })
    
    jQuery(document).on('click', '.ir-add-instructor .mantine-Tabs-tabsList > button:nth-child(2)', function (e) {
        jQuery('.ir-onboarding-container').hide();
        jQuery('.step_new_user').css('display', 'flex');
    })

    jQuery(document).on('click', '.ir-show-last-step', function (e) {
        jQuery('.ir-onboarding-container').hide();
        jQuery('.step_last').css('display', 'flex');
    })

    jQuery(document).on('click', '.ir-certificate-commissions > div > div  > button:nth-child(1)', function (e) {
        jQuery('.ir_commission_step').hide();
        jQuery('.ir_assign_step').css('display', 'flex');
    }) 

    jQuery(document).on('click', '.wisdm-manage-instructor > div > div > button', function (e) {
        jQuery('.ir_commission_step').hide();
        jQuery('.ir_edit_step').css('display', 'flex');
    })

    jQuery(document).on('click', '.ir-mobile-header-content .icon-tabler-text-wrap-disabled', function (e) {
        jQuery(this).toggleClass('ir-opened');
        jQuery('.admin-settings ').toggleClass('ir-opened-sidebar');
    })

    jQuery('a[href=#menu_settings]').trigger('click');

    // Make manual/payout commission payment
    jQuery('#popUpDiv').on('click', '#ir_pay_click', function (e) {
        e.preventDefault();
        var update_commission = jQuery(this);
        update_commission.parent().find('.wdm_ajax_loader').show();
        var total_paid = parseFloat(jQuery('#wdm_total_amount_paid_price').val().replace(/,/g, ''));
        var amount_paid = parseFloat(jQuery('#wdm_amount_paid_price').val().replace(/,/g, ''));
        var enter_amount = parseFloat(jQuery('#wdm_pay_amount_price').val().replace(/,/g, ''));
        var instructor_id = jQuery('#instructor_id').val();
        var payment_method = jQuery('.ir-commission-payment-method:checked').val();
        var payout_note = jQuery('#ir_payout_note').val();
        var ir_nonce = jQuery('#ir_nonce').val();
        // ir_commission_paypal_payout_payment

        // Validate empty amount
        if ('' == enter_amount || 0 >= enter_amount || isNaN(enter_amount)) {
            alert(wdm_commission_data.enter_amount);
            update_commission.parent().find('.wdm_ajax_loader').hide();
            return false;
        }

        // Validate amount value
        if (enter_amount > amount_paid) {
            alert(wdm_commission_data.enter_amount_less_than);
            update_commission.parent().find('.wdm_ajax_loader').hide();
            return false;
        }

        // Validate amount value
        if (enter_amount > amount_paid) {
            alert(wdm_commission_data.enter_amount_less_than);
            update_commission.parent().find('.wdm_ajax_loader').hide();
            return false;
        }

        // Validate payment method
        if (!payment_method.length) {
            alert(ir_commission_data.payment_method_empty);
            update_commission.parent().find('.wdm_ajax_loader').hide();
            return false;
        }

        if ('manual' == payment_method) {
            jQuery.ajax({
                method: 'post',
                url: wdm_commission_data.ajax_url,
                dataType: 'JSON',
                data: {
                    action: 'wdm_amount_paid_instructor',
                    total_paid: total_paid,
                    amount_tobe_paid: amount_paid, // cspell:disable-line
                    enter_amount: enter_amount,
                    instructor_id: instructor_id,
                    payout_note: payout_note
                },
                success: function (response) {
                    jQuery.each(response, function (index, val) {
                        switch (index) {
                            case "error":
                                alert(val);
                                update_commission.parent().find('.wdm_ajax_loader').hide();
                                break;
                            case "success":
                                jQuery('#wdm_total_amount_paid_price').attr('value', val.total_paid);
                                jQuery('#wdm_amount_paid_price').attr('value', val.amount_tobe_paid); // cspell:disable-line
                                jQuery('#wdm_pay_amount_price').attr('value', '');
                                jQuery('#wdm_pay_amount_price').val('');
                                jQuery('#wdm_total_amount_paid').text(val.total_paid);
                                jQuery('#wdm_amount_paid').text(val.amount_tobe_paid); // cspell:disable-line
                                update_commission.parent().find('.wdm_ajax_loader').hide();
                                if (val.amount_tobe_paid == 0) { // cspell:disable-line
                                    jQuery('#ir_pay_click').remove();
                                }
                                alert(wdm_commission_data.added_successfully);
                                jQuery('#wdm_close_pop').trigger('click');
                                add_manual_commission_log_record(val.row);
                                break;
                        }
                        jQuery('#ir_payout_note').val('');
                    });
                }
            });
        } else {
            jQuery.ajax({
                method: 'post',
                url: wdm_commission_data.ajax_url,
                dataType: 'JSON',
                data: {
                    action: 'ir_payout_transaction',
                    total_paid: total_paid,
                    amount_tobe_paid: amount_paid, // cspell:disable-line
                    enter_amount: enter_amount,
                    instructor_id: instructor_id,
                    ir_nonce: ir_nonce
                },
                success: function (response) {
                    jQuery.each(response, function (index, val) {
                        switch (index) {
                            case "error":
                                alert(val);
                                update_commission.parent().find('.wdm_ajax_loader').hide();
                                break;
                            case "success":
                                jQuery('#wdm_total_amount_paid_price').attr('value', val.paid_earnings);
                                jQuery('#wdm_amount_paid_price').attr('value', val.unpaid_earnings);
                                jQuery('#wdm_pay_amount_price').attr('value', '');
                                jQuery('#wdm_total_amount_paid').text(val.paid_earnings);
                                jQuery('#wdm_amount_paid').text(val.unpaid_earnings);
                                update_commission.parent().find('.wdm_ajax_loader').hide();
                                if (val.unpaid_earnings == 0) {
                                    jQuery('#ir_pay_click').remove();
                                }
                                alert(wdm_commission_data.added_successfully);
                                break;
                        }
                    });
                },
                error: function (jqXHR, exception) {
                    var msg = '';
                    if (0 === jqXHR.status) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') { // cspell:disable-line
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    alert(msg);
                },
                timeout: 10000
            });
        }
    });

    // Toggle payout row details
    jQuery('.ir-payout-row').on('click', function () {
        var $row = jQuery(this);
        var batch_id = $row.find('.ir-payout-batch-id').data('batch-id');

        // Check if data already fetched
        if (jQuery('.footable-row-detail .ir-payout-transactions-details-' + batch_id).length) {
            return;
        }

        jQuery.ajax({
            url: ir_commission_data.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: 'ir-get-payout-transaction-details',
                batch_id: batch_id,
                ir_nonce: jQuery('#ir_get_payout_nonce').val()
            },
            complete: function () {
                var $black_screen = jQuery('.footable-row-detail .ir-black-screen-' + batch_id);
                $black_screen.css('display', 'none');
            },
            success: function (response) {
                if ('success' == response.type) {
                    jQuery('.footable-row-detail .ir-payout-transactions-details-' + batch_id).html(response.html);
                }
            },
            error: function (jqXHR, exception) {
                var msg = '';
                if (0 === jqXHR.status) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') { // cspell:disable-line
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            },
            timeout: 10000
        });
    });

    // Initialize Commission Logs Datatables.
    if (jQuery('.ir-manual-commission-logs-container table').length) {
        jQuery('.ir-manual-commission-logs-container table').DataTable({
            order: [],
            dom: 'Bfrtip', // cspell:disable-line
            buttons: [
                {
                    text: wdm_commission_data.csv_button_text,
                    extend: 'csv',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },
                    // action: function (e, dt, node, config) {
                    //     jQuery.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, node, config);
                    // },
                    className: 'ir-create-csv-btn button-primary',
                }
            ],
            'columnDefs': [
                {
                    'targets': 4,
                    'orderable': false,
                    'width': "10%"
                },
                { "width": "20%", "targets": 0 },
                { "width": "15%", "targets": [1, 2] },
                { "width": "40%", "targets": 3 },
            ],
            "columns": [
                { "width": "20%" },
                null,
                null,
                null,
                null
            ],
            'language': {
                "decimal": ir_commission_data.i18n.decimal,
                "emptyTable": ir_commission_data.i18n.emptyTable,
                "info": ir_commission_data.i18n.info,
                "infoEmpty": ir_commission_data.i18n.infoEmpty,
                "infoFiltered": ir_commission_data.i18n.infoFiltered,
                "infoPostFix": ir_commission_data.i18n.infoPostFix,
                "thousands": ir_commission_data.i18n.thousands,
                "lengthMenu": ir_commission_data.i18n.lengthMenu,
                "loadingRecords": ir_commission_data.i18n.loadingRecords,
                "processing": ir_commission_data.i18n.processing,
                "search": ir_commission_data.i18n.search,
                "zeroRecords": ir_commission_data.i18n.zeroRecords,
                "paginate": {
                    "first": ir_commission_data.i18n.paginate.first,
                    "last": ir_commission_data.i18n.paginate.last,
                    "next": ir_commission_data.i18n.paginate.next,
                    "previous": ir_commission_data.i18n.paginate.previous
                },
                "aria": {
                    "sortAscending": ir_commission_data.i18n.aria.sortAscending,
                    "sortDescending": ir_commission_data.i18n.aria.sortDescending
                }
            },
        });
    }

    /**
     * Add newly created row to commission logs table.
     *
     * @param {Array} row_data      Array of newly created commission log row to be added.
     */
    function add_manual_commission_log_record(row_data) {
        var table = jQuery('.ir-manual-commission-logs-container table').DataTable();
        var row_node = table.row.add([row_data.date_time, row_data.amount.toFixed(2), row_data.remaining.toFixed(2), row_data.notes, ir_commission_data.manual_commission_log_actions]).draw().node();
        jQuery(row_node).find('td').first().addClass('ir-log-date').data('value', row_data.date_time);
        jQuery(row_node).find('td:nth-of-type(2)').addClass('ir-log-amount').data('value', row_data.amount);
        jQuery(row_node).find('td:nth-of-type(3)').addClass('ir-log-remaining').data('value', row_data.remaining);
        jQuery(row_node).find('td:nth-of-type(4)').addClass('ir-log-note').data('value', row_data.notes);
        jQuery(row_node).find('td').last().addClass('ir-log-actions');
        jQuery(row_node).data('nonce', row_data.nonce).data('log-id', row_data.log_id).attr('id', 'ir_com_log_'+row_data.log_id);

    }
});
