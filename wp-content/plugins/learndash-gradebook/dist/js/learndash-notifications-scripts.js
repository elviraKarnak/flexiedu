/******/ (() => { // webpackBootstrap
/*!********************************************************!*\
  !*** ./src/assets/js/admin/learndash-notifications.js ***!
  \********************************************************/
/* eslint-disable -- TODO: fix linting issues */

(function ($, l10n) {
  $(document).on('ready', function () {
    $('select[name="_ld_notifications_gradebook_id[]"]').select2({
      dropdownAutoWidth: true,
      width: '100%',
      theme: 'learndash',
      ajax: {
        url: l10n.rest.get_gradebooks,
        delay: 250,
        data: function data(params) {
          var query = {
            term: params.term,
            page: params.page || 1
          };
          return query;
        },
        transport: function transport(params, success, failure) {
          if (typeof params.headers === 'undefined') params.headers = {};
          params.headers['X-WP-Nonce'] = $('#learndash_gradebook_notifications_nonce').val();
          params.cache = false;
          var $request = $.ajax(params);
          $request.then(success);
          $request.fail(failure);
          return $request;
        }
      }
    });
  });
  $(document).on('change', 'select[name="_ld_notifications_trigger"]', function (event) {
    var select = this,
      $recipients = $('#ld-notifications-meta-box input[name="_ld_notifications_recipient[]"]');

    // Only User and Admin apply as Recipients for our Triggers, so we don't want to allow confusion by showing other options.
    $recipients.each(function (index, input) {
      var $label = $('label[for="' + $(input).attr('id') + '"]');
      if (['user', 'admin'].indexOf($(input).val()) < 0) {
        if ($(select).val() === 'ld_gb_manual_grade_added') {
          $(input).prop('checked', false).trigger('change');
          $(input).hide();
          $label.hide();
          $label.next('br').hide();
        } else {
          $(input).show();
          $label.show();
          $label.next('br').show();
        }
      }
    });
  });

  // Trigger page on page load as a quick way to run the above for saved Notifications
  $('select[name="_ld_notifications_trigger"]').trigger('change');
})(jQuery, ldGbNotifications.l10n);
/******/ })()
;