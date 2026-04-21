/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/assets/js/admin/dashboard-widget.js":
/*!*************************************************!*\
  !*** ./src/assets/js/admin/dashboard-widget.js ***!
  \*************************************************/
/***/ (() => {

/* eslint-disable -- TODO: fix linting issues */

/**
 * Gradebook page functionality.
 *
 * @param      $
 * @since 1.1.0
 *
 * @package
 * @subpackage LearnDash_Gradebook/assets/src/js/admin
 */

(function ($) {
  function init_dashboard_widget() {
    $(document).on('change', '[name="ld_gb_widget_gradebook"], [name="ld_gb_widget_group"]', update_widget_data);
  }
  function update_widget_data() {
    var $widget = $(this).closest('.postbox');
    var $user_table = $widget.find('.ld-gb-widget-users');
    var $user_template = $user_table.find('.ld-gb-widget-user-template');
    var $gradebook = $widget.find('[name="ld_gb_widget_gradebook"]');
    var gradebook_id = $gradebook.length && $gradebook.find('option:selected').val() || 0;
    var $group = $widget.find('[name="ld_gb_widget_group"]');
    var group_id = $group.length && $group.find('option:selected').val() || 0;
    var $overlay = $('<div class="ld-gb-widget-users-overlay"><span class="spinner"></span></div>');
    $widget.find('.ld-gb-widget-users-container').append($overlay);
    $.post(ajaxurl, {
      action: 'ld_gb_dashboard_widget_get_data',
      group_id: group_id,
      gradebook_id: gradebook_id,
      user_id: LD_GB_Admin.current_user_id
    }, function (response) {
      if ($gradebook.length) {
        $widget.find('.ld-gb-widget-gradebook-name').html($gradebook.find('option:selected').html().trim());
      }
      $user_table.find('tr:not(.ld-gb-widget-user-template)').remove();
      if (response.data.users) {
        $.each(response.data.users, function (user_ID, user) {
          var $new_row = $user_template.clone();
          $new_row.find('.ld-gb-widget-user-name').html(user.name);
          $new_row.find('.ld-gb-widget-user-grade').html(user.grade);
          $new_row.removeClass('ld-gb-widget-user-template');
          $new_row.show();
          $user_table.append($new_row);
        });
      } else {
        $user_table.append('<tr><td>' + LD_GB_Admin.l10n.no_users + '</td></tr>');
      }
      $overlay.remove();
    });
  }
  $(init_dashboard_widget);
})(jQuery);

/***/ }),

/***/ "./src/assets/js/admin/gradebook-page.js":
/*!***********************************************!*\
  !*** ./src/assets/js/admin/gradebook-page.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _lib_update_url__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../lib/update-url */ "./src/assets/js/lib/update-url.js");
/* eslint-disable -- TODO: fix linting issues */

/**
 * Gradebook page functionality.
 *
 * @since 1.1.0
 *
 * @package
 * @subpackage LearnDash_Gradebook/assets/src/js/admin
 */

var LD_GB_Main;

(function ($, l10n) {
  var api = LD_GB_Main = {
    /**
     * Initializes.
     *
     * @since 1.1.0
     */
    init: function init() {
      $(document).on('ready', api.initSelect2());
      $('#ld-gb-gradebook-selector, #ld-gb-group-selector').change(api.gradebook_form_submit);
      $('#export-gradebook-components').on('submit', api.gradebook_export_components_submit);
      $('#export-gradebook-all-grades').on('submit', api.gradebook_export_all_grades_submit);
    },
    /**
     * Initializes Select2 dropdowns
     *
     * @since   4.3.0
     * @return  {void}
     */
    initSelect2: function initSelect2() {
      $('#ld-gb-gradebook-selector').select2({
        theme: 'learndash',
        ajax: {
          url: l10n.rest.select2_get_gradebooks,
          delay: 250,
          data: function data(params) {
            var query = {
              term: params.term || '',
              page: params.page || 1
            };
            return query;
          },
          transport: function transport(params, success, failure) {
            if (typeof params.headers === 'undefined') params.headers = {};
            params.headers['X-WP-Nonce'] = l10n.nonce;
            params.cache = false;
            var $request = $.ajax(params);
            $request.then(success);
            $request.fail(failure);
            return $request;
          }
        }
      });
      $('#ld-gb-group-selector').select2({
        theme: 'learndash'
      });
    },
    /**
     * Fires when the gradebook select changes, and submits the form.
     *
     * @since 1.2.0
     */
    gradebook_form_submit: function gradebook_form_submit() {
      $(this).closest('form').submit();
    },
    /**
     * Fires when a Gradebook Component CSV Export is requested
     *
     * @param {Object} event JavaScript Event Object
     *
     * @since   2.0.0
     * @return  {void}
     */
    gradebook_export_components_submit: function gradebook_export_components_submit(event) {
      event.preventDefault();
      var $submitButton = $(this).find('input[type="submit"]');
      $submitButton.data('default_text', $submitButton.val());
      $submitButton.val($submitButton.data('submitting_text')).attr('disabled', true);
      var csv = '',
        gradebook_id = parseInt($(this).find('input[type="submit"]').data('gradebook_id')),
        group_id = parseInt($(this).find('input[type="submit"]').data('group_id')),
        perPage = parseInt($(this).find('input[type="submit"]').data('per_page'));
      api.get_csv_component_data(gradebook_id, group_id, csv, 1, perPage);
    },
    /**
     * Continue to hit the CSV Component Export Endpoint until there are no more results, then generate a download link for the complete results
     *
     * @param integer      gradebook_id  Gradebook ID
     * @param integer      group_id      Group ID. 0 for All Users
     * @param string       csv           CSV String that is being built
     * @param integer      page          Page Number of results
     * @param integer      perPage       Per Page
     *
     * @param gradebook_id
     * @param group_id
     * @param csv
     * @param page
     * @param perPage
     * @since   2.0.0
     * @return  void
     */
    get_csv_component_data: function get_csv_component_data(gradebook_id, group_id, csv, page, perPage) {
      var url = l10n.rest.base + 'export-gradebook-component-data/' + gradebook_id + '/' + group_id + '/',
        queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_0__.getQueryString)(url);

      // We need to build our API Endpoint like this to account for people who are not using pretty permalinks
      url = url.replace(queryString, '');
      queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_0__.updateURLParam)(queryString, 'paged', page);
      queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_0__.updateURLParam)(queryString, 'per_page', perPage);
      url = url + queryString;
      $.ajax({
        url: url,
        method: 'GET',
        cache: false,
        headers: {
          'X-WP-Nonce': l10n.nonce
        },
        success: function success(response) {
          var newData = response.csv_data;
          if (csv.length != 0) {
            var lines = newData.split('\n');
            lines.splice(0, 1);
            newData = lines.join('\n');
            newData = '\n' + newData;
          }
          csv = csv + newData;
          if (parseInt(page) < response.total_pages) {
            api.get_csv_component_data(gradebook_id, group_id, csv, parseInt(page) + 1, perPage);
          } else {
            // We've gathered every page
            api.generate_download(csv, 'gradebook-' + gradebook_id + '-' + group_id + '-components.csv');
            $('#export-gradebook-components input[type="submit"]').val($('#export-gradebook-components input[type="submit"]').data('default_text')).attr('disabled', false);
          }
        },
        error: function error(request, status, _error) {
          var errorMessage = JSON.parse(request.responseText);
          if (typeof errorMessage.message !== 'undefined') {
            console.error(errorMessage.message);
          } else if (typeof errorMessage.exception !== 'undefined') {
            console.error(errorMessage.exception);
          }
          $('#export-gradebook-components input[type="submit"]').val($('#export-gradebook-components input[type="submit"]').data('default_text')).attr('disabled', false);
        }
      });
    },
    /**
     * Fires when a Gradebook All Grades CSV Export is requested
     *
     * @param {Object} event JavaScript Event Object
     *
     * @since   2.0.0
     * @return  {void}
     */
    gradebook_export_all_grades_submit: function gradebook_export_all_grades_submit(event) {
      event.preventDefault();
      var $submitButton = $(this).find('input[type="submit"]');
      $submitButton.data('default_text', $submitButton.val());
      $submitButton.val($submitButton.data('submitting_text')).attr('disabled', true);
      var csv = '',
        gradebook_id = parseInt($(this).find('input[type="submit"]').data('gradebook_id')),
        group_id = parseInt($(this).find('input[type="submit"]').data('group_id')),
        perPage = parseInt($(this).find('input[type="submit"]').data('per_page'));
      api.get_csv_all_grades_data(gradebook_id, group_id, csv, 1, perPage);
    },
    /**
     * Continue to hit the CSV All Grades Export Endpoint until there are no more results, then generate a download link for the complete results
     *
     * @param integer      gradebook_id  Gradebook ID
     * @param integer      group_id      Group ID. 0 for All Users
     * @param string       csv           CSV String that is being built
     * @param integer      page          Page Number of results
     * @param integer      perPage       Per Page
     *
     * @param gradebook_id
     * @param group_id
     * @param csv
     * @param page
     * @param perPage
     * @since   2.0.0
     * @return  void
     */
    get_csv_all_grades_data: function get_csv_all_grades_data(gradebook_id, group_id, csv, page, perPage) {
      var url = l10n.rest.base + 'export-gradebook-all-grades/' + gradebook_id + '/' + group_id + '/',
        queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_0__.getQueryString)(url);
      url = url.replace(queryString, '');
      queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_0__.updateURLParam)(queryString, 'paged', page);
      queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_0__.updateURLParam)(queryString, 'per_page', perPage);
      url = url + queryString;
      $.ajax({
        url: url,
        method: 'GET',
        cache: false,
        headers: {
          'X-WP-Nonce': l10n.nonce
        },
        success: function success(response) {
          var newData = response.csv_data;
          if (newData) {
            if (csv.length != 0) {
              var lines = newData.split('\n');
              lines.splice(0, 1);
              newData = lines.join('\n');
              newData = '\n' + newData;
            }
            csv = csv + newData;
          }
          if (parseInt(page) < response.total_pages) {
            api.get_csv_all_grades_data(gradebook_id, group_id, csv, parseInt(page) + 1, perPage);
          } else {
            // We've gathered every page
            api.generate_download(csv, 'gradebook-' + gradebook_id + '-' + group_id + '-all-grades.csv');
            $('#export-gradebook-all-grades input[type="submit"]').val($('#export-gradebook-all-grades input[type="submit"]').data('default_text')).attr('disabled', false);
          }
        },
        error: function error(request, status, _error2) {
          var errorMessage = JSON.parse(request.responseText);
          if (typeof errorMessage.message !== 'undefined') {
            console.error(errorMessage.message);
          } else if (typeof errorMessage.exception !== 'undefined') {
            console.error(errorMessage.exception);
          }
          $('#export-gradebook-all-grades input[type="submit"]').val($('#export-gradebook-all-grades input[type="submit"]').data('default_text')).attr('disabled', false);
        }
      });
    },
    /**
     * Generates a download and forces a click on the element
     *
     * @param {string} contents    File Contents
     * @param {string} filename    File Name
     * @param {string} contentType Content Type
     *
     * @since   2.0.0
     * @return  {void}
     */
    generate_download: function generate_download(contents, filename, contentType) {
      if (typeof filename === 'undefined') filename = 'download.txt';
      if (typeof contentType === 'undefined') contentType = 'attachment/text';
      var hiddenElement = document.createElement('a');
      hiddenElement.href = 'data:' + contentType + ',' + api.rfc3986EncodeURIComponent(contents);
      hiddenElement.target = '_blank';
      hiddenElement.download = filename;
      hiddenElement.click();
      hiddenElement.remove();
    },
    /**
     * People with names like O'Neal break the export without this change
     * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent
     *
     * @param {string} str String to encode
     *
     * @return  {string}       Encoded string
     */
    rfc3986EncodeURIComponent: function rfc3986EncodeURIComponent(str) {
      return encodeURIComponent(str).replace(/[!'()*]/g, function (c) {
        return '%' + c.charCodeAt(0).toString(16);
      }).replace(/\u00C0-\u024F\u1E00-\u1EFF/, function (c) {
        return '%' + c.charCodeAt(0).toString(8);
      });
    }
  };
  $(api.init);
})(jQuery, LD_GB_Admin.l10n);

/***/ }),

/***/ "./src/assets/js/admin/gradebook-post-page.js":
/*!****************************************************!*\
  !*** ./src/assets/js/admin/gradebook-post-page.js ***!
  \****************************************************/
/***/ (() => {

/* eslint-disable -- TODO: fix linting issues */

/**
 * Gradebook Post Edit page functionality.
 *
 * @param      $
 * @param      l10n
 * @since 1.2.0
 *
 * @package
 * @subpackage LearnDash_Gradebook/assets/src/js/admin
 */

(function ($, l10n) {
  var api = {
    /**
     * If system is using weights or not.
     *
     * @since 1.2.0
     *
     * @member bool
     */
    isWeighted: false,
    /**
     * Initializes.
     *
     * @since 1.2.0
     */
    init: function init() {
      api.$components = $('[data-fieldhelpers-field-repeater="ld_gb_components"]');
      if (!api.$components.length) {
        return;
      }
      api.initGradebookWeightingInterface();
      api.initGradebookComponents();
    },
    /**
     * Initializes the interface for modifying Gradebook weighting.
     *
     * @since 1.2.0
     */
    initGradebookWeightingInterface: function initGradebookWeightingInterface() {
      api.$interface = $('#ld-gb-gradebook-weighting');
      if (!api.$interface.length) {
        return;
      }

      // Initial load
      api.weightingUpdateComponentList();

      // Setup handlers
      $('[data-fieldhelpers-name="ld_gb_gradebook_weighting_enable"] [data-fieldhelpers-field-toggle]').change(api.toggleInterfaceVisibility);
      $(document).on('keyup', '[name^="ld_gb_components"][name$="[name]"]', api.weightingUpdateComponentList);
      api.$components.on('repeater-add-item', api.weightingUpdateComponentList);
      api.$components.on('repeater-delete-item', api.weightingUpdateComponentList);
      api.$components.find('.fieldhelpers-field-repeater-list').on('list-update', api.weightingUpdateComponentList);
      $('form#post').submit(api.validateComponentWeights);
    },
    /**
     * Shows or hides the interface.
     *
     * @param e
     * @param toggleValue
     * @since 1.2.0
     */
    toggleInterfaceVisibility: function toggleInterfaceVisibility(e, toggleValue) {
      if (toggleValue === '1') {
        api.isWeighted = true;
        api.$interface.show();
      } else {
        api.isWeighted = false;
        api.$interface.hide();
        api.setInterfaceError(100); // Disable error if exists
      }
    },
    /**
     * Checks component weights to validate equalling 100.
     *
     * @param event
     * @since 1.2.0
     */
    validateComponentWeights: function validateComponentWeights(event) {
      // Bail if not using weights
      if (!api.isWeighted) {
        return;
      }
      var totalWeight = 0;
      api.$interface.find('.ld-gb-gradebook-weighting-component').each(function () {
        var weight = parseInt($(this).find('[name^="ld_gb_components"][name$="[weight]"]').val());
        totalWeight = weight + totalWeight;
      });
      if (totalWeight !== 100) {
        event.preventDefault();
      }
      api.setInterfaceError(totalWeight);
    },
    /**
     * Sets the display of the interface based on validity.
     *
     * @since 1.2.0
     * @param weight
     *
     * @param invalid
     */
    setInterfaceError: function setInterfaceError(weight) {
      var $container = api.$interface.closest('.postbox');
      var $error = $container.find('.ld-gb-component-weighting-error-message');
      if (weight !== 100) {
        $container.addClass('ld-gb-component-weighting-error');
        $error.show().find('.current-weight').html(weight);
      } else {
        $container.removeClass('ld-gb-component-weighting-error');
        $error.hide();
      }
      if (weight > 100) {} else if (weight < 100) {}
    },
    /**
     * Updates the list of components.
     *
     * @since 1.2.0
     */
    weightingUpdateComponentList: function weightingUpdateComponentList() {
      var components = api.weightingGetComponents();
      var currentWeights = {};
      components.map(function (component, i) {
        var $interfaceComponent = api.$interface.find('.ld-gb-gradebook-weighting-component[data-id="' + component.id + '"]');
        if ($interfaceComponent.length) {
          currentWeights[component.id] = $interfaceComponent.find('input[name$="[weight]"]').val();
        }
      });
      api.$interface.find('tbody').html('');
      components.map(function (component, i) {
        var value = currentWeights[component.id] || 0;
        var $interfaceComponent = $('<tr class="ld-gb-gradebook-weighting-component" data-id="' + component.id + '">' + '<td class="ld-gb-gradebook-weighting-component-name">' + component.name + '</td>' + '<td class="ld-gb-gradebook-weighting-component-weight">' + '<input type="number" min="0" max="100" value="' + value + '" ' + 'name="ld_gb_components[' + i + '][weight]" />' + '</td>' + '</tr>');
        $interfaceComponent.show();
        api.$interface.find('tbody').append($interfaceComponent);
      });
    },
    /**
     * Gets all components to set weights for.
     *
     * @since 1.2.0
     *
     * @return {Array}
     */
    weightingGetComponents: function weightingGetComponents() {
      var $componentItems = api.$components.find('[data-repeater-item]');
      var components = [];
      $componentItems.each(function () {
        var name = $(this).find('input[name$="[name]"]').val() || l10n.component_no_name;
        var component = {
          name: name,
          id: $(this).find('input[name$="[id]"]').val()
        };
        components.push(component);
      });
      return components;
    },
    /**
     * Adds some custom functionality to the Components repeater.
     *
     * @since 1.2.0
     */
    initGradebookComponents: function initGradebookComponents() {
      // Initial fields
      api.$components.find('[data-fieldhelpers-field-toggle]').on('change', api.toggleGradebookComponentsSelect);
      api.$components.on('repeater-add-item', function (e, $item) {
        api.componentLoading(true, $item);
        $item.find('[data-fieldhelpers-field-toggle]').on('change', api.toggleGradebookComponentsSelect);
        api.toggleLessonsTopics(api.$course.val());
        api.setComponentsError(false);
        api.setComponentID($item);
      });

      // Course select and options
      api.$course = $('select[name="ld_gb_course[]"]');
      api.$course.data('last_selection', api.$course.val());
      if (api.$course.length) {
        api.$course.change(function () {
          if ($(this).val().length > 1 && $(this).val().indexOf('all') > -1) {
            if (api.$course.data('last_selection').indexOf('all') > -1) {
              $(this).val($(this).val().filter(function (value) {
                return value != 'all';
              }));
              $(this).trigger('change');
              return;
            }
            $(this).val(['all']);
            $(this).trigger('change');
            return;
          }
          api.$course.data('last_selection', $(this).val());
          api.componentsLoading(true);
          api.setCourseOptions($(this).val());
          api.toggleLessonsTopics($(this).val());
        });

        // Initialize
        api.$course.trigger('change');
      }

      // Some must exist
      $('form#post').submit(api.validateComponents);
    },
    /**
     * Sets a component loading.
     *
     * @since 1.2.0
     *
     * @param {bool}   loading Loading or not.
     * @param {jQuery} $item   Component item.
     */
    componentLoading: function componentLoading(loading, $item) {
      if (loading) {
        $item.append($('<div class="ld-gb-loading-overlay"><span class="spinner is-active"></span></div>'));
      } else {
        $item.find('.ld-gb-loading-overlay').remove();
      }
    },
    /**
     * Determines if Components are valid for submission.
     *
     * @param event
     * @since 1.2.0
     */
    validateComponents: function validateComponents(event) {
      if (!api.$components.find('[data-repeater-item]').length) {
        event.preventDefault();
        api.setComponentsError(true);
      } else {
        api.setComponentsError(false);
      }
    },
    /**
     * Sets the display of the Components list based on validity.
     *
     * @param show
     * @since 1.2.0
     */
    setComponentsError: function setComponentsError(show) {
      var $container = api.$components.closest('.postbox');
      var $error = $container.find('.ld-gb-component-error-message');
      if (show) {
        $container.addClass('ld-gb-component-error');
        $error.show();
      } else {
        $container.removeClass('ld-gb-component-error');
        $error.hide();
      }
    },
    /**
     * Sets the unique ID of the Component.
     *
     * @since 1.2.0
     *
     * @param {jQuery} $item New repeater item.
     */
    setComponentID: function setComponentID($item) {
      $.ajax({
        method: 'POST',
        url: ajaxurl,
        data: {
          action: 'ld_gb_get_new_component_id',
          gradebook_id: LD_GB_Admin.gradebook_id
        }
      }).done(function (response) {
        if (!response.success) {
          return;
        }
        $item.find('[data-fieldhelpers-name="id"] input[type="hidden"]').val(response.id);
        api.componentLoading(false, $item);
      });
    },
    /**
     * Adds or removes an overlay from the components repeater.
     *
     * @since 1.2.0
     *
     * @param loading
     */
    componentsLoading: function componentsLoading(loading) {
      if (loading) {
        api.$components.append('<div class="ld-gb-loading-overlay"><span class="spinner is-active"></span></div>');
      } else {
        api.$components.find('.ld-gb-loading-overlay').remove();
      }
    },
    /**
     * Sets the options for all dropdowns in components relating to the course.
     *
     * @since 1.2.0
     *
     * @param courses
     */
    setCourseOptions: function setCourseOptions(courses) {
      $.ajax({
        method: 'POST',
        url: ajaxurl,
        data: {
          action: 'ld_gb_get_component_options',
          courses: courses,
          postID: $('#post_ID').val(),
          postStatus: $('#original_post_status').val()
        }
      }).done(function (response) {
        $('select.ld-gb-component-items-select').each(function () {
          var $select = $(this);
          var selected = [];
          var type = $(this).attr('data-type');
          $select.find('option:selected').each(function () {
            selected.push(parseInt($(this).val()));
          });
          $select.html('');
          response.data[type].map(function (item) {
            var isSelected = selected.indexOf(item.value) !== -1;
            $select.append($('<option value="' + item.value + '" ' + (isSelected && 'selected') + '>' + item.text + '</option>'));
          });
        });
        api.componentsLoading(false);
      });
    },
    /**
     * Toggles visibility of lesson/topic fields based on presence of course.
     *
     * @since 1.2.0
     *
     * @param courses
     */
    toggleLessonsTopics: function toggleLessonsTopics(courses) {
      var $fields = $('[data-fieldhelpers-name="lessons_section_divider"],' + '[data-fieldhelpers-name="lessons"],' + '[data-fieldhelpers-name="lessons_all"],' + '[data-fieldhelpers-name="lessons_clearfix"],' + '[data-fieldhelpers-name="topics_section_divider"],' + '[data-fieldhelpers-name="topics"],' + '[data-fieldhelpers-name="topics_all"],' + '[data-fieldhelpers-name="topics_clearfix"]');
      if (!courses) {
        $fields.hide();
      } else {
        $fields.show();
      }
    },
    /**
     * Handles disabling/enabling the select fields when the toggle field changes.
     *
     * @since 1.2.0
     *
     * @param e
     * @param toggleValue
     */
    toggleGradebookComponentsSelect: function toggleGradebookComponentsSelect(e, toggleValue) {
      var fieldID = $(this).closest('.fieldhelpers-field-toggle').attr('data-fieldhelpers-name');
      var group = $(this).find('.fieldhelpers-field-input').attr('data-disable-group');
      var $fields = $(this).closest('.fieldhelpers-field-repeater-content').find('[data-disable-from-all="' + group + '"]');
      if (toggleValue === '1') {
        $fields.prop('disabled', true);
      } else {
        $fields.prop('disabled', false);
      }
    }
  };
  $(api.init);
})(jQuery, LD_GB_Admin.l10n);

/***/ }),

/***/ "./src/assets/js/admin/main.js":
/*!*************************************!*\
  !*** ./src/assets/js/admin/main.js ***!
  \*************************************/
/***/ (() => {

/* eslint-disable -- TODO: fix linting issues */

/**
 * Main global functionality.
 *
 * @param $
 * @param l10n
 * @since 1.0.0
 *
 * @global
 */
(function ($, l10n) {
  'use strict';

  var api = {
    /**
     * Initializes the api.
     *
     * @since 1.0.0
     */
    init: function init() {
      api.notices();
      api.shift_key_utility();
    },
    /**
     * Dismissible LD GB notices.
     *
     * @since 1.0.1
     */
    notices: function notices() {
      var $notices = $('.ld-gb-notice.is-dismissible');
      $notices.each(function () {
        // cspell:disable-next-line.
        if ($(this).hasClass('not-dismissable')) {
          $(this).removeClass('is-dismissible');
        } else {
          $(this).find('.notice-dismiss').click(api.dismiss_notice);
        }
      });
    },
    /**
     * Dismisses a notice.
     *
     * @since 1.0.1
     */
    dismiss_notice: function dismiss_notice() {
      var $notice = $(this).closest('.ld-gb-notice'),
        id = $notice.attr('id');
      $.post(ajaxurl, {
        action: 'ld_gb_dismiss_notice',
        id: id
      });
    },
    /**
     * Make it easy to determine if shift key is down or up.
     *
     * @since 1.1.0
     */
    shift_key_utility: function shift_key_utility() {
      window.ld_gb_shift_key_down = false;
      $(document).on('keydown', function (e) {
        if (e.which === 16) {
          window.ld_gb_shift_key_down = true;
        }
      });
      $(document).on('keyup', function (e) {
        if (e.which === 16) {
          window.ld_gb_shift_key_down = false;
        }
      });
    }
  };
  $(api.init);
})(jQuery, LD_GB_Admin.l10n);

/***/ }),

/***/ "./src/assets/js/admin/quickstart-mocks.js":
/*!*************************************************!*\
  !*** ./src/assets/js/admin/quickstart-mocks.js ***!
  \*************************************************/
/***/ (() => {

/* eslint-disable -- TODO: fix linting issues */

/**
 * Quick start mock modifications.
 *
 * @param $
 * @param l10n
 * @since 1.2.0
 */
(function ($, l10n) {
  'use strict';

  function init_quickstart_mocks() {
    if (!$('body').hasClass('ld-gb-quickstart')) {
      return;
    }
    if ($('body').is('.post-php, .post-type-gradebook')) {
      gradebook_post_edits();
    }
    if ($('body').hasClass('admin_page_learndash-gradebook-user-grades')) {
      user_grades_edits();
    }
  }
  function gradebook_post_edits() {
    $('form#post').submit(function (e) {
      e.preventDefault();
    });
    $('[data-fieldhelpers-name="ld_gb_components"]').find('[data-repeater-delete], [data-repeater-create]').prop('disabled', true);
  }
  function user_grades_edits() {
    $('.ld-gb-return-button').remove();
    $('[data-edit-grade], [data-ld-gb-component-grade], [data-add-manual-grade]').click(function (e) {
      e.preventDefault();
      alert(l10n.disabled_for_quickstart);
      return false;
    });
  }
  $(init_quickstart_mocks);
})(jQuery, LD_GB_Admin.l10n);

/***/ }),

/***/ "./src/assets/js/admin/quickstart.js":
/*!*******************************************!*\
  !*** ./src/assets/js/admin/quickstart.js ***!
  \*******************************************/
/***/ (() => {

/* eslint-disable -- TODO: fix linting issues */

/**
 * Quick start.
 *
 * @param $
 * @param l10n
 * @since 1.0.0
 *
 * @global
 */
(function ($, l10n) {
  'use strict';

  var pointers = [],
    current_pointer;
  function init_quickstart() {
    if (typeof LD_GB_Quickstart === 'undefined' || !LD_GB_Quickstart) {
      return;
    }
    $.each(LD_GB_Quickstart, function (i, pointer) {
      // Dismissible.
      var options = $.extend(pointer.options, {
        pointerClass: 'wp-pointer ld-gb-pointer',
        pointerWidth: 450,
        /**
         * Extended original to call AJAX disable quickstart.
         *
         * @since 1.0.0
         */
        close: function close() {
          $.post(ajaxurl, {
            action: 'ld_gb_disable_quickstart'
          }, function (response) {
            if (typeof response.success === 'undefined' || !response.success) {
              if (typeof response.data !== 'undefined' && typeof response.error !== 'undefined') {
                alert(response.error);
              } else {
                alert(l10n.disable_quickstart_error_generic);
              }
            } else {
              // Refresh page (get rid of any possible mock data)

              var url = window.location.href,
                paramsToRemove = ['ld_gb_quickstart', 'gradebook', 'post'],
                parameter;
              for (var paramIndex in paramsToRemove) {
                parameter = paramsToRemove[paramIndex];
                var url_parts = url.split('?');
                if (url_parts.length >= 2) {
                  var prefix = encodeURIComponent(parameter) + '=';
                  var params = url_parts[1].split(/[&;]/g);
                  for (var _i = params.length; _i-- > 0;) {
                    if (params[_i].lastIndexOf(prefix, 0) !== -1) {
                      params.splice(_i, 1);
                    }
                  }
                  url = url_parts[0] + (params.length > 0 ? '?' + params.join('&') : '');
                }
              }
              window.location = url;
            }
          });
        },
        /**
         * Extended original function to include quickstart.
         *
         * NOTE: The buttons are wrapped in another <div> because if they are not, each button will be wrapped
         * separately into .wp-pointer-buttons by WP.
         *
         * @since 1.0.0
         *
         * @param  event
         * @param  t
         * @return {*|(function(this:string))}
         */
        buttons: function buttons(event, t) {
          var dismiss = l10n.dismiss,
            quickstart = pointer.options.quickstart_text ? pointer.options.quickstart_text : l10n.quickstart_text,
            quickstart_link = pointer.options.quickstart_link ? pointer.options.quickstart_link : false,
            quickstart_back = pointer.options.quickstart_back_text ? pointer.options.quickstart_back_text : l10n.quickstart_back_text,
            quickstart_back_link = pointer.options.quickstart_back_link ? pointer.options.quickstart_back_link : false,
            dismiss_button = '<a class="close" href="#">' + dismiss + '</a>',
            quickstart_back_button = '<a class="quickstart-back" href="' + quickstart_back_link + '">' + quickstart_back + '</a>',
            quickstart_button = '<a class="button quickstart" href="' + quickstart_link + '">' + quickstart + '</a>',
            buttons = $('<div class="ld-gb-quickstart-buttons">' + dismiss_button + quickstart_button + quickstart_back_button + '</div>');

          // If no link, remove button
          if (quickstart_link === false) {
            buttons.find('.quickstart').remove();
          }
          if (quickstart_back_link === false) {
            buttons.find('.quickstart-back').remove();
          }
          buttons.on('click', '.quickstart', load_next);
          buttons.on('click', '.quickstart-back', load_previous);
          return buttons.on('click', '.close', function (e) {
            e.preventDefault();
            t.element.pointer('close');
          });
        }
      });
      pointers.push(pointer);
    });

    // Load first
    if (pointers.length) {
      $(window).on('load', function () {
        current_pointer = 0;
        load_pointer(pointers[0]);
      });
    }
  }

  /**
   * Loads the pointer.
   *
   * @since 1.0.0
   *
   * @param {Object} pointer
   */
  function load_pointer(pointer) {
    var $pointer_target = $(pointer.target),
      $pointer_widget,
      scroll_to;
    $(pointer.target).pointer(pointer.options).pointer('open');
    $pointer_widget = $pointer_target.pointer('widget');

    // Sometimes gets off
    $pointer_target.pointer('reposition');

    // Scroll to
    if (!$pointer_widget.hasClass('wp-pointer-bottom')) {
      scroll_to = $pointer_widget.offset().top - $pointer_widget.height();
    } else {
      scroll_to = $pointer_widget.offset().top;
    }

    // Admin menu positioned pointer should be fixed
    if ($pointer_target.closest('#adminmenu').length) {
      $pointer_widget.css('position', 'fixed');

      // Make sure it isn't cutoff
      if ($pointer_widget.position().top + $pointer_widget.height() > $(window).height()) {
        $pointer_widget.css('top', $(window).height() - $pointer_widget.height());
      }
      if ($pointer_widget.position().top < 0) {
        $pointer_widget.css('top', 0);
      }
    } else {
      $('body, html').animate({
        scrollTop: scroll_to - $('#wpadminbar').height()
      }, 500);
    }
  }

  /**
   * Unloads a pointer.
   *
   * @since 1.0.0
   *
   * @param pointer
   */
  function unload_pointer(pointer) {
    var $pointer_target = $(pointer.target),
      $pointer_widget = $pointer_target.pointer('widget');
    if (!$pointer_widget.is(':visible')) {
      return;
    }
    $pointer_widget.fadeOut(300);
  }

  /**
   * If more pointers on the page, loads the next.
   *
   * @since 1.0.0
   *
   * @param e
   */
  function load_next(e) {
    if (pointers.length <= 1) {
      return;
    }
    current_pointer++;
    if (typeof pointers[current_pointer] !== 'undefined') {
      unload_pointer(pointers[current_pointer - 1]);
      load_pointer(pointers[current_pointer]);
      e.preventDefault();
    }
  }

  /**
   * If more pointers on the page, loads the next.
   *
   * @since 1.0.0
   *
   * @param e
   */
  function load_previous(e) {
    if (pointers.length <= 1) {
      return;
    }
    current_pointer--;
    if (typeof pointers[current_pointer] !== 'undefined') {
      unload_pointer(pointers[current_pointer + 1]);
      load_pointer(pointers[current_pointer]);
      e.preventDefault();
    }
  }
  $(init_quickstart);
})(jQuery, LD_GB_Admin.l10n);

/***/ }),

/***/ "./src/assets/js/admin/repeater.js":
/*!*****************************************!*\
  !*** ./src/assets/js/admin/repeater.js ***!
  \*****************************************/
/***/ (() => {

/* eslint-disable -- TODO: fix linting issues */

/**
 * Sets up any jQuery Repeaters.
 *
 * @since 1.0.0
 *
 * @package
 * @subpackage LearnDash_Gradebook/assets/src/js/admin
 */

var LD_GB_jQuery_Repeaters;
(function ($, api) {
  api = {
    /**
     * All elements in use by this API.
     *
     * @since 1.0.0
     */
    $elements: {},
    /**
     * Initializes the API.
     *
     * @since 1.0.0
     * @access private
     */
    init: function init() {
      api.get_elements();

      // Close out if no repeaters are present on this page
      if (!api.$elements.repeaters.length) {
        return;
      }
      api.setup_repeaters_data();
      api.init_repeaters();

      // Prevent anchor links from sending to top of page
      $(document).on('click', 'a[data-repeater-delete], a[data-repeater-create]', function (e) {
        e.preventDefault();
      });
    },
    /**
     * Gets all elements in use by this API.
     *
     * @since 1.0.0
     * @access private
     */
    get_elements: function get_elements() {
      api.$elements.repeaters = $('[data-ld-gb-repeater]');
    },
    /**
     * Adds any repeater init data to LD repeaters.
     *
     * @since 1.0.0
     * @access private
     */
    setup_repeaters_data: function setup_repeaters_data() {},
    /**
     * Initializes all repeaters on the page.
     *
     * @since 1.0.0
     */
    init_repeaters: function init_repeaters() {
      api.$elements.repeaters.each(api.init_repeater);
    },
    /**
     * Initializes a repeater.
     *
     * @since 1.0.0
     */
    init_repeater: function init_repeater() {
      var isFirstItemIndelible = $(this).attr('data-first-item-undeletable') ? true : false,
        initEmpty = $(this).attr('data-init-empty') ? true : false,
        $repeater = $(this),
        defaultValues = $(this).attr('data-default-values') || {};
      $(this).repeater({
        hide: function hide(deleteElement) {
          if ($(this).hasClass('locked')) {
            return;
          }
          if (!confirm(LD_GB_Admin.l10n.repeater_confirm_delete)) {
            return;
          }
          $repeater.trigger('repeater-remove', [$(this)]);
          deleteElement();
          $repeater.trigger('repeater-removed');
        },
        show: function show() {
          $repeater.trigger('repeater-add', [$(this)]);
          $(this).show();
          $repeater.trigger('repeater-added', [$(this)]);
        },
        isFirstItemUndeletable: isFirstItemIndelible,
        defaultValues: defaultValues,
        initEmpty: initEmpty
      });
      $(this).find('[data-repeater-item-dummy]').remove();
      $(this).trigger('repeater-init');
    }
  };
  $(api.init);
})(jQuery, LD_GB_jQuery_Repeaters);

/***/ }),

/***/ "./src/assets/js/admin/settings-page.js":
/*!**********************************************!*\
  !*** ./src/assets/js/admin/settings-page.js ***!
  \**********************************************/
/***/ (() => {

/* eslint-disable -- TODO: fix linting issues */

/**
 * Roles Settings page functionality.
 *
 * @param      $
 * @since 1.4.0
 *
 * @package
 * @subpackage LearnDash_Gradebook/assets/src/js/admin
 */

(function ($) {
  var $viewGradebookRoles, $editGradebookRoles;
  function init_settings_page() {
    $viewGradebookRoles = $('input[name="ld_gb_gradebook_roles[]"]'), $editGradebookRoles = $('input[name="ld_gb_edit_gradebook_roles[]"]');
    if (!$viewGradebookRoles.length || !$editGradebookRoles.length) return;
    $editGradebookRoles.on('change', toggleViewGradebookRoles);
    $viewGradebookRoles.on('change', toggleEditGradebookRoles);
  }

  /**
   * When a Edit Gradebook Checkbox is checked, ensure that the View Gradebook Checkbox for that Role also is checked
   *
   * @param {Object} event jQuery Event Object
   *
   * @since		1.4.0
   * @return 	void
   */
  function toggleViewGradebookRoles(event) {
    var value = $(this).val(),
      viewGradebookRoles = getCheckboxesValues($viewGradebookRoles);
    if (!$(this).prop('checked')) return;

    // If not found already checked within the View Gradebook Roles, check it
    if (viewGradebookRoles.indexOf(value) === -1) {
      $viewGradebookRoles.filter(function (index) {
        return $(this).val() == value;
      }).attr('checked', true).trigger('change');
    }
  }

  /**
   * When a View Gradebook Checkbox is unchecked, ensure that the Edit Gradebook Checkbox for that Role also is unchecked
   *
   * @param {Object} event jQuery Event Object
   *
   * @since		1.4.0
   * @return 	void
   */
  function toggleEditGradebookRoles(event) {
    var value = $(this).val(),
      editGradebookRoles = getCheckboxesValues($editGradebookRoles);
    if ($(this).prop('checked')) return;

    // If found within the checked Edit Gradebook Roles, uncheck it
    if (editGradebookRoles.indexOf(value) > -1) {
      $editGradebookRoles.filter(function (index) {
        return $(this).val() == value;
      }).attr('checked', false).trigger('change');
    }
  }

  /**
   * Quickly grabs all the checked values for a group of Checkboxes.
   *
   * @param {Array} $checkboxes Array of jQuery DOM Objects
   *
   * @since		1.4.0
   * @return 	{Array} Array of Values
   */
  function getCheckboxesValues($checkboxes) {
    var values = [];
    $checkboxes.each(function (index, checkbox) {
      if ($(checkbox).prop('checked')) {
        values.push($(checkbox).val());
      }
    });
    return values;
  }
  $(init_settings_page);
})(jQuery);

/***/ }),

/***/ "./src/assets/js/admin/types-page.js":
/*!*******************************************!*\
  !*** ./src/assets/js/admin/types-page.js ***!
  \*******************************************/
/***/ (() => {

/* eslint-disable -- TODO: fix linting issues */

/**
 * Gradebook page admin screen.
 *
 * @param $
 * @param l10n
 * @since 1.0.0
 *
 * @global
 */
(function ($, l10n) {
  'use strict';

  var $form, $tip, $components_edit, tip;
  function init() {
    $components_edit = $('#ld-gb-edit-components');
    if (!$components_edit.length) {
      return;
    }
    $form = $('#ld-gb-edit-components-form');
    $tip = $components_edit.find('.notice.error');
    $components_edit.on('repeater-added', add_component);
    $components_edit.on('repeater-removed', remove_component);
    $components_edit.on('click', '.locked [data-repeater-delete]', show_delete_error);
    $components_edit.on('click', '.notice-dismiss', hide_notice);
    $form.submit(check_weights);
    $form.submit(check_required);
  }
  function add_component(e, $component) {
    $components_edit.removeClass('no-components');
    $component.find('[name$="term_id]"]').val('');
    $component.find('.ld-gb-edit-components-info').html('');
    $component.removeClass('locked');
    $component.find('[name$="[name]"]').focus();
  }
  function remove_component(e) {
    if (!$components_edit.find('[data-repeater-item]').length) {
      $components_edit.addClass('no-components');
    }
  }
  function show_delete_error(e) {
    e.preventDefault();
    show_notice(l10n.cannot_delete_component);
  }
  function dismiss_notice(e) {
    e.preventDefault();
    hide_notice();
  }
  function show_notice(message) {
    window.clearTimeout(tip);
    $tip.find('p').html(message);
    $tip.stop().slideDown(300);
  }
  function hide_notice() {
    $tip.stop().slideUp(300, function () {
      $(this).find('p').html('');
    });
    return false;
  }
  function check_required(e) {
    var $fields = $components_edit.find('[name$="weight]"]:visible,' + '[name$="name]"]:visible');
    if (!$fields.length) {
      hide_notice();
      return;
    }
    $fields.each(function () {
      if (!$(this).val()) {
        e.preventDefault();
        show_notice(l10n.required);
        return false;
      }
    });
  }
  function check_weights(e) {
    var $weights = $components_edit.find('[name*="weight"]:visible'),
      i,
      total = 0,
      $current_weight,
      cancel_submit = false;
    if (!$weights.length) {
      return;
    }
    for (i = 0; i < $weights.length; i++) {
      $current_weight = $($weights.get(i));

      // If not even filled out, bail this check entirely, the required check has it covered
      if (!$current_weight.val()) {
        return;
      }
      total = total + parseInt($current_weight.val());
      if (total > 100) {
        // Notify on all fields weight is too high
        e.preventDefault();
        cancel_submit = l10n.weights_too_high.replace('{percent}', total);
        break;
      } else if (i + 1 === $weights.length && total < 100) {
        // Notify on all fields weight is too low
        e.preventDefault();
        cancel_submit = l10n.weights_too_low.replace('{percent}', total);
        break;
      }
    }
    if (cancel_submit) {
      show_notice(cancel_submit);
    } else {
      hide_notice();
    }
  }
  $(init);
})(jQuery, LD_GB_Admin.l10n);

/***/ }),

/***/ "./src/assets/js/admin/user-grades-page.js":
/*!*************************************************!*\
  !*** ./src/assets/js/admin/user-grades-page.js ***!
  \*************************************************/
/***/ (() => {

/* eslint-disable -- TODO: fix linting issues */

/**
 * Manual score entry.
 *
 * @param $
 * @param l10n
 * @since 1.0.1
 *
 * @global
 */
(function ($, l10n) {
  'use strict';

  var api = {
    $elements: {},
    /**
     * Initializes the api.
     *
     * @since 1.0.1
     */
    init: function init() {
      api.get_elements();
      if (!api.$elements) {
        return;
      }
      api.setup_handlers();
    },
    /**
     * Gets and stores all elements used.
     *
     * @since 1.0.1
     */
    get_elements: function get_elements() {
      api.$elements.gradebook = $('#ld-gb-gradebook');
    },
    /**
     * Binds all event handlers.
     *
     * @since 1.0.1
     */
    setup_handlers: function setup_handlers() {
      api.$elements.gradebook.on('click', '[data-ld-gb-component-edit]', api.edit_component_grade);
      api.$elements.gradebook.on('click', '[data-ld-gb-component-cancel]', api.cancel_component_grade);
      api.$elements.gradebook.on('click', '[data-ld-gb-component-submit]', api.submit_component_grade);
      api.$elements.gradebook.on('click', '[data-ld-gb-component-delete]', api.delete_component_grade);
      api.$elements.gradebook.on('keypress', '.ld-gb-gradebook-component-edit input', api.submit_component_grade);
      api.$elements.gradebook.on('click', '[data-edit-grade]', api.edit_grade);
      api.$elements.gradebook.on('click', '[data-cancel-edit-grade]', api.cancel_edit_grade);
      api.$elements.gradebook.on('click', '[data-submit-edit-grade]', api.submit_edit_grade);
      api.$elements.gradebook.on('click', '[data-remove-manual-grade]', api.remove_manual_grade);
      api.$elements.gradebook.on('click', '[data-add-manual-grade]', api.add_manual_grade);
      api.$elements.gradebook.on('click', '[data-cancel-add-manual-grade]', api.cancel_edit_manual_grade);
      api.$elements.gradebook.on('keypress', '.ld-gb-gradebook-component-grade-editform input,' + '.ld-gb-gradebook-component-grade-editform select,' + '.ld-gb-gradebook-component-grade-editform textarea', api.submit_edit_grade);
    },
    /**
     * Opens the edit component grade form.
     *
     * @since 1.1.0
     *
     * @param e
     */
    edit_component_grade: function edit_component_grade(e) {
      e.preventDefault();
      var $container = $(this).closest('.ld-gb-gradebook-component-overall-grade'),
        grade = $container.attr('data-ld-gb-component-grade');
      $container.find('input[type="text"][name="ld-gb-component-override"]').val(grade);
      $container.addClass('editing');
    },
    /**
     * Cancels editing a component grade form.
     *
     * @since 1.1.0
     *
     * @param e
     */
    cancel_component_grade: function cancel_component_grade(e) {
      e.preventDefault();
      api.close_edit_component_grade($(this).closest('.ld-gb-gradebook-component-overall-grade'));
    },
    /**
     * Deletes a component grade.
     *
     * @since 1.1.0
     *
     * @param e
     */
    delete_component_grade: function delete_component_grade(e) {
      e.preventDefault();
      var $container = $(this).closest('.ld-gb-gradebook-component-overall-grade'),
        $component = $container.closest('.ld-gb-gradebook-component'),
        user_id = $container.attr('data-user-id'),
        component_id = $container.attr('data-component-id');
      $container.find('.ld-gb-gradebook-component-edit').append('<div class="ld-gb-gradebook-component-overall-grade-cover"><span class="spinner"></span></div>');
      $container.find('input, button, textarea, select').prop('disabled', true);
      $.post(ajaxurl, {
        action: 'ld_gb_edit_component_grade',
        data: {
          action: 'delete',
          user_id: user_id,
          component_id: component_id,
          gradebook: LD_GB_Admin.gradebook
        }
      }, function (response) {
        if (typeof response.success === 'undefined') {
          return;
        }
        if (response.success) {
          // Update grade data
          $container.attr('data-ld-gb-component-grade', response.data.component_grade.grade);

          // Update grades' display
          api.change_user_grade(response.data.user_grade);
          api.change_component_grade($component, response.data.component_grade);
          $container.removeClass('overridden');
          $container.find('[data-ld-gb-component-edit]').html(l10n.component_grade_override);
          $component.find('.ld-gb-gradebook-component-overridden-notice').hide();
        }
        api.close_edit_component_grade($container);
      });
    },
    /**
     * Submits the component grade.
     *
     * @since 1.1.0
     *
     * @param e
     */
    submit_component_grade: function submit_component_grade(e) {
      if (typeof e.which !== 'undefined' && e.which != 13 && e.which != 1) {
        return;
      }
      e.preventDefault();
      var $container = $(this).closest('.ld-gb-gradebook-component-overall-grade'),
        $component = $container.closest('.ld-gb-gradebook-component'),
        user_id = $container.attr('data-user-id'),
        component_id = $container.attr('data-component-id'),
        new_grade = $container.find('input[type="text"][name="ld-gb-component-override"]').val();
      $container.find('.ld-gb-gradebook-component-edit').append('<div class="ld-gb-gradebook-component-overall-grade-cover"><span class="spinner"></span></div>');
      $container.find('input, button, textarea, select').prop('disabled', true);
      $.post(ajaxurl, {
        action: 'ld_gb_edit_component_grade',
        data: {
          action: 'save',
          new_grade: new_grade,
          user_id: user_id,
          component_id: component_id,
          gradebook: LD_GB_Admin.gradebook
        }
      }, function (response) {
        if (typeof response.success === 'undefined') {
          return;
        }
        if (response.success) {
          // Update grade data
          $container.attr('data-ld-gb-component-grade', response.data.component_grade.grade);

          // Update grades' display
          api.change_user_grade(response.data.user_grade);
          api.change_component_grade($component, response.data.component_grade);
          $container.addClass('overridden');
          $container.find('[data-ld-gb-component-edit]').html(l10n.component_grade_modify);
          $component.find('.ld-gb-gradebook-component-overridden-notice').show();
        }
        api.close_edit_component_grade($container);
      });
    },
    /**
     * Closes the edit window for editing a component.
     *
     * @since 1.1.0
     *
     * @param $container
     */
    close_edit_component_grade: function close_edit_component_grade($container) {
      $container.find('.ld-gb-gradebook-component-overall-grade-cover').remove();
      $container.find('input, button, textarea, select').prop('disabled', false);
      $container.removeClass('editing');
    },
    /**
     * Edits a manual grade entry.
     *
     * @since 1.0.1
     *
     * @param e Event
     */
    edit_grade: function edit_grade(e) {
      e.preventDefault();
      var $grade = $(this).closest('tr'),
        $edit = $grade.next('tr.ld-gb-gradebook-component-grade-editform');
      if (!$grade.length || !$edit.length) {
        return;
      }
      $grade.addClass('editing');
      $edit.addClass('editing');
    },
    /**
     * Cancels editing a manual grade.
     *
     * @since 1.0.1
     *
     * @param e Event
     */
    cancel_edit_grade: function cancel_edit_grade(e) {
      e.preventDefault();
      var $component = $(this).closest('.ld-gb-gradebook-component'),
        $edit = $(this).closest('tr'),
        $grade = $edit.prev('tr.ld-gb-gradebook-component-grade-display'),
        $add_grade = $component.find('[data-add-manual-grade]');
      if ($grade.attr('data-template') == 'undefined') {
        return;
      }
      if (!$grade.length || !$edit.length) {
        return;
      }
      $grade.removeClass('editing');
      $edit.removeClass('editing');
      $add_grade.show();
    },
    /**
     * Submits the grade edit.
     *
     * @param e
     * @since 1.0.1
     */
    submit_edit_grade: function submit_edit_grade(e) {
      if (typeof e.which !== 'undefined' && e.which != 13 && e.which != 1) {
        return;
      }
      e.preventDefault();
      var $component = $(this).closest('.ld-gb-gradebook-component'),
        $edit = $(this).closest('tr.ld-gb-gradebook-component-grade-editform'),
        $grade = $edit.prev('tr.ld-gb-gradebook-component-grade-display'),
        $inputs = $edit.find('input, select, textarea'),
        $message = $edit.find('.ld-gb-gradebook-component-grade-message'),
        $no_grades = $component.find('[data-no-grades]'),
        type,
        data = {};
      $inputs.each(function () {
        data[$(this).attr('name').replace('grade-', '')] = $(this).val();
      });
      if (typeof data.type === 'undefined') {
        return;
      }
      type = data.type;
      if (type == 'manual') {
        if (typeof data.name === 'undefined' || typeof data.score === 'undefined' || !data.score) {
          api.show_message($message, l10n.manual_grade_add_error, 'error');
          return;
        }
        api.hide_message($message, 'error');
      }
      $.post(ajaxurl, {
        action: 'ld_gb_edit_grade',
        data: data
      }, function (response) {
        if (response == '0') {
          api.show_message($message, l10n.could_not_edit_grade, 'error');
          return;
        }
        if (!response.success) {
          api.show_message($message, response.data.error, 'error');
        } else {
          api.show_message($message, response.data.success, 'success');
          if (type == 'manual') {
            // Update grade content
            $grade.find('.ld-gb-gradebook-component-grade-name-content').html(response.data.name);
            $grade.find('.ld-gb-gradebook-component-grade-timestamp').html(response.data.completed_display);

            // Update input fields
            $edit.find('[name="grade-previous_name"]').val(response.data.name);
            $edit.find('[name="grade-score"]').val(response.data.score);

            // If added, update buttons
            if ($edit.find('[name="grade-new"]').val('1')) {
              $edit.find('[name="grade-new"]').val('0');
              $edit.find('[data-submit-edit-grade]').html(l10n.change);
              $edit.find('[data-cancel-add-manual-grade]').removeAttr('data-cancel-add-manual-grade').attr('data-cancel-edit-grade', '1').html(l10n.close);
            }
          }

          // Update the score field
          if (typeof response.data.score_display !== 'undefined') {
            $grade.find('.ld-gb-gradebook-component-grade-score-content').html(response.data.score_display);
          }

          // Update grades
          if (typeof response.data.user_grade !== 'undefined') {
            api.change_user_grade(response.data.user_grade);
          }
          if (typeof response.data.component_grade !== 'undefined') {
            api.change_component_grade($component, response.data.component_grade);
          }

          // Possibly hide/show the "No Grades Yet"
          if ($component.find('tbody tr:not([data-template])').length) {
            $no_grades.hide();
          } else {
            $no_grades.show();
          }
        }
      });
    },
    /**
     * Adds a manual grade.
     *
     * @since 1.0.1
     *
     * @param e
     */
    add_manual_grade: function add_manual_grade(e) {
      e.preventDefault();
      var $component = $(this).closest('.ld-gb-gradebook-component'),
        $grade_template = $component.find('tr.ld-gb-gradebook-component-grade-display[data-template]'),
        $edit_template = $component.find('tr.ld-gb-gradebook-component-grade-editform[data-template]'),
        $grade = $grade_template.clone(),
        $edit = $edit_template.clone();

      // Prepare template
      $edit.find(':input:not([type="hidden"])').each(function (index, element) {
        var defaultVal = $(element).data('default');
        if (typeof defaultVal === 'undefined') {
          defaultVal = '';
        }
        $(element).val(defaultVal);
      });
      $edit.insertBefore($grade_template).addClass('editing').removeAttr('data-template');
      $grade.insertBefore($edit).addClass('editing').removeAttr('data-template');
      $(this).hide();
    },
    /**
     * Cancels editing a manual grade.
     *
     * @since 1.0.1
     *
     * @param e
     */
    cancel_edit_manual_grade: function cancel_edit_manual_grade(e) {
      e.preventDefault();
      var $component = $(this).closest('.ld-gb-gradebook-component'),
        $edit = $(this).closest('tr'),
        $grade = $edit.prev('tr.ld-gb-gradebook-component-grade-display'),
        $add_grade = $component.find('[data-add-manual-grade]');
      if (!$grade.length || !$edit.length) {
        return;
      }
      $grade.remove();
      $edit.remove();
      $add_grade.show();
    },
    /**
     * Removes a manual grade.
     *
     * @since 1.0.1
     *
     * @param e
     */
    remove_manual_grade: function remove_manual_grade(e) {
      e.preventDefault();
      if (!confirm(l10n.manual_grade_confirm_delete)) {
        return;
      }
      var $component = $(this).closest('.ld-gb-gradebook-component'),
        $grade = $(this).closest('tr.ld-gb-gradebook-component-grade-display'),
        $edit = $grade.next('tr.ld-gb-gradebook-component-grade-editform'),
        $inputs = $edit.find('input, select, textarea'),
        $no_grades = $component.find('[data-no-grades]'),
        data = {};
      $inputs.each(function () {
        data[$(this).attr('name').replace('grade-', '')] = $(this).val();
      });
      data["delete"] = '1';
      $.post(ajaxurl, {
        action: 'ld_gb_edit_grade',
        data: data
      }, function (response) {
        if (response == '0') {
          return;
        }
        if (!response.success) {} else {
          $grade.addClass('deleted').fadeOut(500, function () {
            $grade.remove();
            $edit.remove();

            // Possibly hide/show the "No Grades Yet"
            if ($component.find('tbody tr:not([data-template]):not([data-no-grades])').length) {
              $no_grades.hide();
            } else {
              $no_grades.show();
            }
          });

          // Update grades
          if (typeof response.data.user_grade !== 'undefined') {
            api.change_user_grade(response.data.user_grade);
          }
          if (typeof response.data.component_grade !== 'undefined') {
            api.change_component_grade($component, response.data.component_grade);
          }
        }
      });
    },
    /**
     * Gets all elements pertaining to the manual grade editing process.
     *
     * @since 1.0.1
     * @param  $row
     *
     * @param  $e
     * @return {{}}
     */
    get_manual_edit_elements: function get_manual_edit_elements($row) {
      var $elements = {};
      $elements.name = $row.find('.ld-gb-gradebook-component-grade-name');
      $elements.name_content = $elements.name.find('.ld-gb-gradebook-component-grade-name-content');
      $elements.score = $row.find('.ld-gb-gradebook-component-grade-score');
      $elements.score_content = $elements.score.find('.ld-gb-gradebook-component-grade-score-content');
      $elements.input_name = $elements.name.find('input[name="ld_gb_gradebook_component_grade_name"]');
      $elements.input_score = $elements.score.find('input[name="ld_gb_gradebook_component_grade_score"]');
      return $elements;
    },
    /**
     * Changes the course display grade.
     *
     * @since 1.0.1
     *
     * @param $course
     * @param grade
     */
    change_user_grade: function change_user_grade(grade) {
      api.$elements.gradebook.find('span.user-grade').html(grade.score).css('background', grade.color);
    },
    /**
     * Changes the component display grade.
     *
     * @since 1.0.1
     *
     * @param $component
     * @param grade
     */
    change_component_grade: function change_component_grade($component, grade) {
      $component.find('.ld-gb-gradebook-component-overall-grade .ld-gb-grade').html(grade.score).css('background', grade.color);
    },
    /**
     * Shows the component message.
     *
     * @since 1.0.1
     *
     * @param $message
     * @param message
     * @param type
     */
    show_message: function show_message($message, message, type) {
      $message.find('.notice-' + type).stop().slideDown(150).find('p').html(message);
      $message.find('.notice-' + type).delay(5000).slideUp(150);
    },
    /**
     * Hides the component message.
     *
     * @since 1.0.1
     *
     * @param $message
     * @param type
     */
    hide_message: function hide_message($message, type) {
      $message.find('.notice-' + type).stop().slideUp(150);
    },
    /**
     * Sets a manual entry row to processing.
     *
     * @since 1.0.1
     *
     * @param $row
     */
    row_set_processing: function row_set_processing($row) {
      $row.addClass('processing');
      $row.find('.ld-gb-gradebook-component-grade-name').append('<span class="spinner"></span>');
    },
    /**
     * Removes a manual entry row to processing.
     *
     * @since 1.0.1
     *
     * @param $row
     */
    row_remove_processing: function row_remove_processing($row) {
      $row.removeClass('processing');
      $row.find('.spinner').remove();
    },
    /**
     * Shortcut for stopping propagation.
     *
     * @since 1.1.0
     *
     * @param e
     */
    stop_propagation: function stop_propagation(e) {
      e.stopPropagation();
    }
  };
  $(api.init);
})(jQuery, LD_GB_Admin.l10n);

/***/ }),

/***/ "./src/assets/js/lib/update-url.js":
/*!*****************************************!*\
  !*** ./src/assets/js/lib/update-url.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getQueryString: () => (/* binding */ getQueryString),
/* harmony export */   getURLParam: () => (/* binding */ getURLParam),
/* harmony export */   updateURLParam: () => (/* binding */ updateURLParam)
/* harmony export */ });
/* eslint-disable -- TODO: fix linting issues */

/**
 * Gets a Value from a Query String by Key
 *
 * @param string      queryString  Query String
 * @param string      key
 *
 * @param queryString
 * @param key
 * @since   2.0.0
 * @return  string               Query String
 */
function getURLParam(queryString, key) {
  var vars = queryString.replace(/^\?/, '').split('&');
  for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split('=');
    if (pair[0] == key) {
      return pair[1];
    }
  }
  return false;
}

/**
 * Update a URL Query String Param by Key, preserving any Hash
 *
 * @param string      queryString    URL
 * @param string      key            Query String Param Key
 * @param string      value          Query String Param Value
 *
 * @param queryString
 * @param key
 * @param value
 * @since   2.0.0
 * @return  string                 URL
 */
function updateURLParam(queryString, key, value) {
  // remove the hash part before operating on the url
  var hashIndex = queryString.indexOf('#');
  var hash = hashIndex === -1 ? '' : queryString.substr(hashIndex);
  queryString = hashIndex === -1 ? queryString : queryString.substr(0, hashIndex);
  var re = new RegExp('([?&])' + key + '=.*?(&|$)', 'i');
  var separator = queryString.indexOf('?') !== -1 ? '&' : '?';
  if (queryString.match(re) && value !== '') {
    queryString = queryString.replace(re, '$1' + key + '=' + value + '$2');
  } else if (value.length == 0) {
    var temp = queryString.replace(re, '');
    if (temp.length == 0) {
      queryString = temp;
    } else {
      queryString = '?' + temp;
    }
  } else {
    queryString = queryString + separator + key + '=' + value;
  }
  return queryString + hash;
}

/**
 * Extracts a Query String from a URL
 *
 * @param string url  Full URL
 *
 * @param url
 * @since   2.0.0
 * @return  string       Query String
 */
function getQueryString(url) {
  var matches = url.match(/(\?.*)$/);
  if (matches == null) return '';
  return matches[1];
}


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!**************************************!*\
  !*** ./src/assets/js/admin/admin.js ***!
  \**************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _dashboard_widget__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./dashboard-widget */ "./src/assets/js/admin/dashboard-widget.js");
/* harmony import */ var _dashboard_widget__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_dashboard_widget__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gradebook_page__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./gradebook-page */ "./src/assets/js/admin/gradebook-page.js");
/* harmony import */ var _gradebook_post_page__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./gradebook-post-page */ "./src/assets/js/admin/gradebook-post-page.js");
/* harmony import */ var _gradebook_post_page__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_gradebook_post_page__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _main__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./main */ "./src/assets/js/admin/main.js");
/* harmony import */ var _main__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_main__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _quickstart__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./quickstart */ "./src/assets/js/admin/quickstart.js");
/* harmony import */ var _quickstart__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_quickstart__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _quickstart_mocks__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./quickstart-mocks */ "./src/assets/js/admin/quickstart-mocks.js");
/* harmony import */ var _quickstart_mocks__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_quickstart_mocks__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _repeater__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./repeater */ "./src/assets/js/admin/repeater.js");
/* harmony import */ var _repeater__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_repeater__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _settings_page__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./settings-page */ "./src/assets/js/admin/settings-page.js");
/* harmony import */ var _settings_page__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_settings_page__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _types_page__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./types-page */ "./src/assets/js/admin/types-page.js");
/* harmony import */ var _types_page__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_types_page__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _user_grades_page__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./user-grades-page */ "./src/assets/js/admin/user-grades-page.js");
/* harmony import */ var _user_grades_page__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_user_grades_page__WEBPACK_IMPORTED_MODULE_9__);










})();

/******/ })()
;