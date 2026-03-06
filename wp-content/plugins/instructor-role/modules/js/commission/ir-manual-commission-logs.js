// cspell:ignore processer


; (function ($) {
    /**
     * Class to handle manual commission logs edit and delete actions.
     * 
     * @since   4.2.0
     */
    class IrCommissionLogProcesser {
        constructor() {
            // Hide column if instructor
            if ( ! ir_loc_data.is_admin ) {
                let log_table = $('.ir-manual-commission-logs-container table').DataTable();
                log_table.column(4).visible(false);
            }
            $(document)
                .on('click', '.ir-log-actions > span.dashicons.dashicons-edit', this.openEditCommissionLogModal)
                .on('click', '.ir-log-actions > span.dashicons.dashicons-no', this.deleteCommissionLog)
                .on('click', '.ir-modal-actions-top > span.dashicons.dashicons-no, #ir-modal-cancel', this.closeEditCommissionLogModal)
                .on('click', '#ir-modal-submit', { instance: this }, this.updateEditCommissionLogModal);
        }
        openEditCommissionLogModal(e) {
            // Get all necessary data for the commission log
            let $commission_log = $(this).parents('tr');
            let commission_log_id = $commission_log.data('log-id');
            let date = $commission_log.find('.ir-log-date').data('value');
            let amount = String( $commission_log.find('.ir-log-amount').data('value') );
            amount = parseFloat(amount.replace(/,/g, ''));
            let note = $commission_log.find('.ir-log-note').data('value');

            // Update modal values
            $('#ir_log_date').val(date);
            $('#ir_log_amount').val(amount);
            $('#ir_original_amount').val(amount);
            $('#ir_log_note').val(note);
            $('#ir_log_id').val(commission_log_id);

            // Open edit modal.
            $('.ir-modal-background').show();
            $('div#ir-commission-log-update-modal').show();

        }
        deleteCommissionLog(e) {
            // Confirm delete message
            if (true !== confirm(ir_loc_data.confirm_delete_log_message)) {
                return;
            }

            // Get all necessary data for the commission log
            let $commission_log = $(this).parents('tr');
            let commission_log_id = $commission_log.data('log-id');
            let log_table = $('.ir-manual-commission-logs-container table').DataTable();
            let unpaid_earnings = parseFloat($('#wdm_amount_paid').text().replace(/,/g, ''));

            // Send ajax to delete the log and update log details
            $.ajax({
                method: "POST",
                url: ir_loc_data.ajax_url,
                data: {
                    action: 'delete_manual_commission_log',
                    nonce: $commission_log.data('nonce'),
                    log_id: commission_log_id
                },
                dataType: 'JSON',
                timeout: 30000,
                success: function (response) {
                    if ('success' === response.data.type) {
                        // Remove log from the commissions table
                        $commission_log.fadeOut();
                        log_table.row($commission_log).remove().draw();
                        // Update Earning details
                        $('#wdm_total_amount_paid_price').attr('value', parseFloat(response.data.paid_earnings));
                        $('#wdm_amount_paid_price').attr('value', parseFloat(response.data.revert_amount + unpaid_earnings));
                        $('#wdm_total_amount_paid').text(parseFloat(response.data.paid_earnings));
                        $('#wdm_amount_paid').text(parseFloat(response.data.revert_amount + unpaid_earnings));
                        // Add pay button in modal if not present.
                        if (!$('#ir_pay_click').length) {
                            $('#instructor_id').after(ir_loc_data.modal_pay_button_html);
                        }
                        // Add pay button in commissions table if not present.
                        if (!$('#wdm_pay_amount').length) {
                            $('#wdm_amount_paid').after(ir_loc_data.main_pay_button_html);
                        }
                    } else {
                        alert(response.data.message);
                    }
                },
                beforeSend: function () {
                    $('.ir-loader-screen').show();
                },
                complete: function () {
                    $('.ir-loader-screen').hide();
                }
            });
        }
        closeEditCommissionLogModal(e) {
            $('div#ir-commission-log-update-modal').hide();
            $('.ir-modal-background').hide();

            // Reset edit modal values.
            $('#ir_log_date').val('');
            $('#ir_log_amount').val(0);
            $('#ir_original_amount').val(0);
            $('#ir_log_note').val('');
            $('#ir_log_id').val('');
        }
        updateEditCommissionLogModal(e) {
            // Get all necessary data for the commission log
            let commission_log_id = $('#ir_log_id').val();
            let log_table = $('.ir-manual-commission-logs-container table').DataTable();
            let unpaid_earnings = parseFloat($('#wdm_amount_paid').text().replace(/,/g, ''));
            let date = $('#ir_log_date').val();
            let amount = parseFloat($('#ir_log_amount').val().replace(/,/g, ''));
            let original_amount = parseFloat($('#ir_original_amount').val().replace(/,/g, ''));
            let note = $('#ir_log_note').val();
            let $row = jQuery('.ir-manual-commission-logs-container table tr#ir_com_log_' + commission_log_id);
            let row_index = log_table.row($row).index();

            // Check if valid date and amount.
            if (!date.length) {
                alert(ir_loc_data.invalid_date_message);
                return;
            }

            // Check if amount not less than 0.
            if (amount <= 0 || isNaN(amount)) {
                alert(ir_loc_data.invalid_amount_message);
                return;
            }

            // Check if amount not more than what is owed.
            let difference = amount - original_amount;
            if (amount > original_amount) {
                if (difference > unpaid_earnings) {
                    alert(ir_loc_data.additional_amount_message);
                    return;
                }
            }

            // Check if note length not more than specified length.
            if (note.length > ir_loc_data.note_limit) {
                alert(ir_loc_data.invalid_note_length);
                return;
            }

            // Send ajax to delete the log and update log details
            $.ajax({
                method: "POST",
                url: ir_loc_data.ajax_url,
                data: {
                    action: 'update_manual_commission_log',
                    nonce: $('#ir_update_nonce').val(),
                    log_id: commission_log_id,
                    date: date,
                    amount: amount,
                    note: note
                },
                dataType: 'JSON',
                timeout: 30000,
                success: function (response) {
                    if ('success' === response.data.type) {
                        // Update Earning details.
                        $('#wdm_total_amount_paid_price').attr('value', parseFloat(response.data.paid_earnings));
                        $('#wdm_amount_paid_price').attr('value', parseFloat(unpaid_earnings - difference));
                        $('#wdm_total_amount_paid').text(parseFloat(response.data.paid_earnings));
                        $('#wdm_amount_paid').text(parseFloat(unpaid_earnings - difference));

                        // Update row data.
                        let date_cell = log_table.cell({ row: row_index, column: 0 }).data(date).node();
                        $(date_cell).data('value', date);
                        let amount_cell = log_table.cell({ row: row_index, column: 1 }).data(amount).node();
                        $(amount_cell).data('value', amount);
                        let rem_cell = log_table.cell({ row: row_index, column: 2 }).data(unpaid_earnings - difference).node();
                        $(rem_cell).data('value', unpaid_earnings - difference);
                        let note_cell = log_table.cell({ row: row_index, column: 3 }).data(note).node();
                        $(note_cell).data('value', note);

                        // Add pay button in modal if not present.
                        if (!$('#ir_pay_click').length) {
                            $('#instructor_id').after(ir_loc_data.modal_pay_button_html);
                        }
                        // Add pay button in commissions table if not present.
                        if (!$('#wdm_pay_amount').length) {
                            $('#wdm_amount_paid').after(ir_loc_data.main_pay_button_html);
                        }
                    } else {
                        alert(response.data.message);
                    }
                    e.data.instance.closeEditCommissionLogModal();
                },
                beforeSend: function () {
                    $('div.ir-modal-loader-screen').show();
                },
                complete: function () {
                    $('div.ir-modal-loader-screen').hide();
                }
            });
        }
    }

    $(document).ready(
        function () {
            var irCommissionLogProcesser = new IrCommissionLogProcesser();
            jQuery("#ir_log_date").datetimepicker({
                format: 'Y-m-d H:i:s',
                maxDate: 0
            });
        }
    );

})(jQuery);
