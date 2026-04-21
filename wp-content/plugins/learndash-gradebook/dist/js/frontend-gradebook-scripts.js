/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/assets/js/frontend-gradebook/ancient-browser-support.js":
/*!*********************************************************************!*\
  !*** ./src/assets/js/frontend-gradebook/ancient-browser-support.js ***!
  \*********************************************************************/
/***/ (() => {

/* eslint-disable -- TODO: fix linting issues */

if (!HTMLFormElement.prototype.reportValidity) {
  // Quick and dirty global to know what I'm working with
  window.LD_GB_FrontendGradebookAncientBrowser = true;

  /**
   * Wait, people use IE and Safari outside of downloading Chrome?
   *
   * @since	  2.0.0
   * @return	  void
   */
  HTMLFormElement.prototype.reportValidity = function () {
    var requiredError = LD_GB_FrontendGradebook.i18n.validationError,
      valid = true;

    // Remove all old Validation Errors
    jQuery(this).find('.validation-error').remove();
    jQuery(this).find('[required]').each(function (index, element) {
      // Reset Custom Validity Message
      element.setCustomValidity('');
      if (jQuery(element).val() === null || jQuery(element).val() == '') {
        element.setCustomValidity(requiredError);
        jQuery(element).before('<span class="validation-error">' + requiredError + '</span>');
        valid = false;
      }
    });
    if (!valid) {
      jQuery(this).scrollTop(jQuery(this).find('.validation-error:first-of-type'));
      return valid;
    }
    return valid;
  };
}

/***/ }),

/***/ "./src/assets/js/frontend-gradebook/minimal-foundation.js":
/*!****************************************************************!*\
  !*** ./src/assets/js/frontend-gradebook/minimal-foundation.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Foundation: () => (/* reexport safe */ foundation_sites_js_foundation_core__WEBPACK_IMPORTED_MODULE_1__.Foundation)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var foundation_sites_js_foundation_core__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! foundation-sites/js/foundation.core */ "./node_modules/foundation-sites/js/foundation.core.js");
/* harmony import */ var foundation_sites_js_foundation_util_keyboard__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! foundation-sites/js/foundation.util.keyboard */ "./node_modules/foundation-sites/js/foundation.util.keyboard.js");
/* harmony import */ var foundation_sites_js_foundation_util_mediaQuery__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! foundation-sites/js/foundation.util.mediaQuery */ "./node_modules/foundation-sites/js/foundation.util.mediaQuery.js");
/* harmony import */ var foundation_sites_js_foundation_util_motion__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! foundation-sites/js/foundation.util.motion */ "./node_modules/foundation-sites/js/foundation.util.motion.js");
/* harmony import */ var foundation_sites_js_foundation_util_touch__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! foundation-sites/js/foundation.util.touch */ "./node_modules/foundation-sites/js/foundation.util.touch.js");
/* harmony import */ var foundation_sites_js_foundation_util_triggers__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! foundation-sites/js/foundation.util.triggers */ "./node_modules/foundation-sites/js/foundation.util.triggers.js");
/* harmony import */ var foundation_sites_js_foundation_reveal__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! foundation-sites/js/foundation.reveal */ "./node_modules/foundation-sites/js/foundation.reveal.js");
// cspell:ignore drilldown .
/* eslint-disable -- TODO: fix linting issues */



//import { rtl, GetYoDigits, transitionend } from 'foundation-sites/js/foundation.core.utils';
//import { Box } from 'foundation-sites/js/foundation.util.box'
//import { onImagesLoaded } from 'foundation-sites/js/foundation.util.imageLoader';



//import { Nest } from 'foundation-sites/js/foundation.util.nest';
//import { Timer } from 'foundation-sites/js/foundation.util.timer';


//import { Abide } from 'foundation-sites/js/foundation.abide';
//import { Accordion } from 'foundation-sites/js/foundation.accordion';
//import { AccordionMenu } from 'foundation-sites/js/foundation.accordionMenu';
//import { Drilldown } from 'foundation-sites/js/foundation.drilldown';
//import { Dropdown } from 'foundation-sites/js/foundation.dropdown';
//import { DropdownMenu } from 'foundation-sites/js/foundation.dropdownMenu';
//import { Equalizer } from 'foundation-sites/js/foundation.equalizer';
//import { Interchange } from 'foundation-sites/js/foundation.interchange';
//import { Magellan } from 'foundation-sites/js/foundation.magellan';
//import { OffCanvas } from 'foundation-sites/js/foundation.offcanvas';
//import { Orbit } from 'foundation-sites/js/foundation.orbit';
//import { ResponsiveMenu } from 'foundation-sites/js/foundation.responsiveMenu';
//import { ResponsiveToggle } from 'foundation-sites/js/foundation.responsiveToggle';

//import { Slider } from 'foundation-sites/js/foundation.slider';
//import { SmoothScroll } from 'foundation-sites/js/foundation.smoothScroll';
//import { Sticky } from 'foundation-sites/js/foundation.sticky';
//import { Tabs } from 'foundation-sites/js/foundation.tabs';
//import { Toggler } from 'foundation-sites/js/foundation.toggler';
//import { Tooltip } from 'foundation-sites/js/foundation.tooltip';
//import { ResponsiveAccordionTabs } from 'foundation-sites/js/foundation.responsiveAccordionTabs';

foundation_sites_js_foundation_core__WEBPACK_IMPORTED_MODULE_1__.Foundation.addToJquery((jquery__WEBPACK_IMPORTED_MODULE_0___default()));

// Add Foundation Utils to Foundation global namespace for backwards
// compatibility.

//Foundation.rtl = rtl;
//Foundation.GetYoDigits = GetYoDigits;
//Foundation.transitionend = transitionend;

//Foundation.Box = Box;
//Foundation.onImagesLoaded = onImagesLoaded;
foundation_sites_js_foundation_core__WEBPACK_IMPORTED_MODULE_1__.Foundation.Keyboard = foundation_sites_js_foundation_util_keyboard__WEBPACK_IMPORTED_MODULE_2__.Keyboard;
foundation_sites_js_foundation_core__WEBPACK_IMPORTED_MODULE_1__.Foundation.MediaQuery = foundation_sites_js_foundation_util_mediaQuery__WEBPACK_IMPORTED_MODULE_3__.MediaQuery;
foundation_sites_js_foundation_core__WEBPACK_IMPORTED_MODULE_1__.Foundation.Motion = foundation_sites_js_foundation_util_motion__WEBPACK_IMPORTED_MODULE_4__.Motion;
foundation_sites_js_foundation_core__WEBPACK_IMPORTED_MODULE_1__.Foundation.Move = foundation_sites_js_foundation_util_motion__WEBPACK_IMPORTED_MODULE_4__.Move;
//Foundation.Nest = Nest;
//Foundation.Timer = Timer;

// Touch and Triggers previously were almost purely side effect driven,
// so no // need to add it to Foundation, just init them.

foundation_sites_js_foundation_util_touch__WEBPACK_IMPORTED_MODULE_5__.Touch.init((jquery__WEBPACK_IMPORTED_MODULE_0___default()));
foundation_sites_js_foundation_util_triggers__WEBPACK_IMPORTED_MODULE_6__.Triggers.init((jquery__WEBPACK_IMPORTED_MODULE_0___default()), foundation_sites_js_foundation_core__WEBPACK_IMPORTED_MODULE_1__.Foundation);

//Foundation.plugin(Abide, 'Abide');

//Foundation.plugin(Accordion, 'Accordion');

//Foundation.plugin(AccordionMenu, 'AccordionMenu');

//Foundation.plugin(Drilldown, 'Drilldown');

//Foundation.plugin(Dropdown, 'Dropdown');

//Foundation.plugin(DropdownMenu, 'DropdownMenu');

//Foundation.plugin(Equalizer, 'Equalizer');

//Foundation.plugin(Interchange, 'Interchange');

//Foundation.plugin(Magellan, 'Magellan');

//Foundation.plugin(OffCanvas, 'OffCanvas');

//Foundation.plugin(Orbit, 'Orbit');

//Foundation.plugin(ResponsiveMenu, 'ResponsiveMenu');

//Foundation.plugin(ResponsiveToggle, 'ResponsiveToggle');

foundation_sites_js_foundation_core__WEBPACK_IMPORTED_MODULE_1__.Foundation.plugin(foundation_sites_js_foundation_reveal__WEBPACK_IMPORTED_MODULE_7__.Reveal, 'Reveal');

//Foundation.plugin(Slider, 'Slider');

//Foundation.plugin(SmoothScroll, 'SmoothScroll');

//Foundation.plugin(Sticky, 'Sticky');

//Foundation.plugin(Tabs, 'Tabs');

//Foundation.plugin(Toggler, 'Toggler');

//Foundation.plugin(Tooltip, 'Tooltip');

//Foundation.plugin(ResponsiveAccordionTabs, 'ResponsiveAccordionTabs');



/***/ }),

/***/ "./src/assets/js/lib/conditionally-load-select2.js":
/*!*********************************************************!*\
  !*** ./src/assets/js/lib/conditionally-load-select2.js ***!
  \*********************************************************/
/***/ (() => {

/* eslint-disable -- TODO: fix linting issues */

/**
 * Conditionally load local copy of Select2
 * This helps silently prevent any conflicts with other plugins loading Select2 globally
 * Based on https://gist.github.com/gists/902090/
 *
 * @since		3.0.2
 */
var ldGBMaybeLoadSelect2 = function ldGBMaybeLoadSelect2() {
  var select2Script;
  if (!(typeof jQuery.fn.select2 !== 'undefined' && jQuery.fn.select2 !== null)) {
    select2Script = document.createElement('script');
    select2Script.type = 'text/javascript';
    select2Script.id = 'ld-slack-select2-js';
    select2Script.src = LD_GB_FrontendGradebook.l10n.uri + 'dist/js/select2.js';

    // This forces the trigger to run only after the JS finishes
    jQuery.getScript(select2Script.src).done(function (scriptContents, textStatus) {
      document.body.appendChild(select2Script);
      jQuery(window).trigger('select2-loaded');
    });
    return false;
  }
  console.warn(LD_GB_FrontendGradebook.i18n.select2Warning);
  jQuery(window).trigger('select2-loaded');
};
if (window.addEventListener) {
  window.addEventListener('load', ldGBMaybeLoadSelect2, false);
} else if (window.attachEvent) {
  window.attachEvent('onload', ldGBMaybeLoadSelect2);
}

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


/***/ }),

/***/ "./node_modules/foundation-sites/js/foundation.core.js":
/*!*************************************************************!*\
  !*** ./node_modules/foundation-sites/js/foundation.core.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Foundation: () => (/* binding */ Foundation)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _foundation_core_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./foundation.core.utils */ "./node_modules/foundation-sites/js/foundation.core.utils.js");
/* harmony import */ var _foundation_util_mediaQuery__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./foundation.util.mediaQuery */ "./node_modules/foundation-sites/js/foundation.util.mediaQuery.js");




var FOUNDATION_VERSION = '6.7.5';

// Global Foundation object
// This is attached to the window, or used as a module for AMD/Browserify
var Foundation = {
  version: FOUNDATION_VERSION,

  /**
   * Stores initialized plugins.
   */
  _plugins: {},

  /**
   * Stores generated unique ids for plugin instances
   */
  _uuids: [],

  /**
   * Defines a Foundation plugin, adding it to the `Foundation` namespace and the list of plugins to initialize when reflowing.
   * @param {Object} plugin - The constructor of the plugin.
   */
  plugin: function(plugin, name) {
    // Object key to use when adding to global Foundation object
    // Examples: Foundation.Reveal, Foundation.OffCanvas
    var className = (name || functionName(plugin));
    // Object key to use when storing the plugin, also used to create the identifying data attribute for the plugin
    // Examples: data-reveal, data-off-canvas
    var attrName  = hyphenate(className);

    // Add to the Foundation object and the plugins list (for reflowing)
    this._plugins[attrName] = this[className] = plugin;
  },
  /**
   * @function
   * Populates the _uuids array with pointers to each individual plugin instance.
   * Adds the `zfPlugin` data-attribute to programmatically created plugins to allow use of $(selector).foundation(method) calls.
   * Also fires the initialization event for each plugin, consolidating repetitive code.
   * @param {Object} plugin - an instance of a plugin, usually `this` in context.
   * @param {String} name - the name of the plugin, passed as a camelCased string.
   * @fires Plugin#init
   */
  registerPlugin: function(plugin, name){
    var pluginName = name ? hyphenate(name) : functionName(plugin.constructor).toLowerCase();
    plugin.uuid = (0,_foundation_core_utils__WEBPACK_IMPORTED_MODULE_1__.GetYoDigits)(6, pluginName);

    if(!plugin.$element.attr(`data-${pluginName}`)){ plugin.$element.attr(`data-${pluginName}`, plugin.uuid); }
    if(!plugin.$element.data('zfPlugin')){ plugin.$element.data('zfPlugin', plugin); }
          /**
           * Fires when the plugin has initialized.
           * @event Plugin#init
           */
    plugin.$element.trigger(`init.zf.${pluginName}`);

    this._uuids.push(plugin.uuid);

    return;
  },
  /**
   * @function
   * Removes the plugins uuid from the _uuids array.
   * Removes the zfPlugin data attribute, as well as the data-plugin-name attribute.
   * Also fires the destroyed event for the plugin, consolidating repetitive code.
   * @param {Object} plugin - an instance of a plugin, usually `this` in context.
   * @fires Plugin#destroyed
   */
  unregisterPlugin: function(plugin){
    var pluginName = hyphenate(functionName(plugin.$element.data('zfPlugin').constructor));

    this._uuids.splice(this._uuids.indexOf(plugin.uuid), 1);
    plugin.$element.removeAttr(`data-${pluginName}`).removeData('zfPlugin')
          /**
           * Fires when the plugin has been destroyed.
           * @event Plugin#destroyed
           */
          .trigger(`destroyed.zf.${pluginName}`);
    for(var prop in plugin){
      if(typeof plugin[prop] === 'function'){
        plugin[prop] = null; //clean up script to prep for garbage collection.
      }
    }
    return;
  },

  /**
   * @function
   * Causes one or more active plugins to re-initialize, resetting event listeners, recalculating positions, etc.
   * @param {String} plugins - optional string of an individual plugin key, attained by calling `$(element).data('pluginName')`, or string of a plugin class i.e. `'dropdown'`
   * @default If no argument is passed, reflow all currently active plugins.
   */
   reInit: function(plugins){
     var isJQ = plugins instanceof (jquery__WEBPACK_IMPORTED_MODULE_0___default());
     try{
       if(isJQ){
         plugins.each(function(){
           jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).data('zfPlugin')._init();
         });
       }else{
         var type = typeof plugins,
         _this = this,
         fns = {
           'object': function(plgs){
             plgs.forEach(function(p){
               p = hyphenate(p);
               jquery__WEBPACK_IMPORTED_MODULE_0___default()('[data-'+ p +']').foundation('_init');
             });
           },
           'string': function(){
             plugins = hyphenate(plugins);
             jquery__WEBPACK_IMPORTED_MODULE_0___default()('[data-'+ plugins +']').foundation('_init');
           },
           'undefined': function(){
             this.object(Object.keys(_this._plugins));
           }
         };
         fns[type](plugins);
       }
     }catch(err){
       console.error(err);
     }finally{
       return plugins;
     }
   },

  /**
   * Initialize plugins on any elements within `elem` (and `elem` itself) that aren't already initialized.
   * @param {Object} elem - jQuery object containing the element to check inside. Also checks the element itself, unless it's the `document` object.
   * @param {String|Array} plugins - A list of plugins to initialize. Leave this out to initialize everything.
   */
  reflow: function(elem, plugins) {

    // If plugins is undefined, just grab everything
    if (typeof plugins === 'undefined') {
      plugins = Object.keys(this._plugins);
    }
    // If plugins is a string, convert it to an array with one item
    else if (typeof plugins === 'string') {
      plugins = [plugins];
    }

    var _this = this;

    // Iterate through each plugin
    jquery__WEBPACK_IMPORTED_MODULE_0___default().each(plugins, function(i, name) {
      // Get the current plugin
      var plugin = _this._plugins[name];

      // Localize the search to all elements inside elem, as well as elem itself, unless elem === document
      var $elem = jquery__WEBPACK_IMPORTED_MODULE_0___default()(elem).find('[data-'+name+']').addBack('[data-'+name+']').filter(function () {
        return typeof jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).data("zfPlugin") === 'undefined';
      });

      // For each plugin found, initialize it
      $elem.each(function() {
        var $el = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this),
            opts = { reflow: true };

        if($el.attr('data-options')){
          $el.attr('data-options').split(';').forEach(function(option){
            var opt = option.split(':').map(function(el){ return el.trim(); });
            if(opt[0]) opts[opt[0]] = parseValue(opt[1]);
          });
        }
        try{
          $el.data('zfPlugin', new plugin(jquery__WEBPACK_IMPORTED_MODULE_0___default()(this), opts));
        }catch(er){
          console.error(er);
        }finally{
          return;
        }
      });
    });
  },
  getFnName: functionName,

  addToJquery: function() {
    // TODO: consider not making this a jQuery function
    // TODO: need way to reflow vs. re-initialize
    /**
     * The Foundation jQuery method.
     * @param {String|Array} method - An action to perform on the current jQuery object.
     */
    var foundation = function(method) {
      var type = typeof method,
          $noJS = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.no-js');

      if($noJS.length){
        $noJS.removeClass('no-js');
      }

      if(type === 'undefined'){//needs to initialize the Foundation object, or an individual plugin.
        _foundation_util_mediaQuery__WEBPACK_IMPORTED_MODULE_2__.MediaQuery._init();
        Foundation.reflow(this);
      }else if(type === 'string'){//an individual method to invoke on a plugin or group of plugins
        var args = Array.prototype.slice.call(arguments, 1);//collect all the arguments, if necessary
        var plugClass = this.data('zfPlugin');//determine the class of plugin

        if(typeof plugClass !== 'undefined' && typeof plugClass[method] !== 'undefined'){//make sure both the class and method exist
          if(this.length === 1){//if there's only one, call it directly.
              plugClass[method].apply(plugClass, args);
          }else{
            this.each(function(i, el){//otherwise loop through the jQuery collection and invoke the method on each
              plugClass[method].apply(jquery__WEBPACK_IMPORTED_MODULE_0___default()(el).data('zfPlugin'), args);
            });
          }
        }else{//error for no class or no method
          throw new ReferenceError("We're sorry, '" + method + "' is not an available method for " + (plugClass ? functionName(plugClass) : 'this element') + '.');
        }
      }else{//error for invalid argument type
        throw new TypeError(`We're sorry, ${type} is not a valid parameter. You must use a string representing the method you wish to invoke.`);
      }
      return this;
    };
    (jquery__WEBPACK_IMPORTED_MODULE_0___default().fn).foundation = foundation;
    return (jquery__WEBPACK_IMPORTED_MODULE_0___default());
  }
};

Foundation.util = {
  /**
   * Function for applying a debounce effect to a function call.
   * @function
   * @param {Function} func - Function to be called at end of timeout.
   * @param {Number} delay - Time in ms to delay the call of `func`.
   * @returns function
   */
  throttle: function (func, delay) {
    var timer = null;

    return function () {
      var context = this, args = arguments;

      if (timer === null) {
        timer = setTimeout(function () {
          func.apply(context, args);
          timer = null;
        }, delay);
      }
    };
  }
};

window.Foundation = Foundation;

// Polyfill for requestAnimationFrame
(function() {
  if (!Date.now || !window.Date.now)
    window.Date.now = Date.now = function() { return new Date().getTime(); };

  var vendors = ['webkit', 'moz'];
  for (var i = 0; i < vendors.length && !window.requestAnimationFrame; ++i) {
      var vp = vendors[i];
      window.requestAnimationFrame = window[vp+'RequestAnimationFrame'];
      window.cancelAnimationFrame = (window[vp+'CancelAnimationFrame']
                                 || window[vp+'CancelRequestAnimationFrame']);
  }
  if (/iP(ad|hone|od).*OS 6/.test(window.navigator.userAgent)
    || !window.requestAnimationFrame || !window.cancelAnimationFrame) {
    var lastTime = 0;
    window.requestAnimationFrame = function(callback) {
        var now = Date.now();
        var nextTime = Math.max(lastTime + 16, now);
        return setTimeout(function() { callback(lastTime = nextTime); },
                          nextTime - now);
    };
    window.cancelAnimationFrame = clearTimeout;
  }
  /**
   * Polyfill for performance.now, required by rAF
   */
  if(!window.performance || !window.performance.now){
    window.performance = {
      start: Date.now(),
      now: function(){ return Date.now() - this.start; }
    };
  }
})();
if (!Function.prototype.bind) {
  /* eslint-disable no-extend-native */
  Function.prototype.bind = function(oThis) {
    if (typeof this !== 'function') {
      // closest thing possible to the ECMAScript 5
      // internal IsCallable function
      throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
    }

    var aArgs   = Array.prototype.slice.call(arguments, 1),
        fToBind = this,
        fNOP    = function() {},
        fBound  = function() {
          return fToBind.apply(this instanceof fNOP
                 ? this
                 : oThis,
                 aArgs.concat(Array.prototype.slice.call(arguments)));
        };

    if (this.prototype) {
      // native functions don't have a prototype
      fNOP.prototype = this.prototype;
    }
    fBound.prototype = new fNOP();

    return fBound;
  };
}
// Polyfill to get the name of a function in IE9
function functionName(fn) {
  if (typeof Function.prototype.name === 'undefined') {
    var funcNameRegex = /function\s([^(]{1,})\(/;
    var results = (funcNameRegex).exec((fn).toString());
    return (results && results.length > 1) ? results[1].trim() : "";
  }
  else if (typeof fn.prototype === 'undefined') {
    return fn.constructor.name;
  }
  else {
    return fn.prototype.constructor.name;
  }
}
function parseValue(str){
  if ('true' === str) return true;
  else if ('false' === str) return false;
  else if (!isNaN(str * 1)) return parseFloat(str);
  return str;
}
// Convert PascalCase to kebab-case
// Thank you: http://stackoverflow.com/a/8955580
function hyphenate(str) {
  return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
}




/***/ }),

/***/ "./node_modules/foundation-sites/js/foundation.core.plugin.js":
/*!********************************************************************!*\
  !*** ./node_modules/foundation-sites/js/foundation.core.plugin.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Plugin: () => (/* binding */ Plugin)
/* harmony export */ });
/* harmony import */ var _foundation_core_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./foundation.core.utils */ "./node_modules/foundation-sites/js/foundation.core.utils.js");


// Abstract class for providing lifecycle hooks. Expect plugins to define AT LEAST
// {function} _setup (replaces previous constructor),
// {function} _destroy (replaces previous destroy)
class Plugin {

  constructor(element, options) {
    this._setup(element, options);
    var pluginName = getPluginName(this);
    this.uuid = (0,_foundation_core_utils__WEBPACK_IMPORTED_MODULE_0__.GetYoDigits)(6, pluginName);

    if(!this.$element.attr(`data-${pluginName}`)){ this.$element.attr(`data-${pluginName}`, this.uuid); }
    if(!this.$element.data('zfPlugin')){ this.$element.data('zfPlugin', this); }
    /**
     * Fires when the plugin has initialized.
     * @event Plugin#init
     */
    this.$element.trigger(`init.zf.${pluginName}`);
  }

  destroy() {
    this._destroy();
    var pluginName = getPluginName(this);
    this.$element.removeAttr(`data-${pluginName}`).removeData('zfPlugin')
        /**
         * Fires when the plugin has been destroyed.
         * @event Plugin#destroyed
         */
        .trigger(`destroyed.zf.${pluginName}`);
    for(var prop in this){
      if (this.hasOwnProperty(prop)) {
        this[prop] = null; //clean up script to prep for garbage collection.
      }
    }
  }
}

// Convert PascalCase to kebab-case
// Thank you: http://stackoverflow.com/a/8955580
function hyphenate(str) {
  return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
}

function getPluginName(obj) {
  return hyphenate(obj.className);
}




/***/ }),

/***/ "./node_modules/foundation-sites/js/foundation.core.utils.js":
/*!*******************************************************************!*\
  !*** ./node_modules/foundation-sites/js/foundation.core.utils.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GetYoDigits: () => (/* binding */ GetYoDigits),
/* harmony export */   RegExpEscape: () => (/* binding */ RegExpEscape),
/* harmony export */   ignoreMousedisappear: () => (/* binding */ ignoreMousedisappear),
/* harmony export */   onLoad: () => (/* binding */ onLoad),
/* harmony export */   rtl: () => (/* binding */ rtl),
/* harmony export */   transitionend: () => (/* binding */ transitionend)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);


// Core Foundation Utilities, utilized in a number of places.

  /**
   * Returns a boolean for RTL support
   */
function rtl() {
  return jquery__WEBPACK_IMPORTED_MODULE_0___default()('html').attr('dir') === 'rtl';
}

/**
 * returns a random base-36 uid with namespacing
 * @function
 * @param {Number} length - number of random base-36 digits desired. Increase for more random strings.
 * @param {String} namespace - name of plugin to be incorporated in uid, optional.
 * @default {String} '' - if no plugin name is provided, nothing is appended to the uid.
 * @returns {String} - unique id
 */
function GetYoDigits(length = 6, namespace){
  let str = '';
  const chars = '0123456789abcdefghijklmnopqrstuvwxyz';
  const charsLength = chars.length;
  for (let i = 0; i < length; i++) {
    str += chars[Math.floor(Math.random() * charsLength)];
  }
  return namespace ? `${str}-${namespace}` : str;
}

/**
 * Escape a string so it can be used as a regexp pattern
 * @function
 * @see https://stackoverflow.com/a/9310752/4317384
 *
 * @param {String} str - string to escape.
 * @returns {String} - escaped string
 */
function RegExpEscape(str){
  return str.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
}

function transitionend($elem){
  var transitions = {
    'transition': 'transitionend',
    'WebkitTransition': 'webkitTransitionEnd',
    'MozTransition': 'transitionend',
    'OTransition': 'otransitionend'
  };
  var elem = document.createElement('div'),
      end;

  for (let transition in transitions){
    if (typeof elem.style[transition] !== 'undefined'){
      end = transitions[transition];
    }
  }
  if (end) {
    return end;
  } else {
    setTimeout(function(){
      $elem.triggerHandler('transitionend', [$elem]);
    }, 1);
    return 'transitionend';
  }
}

/**
 * Return an event type to listen for window load.
 *
 * If `$elem` is passed, an event will be triggered on `$elem`. If window is already loaded, the event will still be triggered.
 * If `handler` is passed, attach it to the event on `$elem`.
 * Calling `onLoad` without handler allows you to get the event type that will be triggered before attaching the handler by yourself.
 * @function
 *
 * @param {Object} [] $elem - jQuery element on which the event will be triggered if passed.
 * @param {Function} [] handler - function to attach to the event.
 * @returns {String} - event type that should or will be triggered.
 */
function onLoad($elem, handler) {
  const didLoad = document.readyState === 'complete';
  const eventType = (didLoad ? '_didLoad' : 'load') + '.zf.util.onLoad';
  const cb = () => $elem.triggerHandler(eventType);

  if ($elem) {
    if (handler) $elem.one(eventType, handler);

    if (didLoad)
      setTimeout(cb);
    else
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).one('load', cb);
  }

  return eventType;
}

/**
 * Retuns an handler for the `mouseleave` that ignore disappeared mouses.
 *
 * If the mouse "disappeared" from the document (like when going on a browser UI element, See https://git.io/zf-11410),
 * the event is ignored.
 * - If the `ignoreLeaveWindow` is `true`, the event is ignored when the user actually left the window
 *   (like by switching to an other window with [Alt]+[Tab]).
 * - If the `ignoreReappear` is `true`, the event will be ignored when the mouse will reappear later on the document
 *   outside of the element it left.
 *
 * @function
 *
 * @param {Function} [] handler - handler for the filtered `mouseleave` event to watch.
 * @param {Object} [] options - object of options:
 * - {Boolean} [false] ignoreLeaveWindow - also ignore when the user switched windows.
 * - {Boolean} [false] ignoreReappear - also ignore when the mouse reappeared outside of the element it left.
 * @returns {Function} - filtered handler to use to listen on the `mouseleave` event.
 */
function ignoreMousedisappear(handler, { ignoreLeaveWindow = false, ignoreReappear = false } = {}) {
  return function leaveEventHandler(eLeave, ...rest) {
    const callback = handler.bind(this, eLeave, ...rest);

    // The mouse left: call the given callback if the mouse entered elsewhere
    if (eLeave.relatedTarget !== null) {
      return callback();
    }

    // Otherwise, check if the mouse actually left the window.
    // In firefox if the user switched between windows, the window sill have the focus by the time
    // the event is triggered. We have to debounce the event to test this case.
    setTimeout(function leaveEventDebouncer() {
      if (!ignoreLeaveWindow && document.hasFocus && !document.hasFocus()) {
        return callback();
      }

      // Otherwise, wait for the mouse to reeapear outside of the element,
      if (!ignoreReappear) {
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(document).one('mouseenter', function reenterEventHandler(eReenter) {
          if (!jquery__WEBPACK_IMPORTED_MODULE_0___default()(eLeave.currentTarget).has(eReenter.target).length) {
            // Fill where the mouse finally entered.
            eLeave.relatedTarget = eReenter.target;
            callback();
          }
        });
      }

    }, 0);
  };
}





/***/ }),

/***/ "./node_modules/foundation-sites/js/foundation.reveal.js":
/*!***************************************************************!*\
  !*** ./node_modules/foundation-sites/js/foundation.reveal.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Reveal: () => (/* binding */ Reveal)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _foundation_core_plugin__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./foundation.core.plugin */ "./node_modules/foundation-sites/js/foundation.core.plugin.js");
/* harmony import */ var _foundation_core_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./foundation.core.utils */ "./node_modules/foundation-sites/js/foundation.core.utils.js");
/* harmony import */ var _foundation_util_keyboard__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./foundation.util.keyboard */ "./node_modules/foundation-sites/js/foundation.util.keyboard.js");
/* harmony import */ var _foundation_util_mediaQuery__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./foundation.util.mediaQuery */ "./node_modules/foundation-sites/js/foundation.util.mediaQuery.js");
/* harmony import */ var _foundation_util_motion__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./foundation.util.motion */ "./node_modules/foundation-sites/js/foundation.util.motion.js");
/* harmony import */ var _foundation_util_triggers__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./foundation.util.triggers */ "./node_modules/foundation-sites/js/foundation.util.triggers.js");
/* harmony import */ var _foundation_util_touch__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./foundation.util.touch */ "./node_modules/foundation-sites/js/foundation.util.touch.js");









/**
 * Reveal module.
 * @module foundation.reveal
 * @requires foundation.util.keyboard
 * @requires foundation.util.touch
 * @requires foundation.util.triggers
 * @requires foundation.util.mediaQuery
 * @requires foundation.util.motion if using animations
 */

class Reveal extends _foundation_core_plugin__WEBPACK_IMPORTED_MODULE_1__.Plugin {
  /**
   * Creates a new instance of Reveal.
   * @class
   * @name Reveal
   * @param {jQuery} element - jQuery object to use for the modal.
   * @param {Object} options - optional parameters.
   */
  _setup(element, options) {
    this.$element = element;
    this.options = jquery__WEBPACK_IMPORTED_MODULE_0___default().extend({}, Reveal.defaults, this.$element.data(), options);
    this.className = 'Reveal'; // ie9 back compat
    this._init();

    // Touch and Triggers init are idempotent, just need to make sure they are initialized
    _foundation_util_touch__WEBPACK_IMPORTED_MODULE_7__.Touch.init((jquery__WEBPACK_IMPORTED_MODULE_0___default()));
    _foundation_util_triggers__WEBPACK_IMPORTED_MODULE_6__.Triggers.init((jquery__WEBPACK_IMPORTED_MODULE_0___default()));

    _foundation_util_keyboard__WEBPACK_IMPORTED_MODULE_3__.Keyboard.register('Reveal', {
      'ESCAPE': 'close',
    });
  }

  /**
   * Initializes the modal by adding the overlay and close buttons, (if selected).
   * @private
   */
  _init() {
    _foundation_util_mediaQuery__WEBPACK_IMPORTED_MODULE_4__.MediaQuery._init();
    this.id = this.$element.attr('id');
    this.isActive = false;
    this.cached = {mq: _foundation_util_mediaQuery__WEBPACK_IMPORTED_MODULE_4__.MediaQuery.current};

    this.$anchor = jquery__WEBPACK_IMPORTED_MODULE_0___default()(`[data-open="${this.id}"]`).length ? jquery__WEBPACK_IMPORTED_MODULE_0___default()(`[data-open="${this.id}"]`) : jquery__WEBPACK_IMPORTED_MODULE_0___default()(`[data-toggle="${this.id}"]`);
    this.$anchor.attr({
      'aria-controls': this.id,
      'aria-haspopup': 'dialog',
      'tabindex': 0
    });

    if (this.options.fullScreen || this.$element.hasClass('full')) {
      this.options.fullScreen = true;
      this.options.overlay = false;
    }
    if (this.options.overlay && !this.$overlay) {
      this.$overlay = this._makeOverlay(this.id);
    }

    this.$element.attr({
        'role': 'dialog',
        'aria-hidden': true,
        'data-yeti-box': this.id,
        'data-resize': this.id
    });

    if(this.$overlay) {
      this.$element.detach().appendTo(this.$overlay);
    } else {
      this.$element.detach().appendTo(jquery__WEBPACK_IMPORTED_MODULE_0___default()(this.options.appendTo));
      this.$element.addClass('without-overlay');
    }
    this._events();
    if (this.options.deepLink && window.location.hash === ( `#${this.id}`)) {
      this.onLoadListener = (0,_foundation_core_utils__WEBPACK_IMPORTED_MODULE_2__.onLoad)(jquery__WEBPACK_IMPORTED_MODULE_0___default()(window), () => this.open());
    }
  }

  /**
   * Creates an overlay div to display behind the modal.
   * @private
   */
  _makeOverlay() {
    var additionalOverlayClasses = '';

    if (this.options.additionalOverlayClasses) {
      additionalOverlayClasses = ' ' + this.options.additionalOverlayClasses;
    }

    return jquery__WEBPACK_IMPORTED_MODULE_0___default()('<div></div>')
      .addClass('reveal-overlay' + additionalOverlayClasses)
      .appendTo(this.options.appendTo);
  }

  /**
   * Updates position of modal
   * TODO:  Figure out if we actually need to cache these values or if it doesn't matter
   * @private
   */
  _updatePosition() {
    var width = this.$element.outerWidth();
    var outerWidth = jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).width();
    var height = this.$element.outerHeight();
    var outerHeight = jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).height();
    var left, top = null;
    if (this.options.hOffset === 'auto') {
      left = parseInt((outerWidth - width) / 2, 10);
    } else {
      left = parseInt(this.options.hOffset, 10);
    }
    if (this.options.vOffset === 'auto') {
      if (height > outerHeight) {
        top = parseInt(Math.min(100, outerHeight / 10), 10);
      } else {
        top = parseInt((outerHeight - height) / 4, 10);
      }
    } else if (this.options.vOffset !== null) {
      top = parseInt(this.options.vOffset, 10);
    }

    if (top !== null) {
      this.$element.css({top: top + 'px'});
    }

    // only worry about left if we don't have an overlay or we have a horizontal offset,
    // otherwise we're perfectly in the middle
    if (!this.$overlay || (this.options.hOffset !== 'auto')) {
      this.$element.css({left: left + 'px'});
      this.$element.css({margin: '0px'});
    }

  }

  /**
   * Adds event handlers for the modal.
   * @private
   */
  _events() {
    var _this = this;

    this.$element.on({
      'open.zf.trigger': this.open.bind(this),
      'close.zf.trigger': (event, $element) => {
        if ((event.target === _this.$element[0]) ||
            (jquery__WEBPACK_IMPORTED_MODULE_0___default()(event.target).parents('[data-closable]')[0] === $element)) { // only close reveal when it's explicitly called
          return this.close.apply(this);
        }
      },
      'toggle.zf.trigger': this.toggle.bind(this),
      'resizeme.zf.trigger': function() {
        _this._updatePosition();
      }
    });

    if (this.options.closeOnClick && this.options.overlay) {
      this.$overlay.off('.zf.reveal').on('click.zf.dropdown tap.zf.dropdown', function(e) {
        if (e.target === _this.$element[0] ||
          jquery__WEBPACK_IMPORTED_MODULE_0___default().contains(_this.$element[0], e.target) ||
            !jquery__WEBPACK_IMPORTED_MODULE_0___default().contains(document, e.target)) {
              return;
        }
        _this.close();
      });
    }
    if (this.options.deepLink) {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).on(`hashchange.zf.reveal:${this.id}`, this._handleState.bind(this));
    }
  }

  /**
   * Handles modal methods on back/forward button clicks or any other event that triggers hashchange.
   * @private
   */
  _handleState() {
    if(window.location.hash === ( '#' + this.id) && !this.isActive){ this.open(); }
    else{ this.close(); }
  }

  /**
  * Disables the scroll when Reveal is shown to prevent the background from shifting
  * @param {number} scrollTop - Scroll to visually apply, window current scroll by default
  */
  _disableScroll(scrollTop) {
    scrollTop = scrollTop || jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).scrollTop();
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()(document).height() > jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).height()) {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()("html")
        .css("top", -scrollTop);
    }
  }

  /**
  * Reenables the scroll when Reveal closes
  * @param {number} scrollTop - Scroll to restore, html "top" property by default (as set by `_disableScroll`)
  */
  _enableScroll(scrollTop) {
    scrollTop = scrollTop || parseInt(jquery__WEBPACK_IMPORTED_MODULE_0___default()("html").css("top"), 10);
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()(document).height() > jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).height()) {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()("html")
        .css("top", "");
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).scrollTop(-scrollTop);
    }
  }


  /**
   * Opens the modal controlled by `this.$anchor`, and closes all others by default.
   * @function
   * @fires Reveal#closeme
   * @fires Reveal#open
   */
  open() {
    // either update or replace browser history
    const hash = `#${this.id}`;
    if (this.options.deepLink && window.location.hash !== hash) {

      if (window.history.pushState) {
        if (this.options.updateHistory) {
          window.history.pushState({}, '', hash);
        } else {
          window.history.replaceState({}, '', hash);
        }
      } else {
        window.location.hash = hash;
      }
    }

    // Remember anchor that opened it to set focus back later, have general anchors as fallback
    this.$activeAnchor = jquery__WEBPACK_IMPORTED_MODULE_0___default()(document.activeElement).is(this.$anchor) ? jquery__WEBPACK_IMPORTED_MODULE_0___default()(document.activeElement) : this.$anchor;

    this.isActive = true;

    // Make elements invisible, but remove display: none so we can get size and positioning
    this.$element
        .css({ 'visibility': 'hidden' })
        .show()
        .scrollTop(0);
    if (this.options.overlay) {
      this.$overlay.css({'visibility': 'hidden'}).show();
    }

    this._updatePosition();

    this.$element
      .hide()
      .css({ 'visibility': '' });

    if(this.$overlay) {
      this.$overlay.css({'visibility': ''}).hide();
      if(this.$element.hasClass('fast')) {
        this.$overlay.addClass('fast');
      } else if (this.$element.hasClass('slow')) {
        this.$overlay.addClass('slow');
      }
    }


    if (!this.options.multipleOpened) {
      /**
       * Fires immediately before the modal opens.
       * Closes any other modals that are currently open
       * @event Reveal#closeme
       */
      this.$element.trigger('closeme.zf.reveal', this.id);
    }

    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('.reveal:visible').length === 0) {
      this._disableScroll();
    }

    var _this = this;

    // Motion UI method of reveal
    if (this.options.animationIn) {
      function afterAnimation(){
        _this.$element
          .attr({
            'aria-hidden': false,
            'tabindex': -1
          })
          .focus();
        _this._addGlobalClasses();
        _foundation_util_keyboard__WEBPACK_IMPORTED_MODULE_3__.Keyboard.trapFocus(_this.$element);
      }
      if (this.options.overlay) {
        _foundation_util_motion__WEBPACK_IMPORTED_MODULE_5__.Motion.animateIn(this.$overlay, 'fade-in');
      }
      _foundation_util_motion__WEBPACK_IMPORTED_MODULE_5__.Motion.animateIn(this.$element, this.options.animationIn, () => {
        if(this.$element) { // protect against object having been removed
          this.focusableElements = _foundation_util_keyboard__WEBPACK_IMPORTED_MODULE_3__.Keyboard.findFocusable(this.$element);
          afterAnimation();
        }
      });
    }
    // jQuery method of reveal
    else {
      if (this.options.overlay) {
        this.$overlay.show(0);
      }
      this.$element.show(this.options.showDelay);
    }

    // handle accessibility
    this.$element
      .attr({
        'aria-hidden': false,
        'tabindex': -1
      })
      .focus();
    _foundation_util_keyboard__WEBPACK_IMPORTED_MODULE_3__.Keyboard.trapFocus(this.$element);

    this._addGlobalClasses();

    this._addGlobalListeners();

    /**
     * Fires when the modal has successfully opened.
     * @event Reveal#open
     */
    this.$element.trigger('open.zf.reveal');
  }

  /**
   * Adds classes and listeners on document required by open modals.
   *
   * The following classes are added and updated:
   * - `.is-reveal-open` - Prevents the scroll on document
   * - `.zf-has-scroll`  - Displays a disabled scrollbar on document if required like if the
   *                       scroll was not disabled. This prevent a "shift" of the page content due
   *                       the scrollbar disappearing when the modal opens.
   *
   * @private
   */
  _addGlobalClasses() {
    const updateScrollbarClass = () => {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()('html').toggleClass('zf-has-scroll', !!(jquery__WEBPACK_IMPORTED_MODULE_0___default()(document).height() > jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).height()));
    };

    this.$element.on('resizeme.zf.trigger.revealScrollbarListener', () => updateScrollbarClass());
    updateScrollbarClass();
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('html').addClass('is-reveal-open');
  }

  /**
   * Removes classes and listeners on document that were required by open modals.
   * @private
   */
  _removeGlobalClasses() {
    this.$element.off('resizeme.zf.trigger.revealScrollbarListener');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('html').removeClass('is-reveal-open');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('html').removeClass('zf-has-scroll');
  }

  /**
   * Adds extra event handlers for the body and window if necessary.
   * @private
   */
  _addGlobalListeners() {
    var _this = this;
    if(!this.$element) { return; } // If we're in the middle of cleanup, don't freak out
    this.focusableElements = _foundation_util_keyboard__WEBPACK_IMPORTED_MODULE_3__.Keyboard.findFocusable(this.$element);

    if (!this.options.overlay && this.options.closeOnClick && !this.options.fullScreen) {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()('body').on('click.zf.dropdown tap.zf.dropdown', function(e) {
        if (e.target === _this.$element[0] ||
          jquery__WEBPACK_IMPORTED_MODULE_0___default().contains(_this.$element[0], e.target) ||
            !jquery__WEBPACK_IMPORTED_MODULE_0___default().contains(document, e.target)) { return; }
        _this.close();
      });
    }

    if (this.options.closeOnEsc) {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).on('keydown.zf.reveal', function(e) {
        _foundation_util_keyboard__WEBPACK_IMPORTED_MODULE_3__.Keyboard.handleKey(e, 'Reveal', {
          close: function() {
            if (_this.options.closeOnEsc) {
              _this.close();
            }
          }
        });
      });
    }
  }

  /**
   * Closes the modal.
   * @function
   * @fires Reveal#closed
   */
  close() {
    if (!this.isActive || !this.$element.is(':visible')) {
      return false;
    }
    var _this = this;

    // Motion UI method of hiding
    if (this.options.animationOut) {
      if (this.options.overlay) {
        _foundation_util_motion__WEBPACK_IMPORTED_MODULE_5__.Motion.animateOut(this.$overlay, 'fade-out');
      }

      _foundation_util_motion__WEBPACK_IMPORTED_MODULE_5__.Motion.animateOut(this.$element, this.options.animationOut, finishUp);
    }
    // jQuery method of hiding
    else {
      this.$element.hide(this.options.hideDelay);

      if (this.options.overlay) {
        this.$overlay.hide(0, finishUp);
      }
      else {
        finishUp();
      }
    }

    // Conditionals to remove extra event listeners added on open
    if (this.options.closeOnEsc) {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).off('keydown.zf.reveal');
    }

    if (!this.options.overlay && this.options.closeOnClick) {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()('body').off('click.zf.dropdown tap.zf.dropdown');
    }

    this.$element.off('keydown.zf.reveal');

    function finishUp() {

      // Get the current top before the modal is closed and restore the scroll after.
      // TODO: use component properties instead of HTML properties
      // See https://github.com/foundation/foundation-sites/pull/10786
      var scrollTop = parseInt(jquery__WEBPACK_IMPORTED_MODULE_0___default()("html").css("top"), 10);

      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('.reveal:visible').length  === 0) {
        _this._removeGlobalClasses(); // also remove .is-reveal-open from the html element when there is no opened reveal
      }

      _foundation_util_keyboard__WEBPACK_IMPORTED_MODULE_3__.Keyboard.releaseFocus(_this.$element);

      _this.$element.attr('aria-hidden', true);

      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('.reveal:visible').length  === 0) {
        _this._enableScroll(scrollTop);
      }

      /**
      * Fires when the modal is done closing.
      * @event Reveal#closed
      */
      _this.$element.trigger('closed.zf.reveal');
    }

    /**
    * Resets the modal content
    * This prevents a running video to keep going in the background
    */
    if (this.options.resetOnClose) {
      this.$element.html(this.$element.html());
    }

    this.isActive = false;
    // If deepLink and we did not switched to an other modal...
    if (_this.options.deepLink && window.location.hash === `#${this.id}`) {
      // Remove the history hash
      if (window.history.replaceState) {
        const urlWithoutHash = window.location.pathname + window.location.search;
        if (this.options.updateHistory) {
          window.history.pushState({}, '', urlWithoutHash); // remove the hash
        } else {
          window.history.replaceState('', document.title, urlWithoutHash);
        }
      } else {
        window.location.hash = '';
      }
    }

    this.$activeAnchor.focus();
  }

  /**
   * Toggles the open/closed state of a modal.
   * @function
   */
  toggle() {
    if (this.isActive) {
      this.close();
    } else {
      this.open();
    }
  };

  /**
   * Destroys an instance of a modal.
   * @function
   */
  _destroy() {
    if (this.options.overlay) {
      this.$element.appendTo(jquery__WEBPACK_IMPORTED_MODULE_0___default()(this.options.appendTo)); // move $element outside of $overlay to prevent error unregisterPlugin()
      this.$overlay.hide().off().remove();
    }
    this.$element.hide().off();
    this.$anchor.off('.zf');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).off(`.zf.reveal:${this.id}`)
    if (this.onLoadListener) jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).off(this.onLoadListener);

    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('.reveal:visible').length  === 0) {
      this._removeGlobalClasses(); // also remove .is-reveal-open from the html element when there is no opened reveal
    }
  };
}

Reveal.defaults = {
  /**
   * Motion-UI class to use for animated elements. If none used, defaults to simple show/hide.
   * @option
   * @type {string}
   * @default ''
   */
  animationIn: '',
  /**
   * Motion-UI class to use for animated elements. If none used, defaults to simple show/hide.
   * @option
   * @type {string}
   * @default ''
   */
  animationOut: '',
  /**
   * Time, in ms, to delay the opening of a modal after a click if no animation used.
   * @option
   * @type {number}
   * @default 0
   */
  showDelay: 0,
  /**
   * Time, in ms, to delay the closing of a modal after a click if no animation used.
   * @option
   * @type {number}
   * @default 0
   */
  hideDelay: 0,
  /**
   * Allows a click on the body/overlay to close the modal.
   * @option
   * @type {boolean}
   * @default true
   */
  closeOnClick: true,
  /**
   * Allows the modal to close if the user presses the `ESCAPE` key.
   * @option
   * @type {boolean}
   * @default true
   */
  closeOnEsc: true,
  /**
   * If true, allows multiple modals to be displayed at once.
   * @option
   * @type {boolean}
   * @default false
   */
  multipleOpened: false,
  /**
   * Distance, in pixels, the modal should push down from the top of the screen.
   * @option
   * @type {number|string}
   * @default auto
   */
  vOffset: 'auto',
  /**
   * Distance, in pixels, the modal should push in from the side of the screen.
   * @option
   * @type {number|string}
   * @default auto
   */
  hOffset: 'auto',
  /**
   * Allows the modal to be fullscreen, completely blocking out the rest of the view. JS checks for this as well.
   * @option
   * @type {boolean}
   * @default false
   */
  fullScreen: false,
  /**
   * Allows the modal to generate an overlay div, which will cover the view when modal opens.
   * @option
   * @type {boolean}
   * @default true
   */
  overlay: true,
  /**
   * Allows the modal to remove and reinject markup on close. Should be true if using video elements w/o using provider's api, otherwise, videos will continue to play in the background.
   * @option
   * @type {boolean}
   * @default false
   */
  resetOnClose: false,
  /**
   * Link the location hash to the modal.
   * Set the location hash when the modal is opened/closed, and open/close the modal when the location changes.
   * @option
   * @type {boolean}
   * @default false
   */
  deepLink: false,
  /**
   * If `deepLink` is enabled, update the browser history with the open modal
   * @option
   * @default false
   */
  updateHistory: false,
    /**
   * Allows the modal to append to custom div.
   * @option
   * @type {string}
   * @default "body"
   */
  appendTo: "body",
  /**
   * Allows adding additional class names to the reveal overlay.
   * @option
   * @type {string}
   * @default ''
   */
  additionalOverlayClasses: ''
};




/***/ }),

/***/ "./node_modules/foundation-sites/js/foundation.util.keyboard.js":
/*!**********************************************************************!*\
  !*** ./node_modules/foundation-sites/js/foundation.util.keyboard.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Keyboard: () => (/* binding */ Keyboard)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _foundation_core_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./foundation.core.utils */ "./node_modules/foundation-sites/js/foundation.core.utils.js");
/*******************************************
 *                                         *
 * This util was created by Marius Olbertz *
 * Please thank Marius on GitHub /owlbertz *
 * or the web http://www.mariusolbertz.de/ *
 *                                         *
 ******************************************/




const keyCodes = {
  9: 'TAB',
  13: 'ENTER',
  27: 'ESCAPE',
  32: 'SPACE',
  35: 'END',
  36: 'HOME',
  37: 'ARROW_LEFT',
  38: 'ARROW_UP',
  39: 'ARROW_RIGHT',
  40: 'ARROW_DOWN'
}

var commands = {}

// Functions pulled out to be referenceable from internals
function findFocusable($element) {
  if(!$element) {return false; }
  return $element.find('a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, *[tabindex], *[contenteditable]').filter(function() {
    if (!jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).is(':visible') || jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).attr('tabindex') < 0) { return false; } //only have visible elements and those that have a tabindex greater or equal 0
    return true;
  })
  .sort( function( a, b ) {
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()(a).attr('tabindex') === jquery__WEBPACK_IMPORTED_MODULE_0___default()(b).attr('tabindex')) {
      return 0;
    }
    let aTabIndex = parseInt(jquery__WEBPACK_IMPORTED_MODULE_0___default()(a).attr('tabindex'), 10),
      bTabIndex = parseInt(jquery__WEBPACK_IMPORTED_MODULE_0___default()(b).attr('tabindex'), 10);
    // Undefined is treated the same as 0
    if (typeof jquery__WEBPACK_IMPORTED_MODULE_0___default()(a).attr('tabindex') === 'undefined' && bTabIndex > 0) {
      return 1;
    }
    if (typeof jquery__WEBPACK_IMPORTED_MODULE_0___default()(b).attr('tabindex') === 'undefined' && aTabIndex > 0) {
      return -1;
    }
    if (aTabIndex === 0 && bTabIndex > 0) {
      return 1;
    }
    if (bTabIndex === 0 && aTabIndex > 0) {
      return -1;
    }
    if (aTabIndex < bTabIndex) {
      return -1;
    }
    if (aTabIndex > bTabIndex) {
      return 1;
    }
  });
}

function parseKey(event) {
  var key = keyCodes[event.which || event.keyCode] || String.fromCharCode(event.which).toUpperCase();

  // Remove un-printable characters, e.g. for `fromCharCode` calls for CTRL only events
  key = key.replace(/\W+/, '');

  if (event.shiftKey) key = `SHIFT_${key}`;
  if (event.ctrlKey) key = `CTRL_${key}`;
  if (event.altKey) key = `ALT_${key}`;

  // Remove trailing underscore, in case only modifiers were used (e.g. only `CTRL_ALT`)
  key = key.replace(/_$/, '');

  return key;
}

var Keyboard = {
  keys: getKeyCodes(keyCodes),

  /**
   * Parses the (keyboard) event and returns a String that represents its key
   * Can be used like Foundation.parseKey(event) === Foundation.keys.SPACE
   * @param {Event} event - the event generated by the event handler
   * @return String key - String that represents the key pressed
   */
  parseKey: parseKey,

  /**
   * Handles the given (keyboard) event
   * @param {Event} event - the event generated by the event handler
   * @param {String} component - Foundation component's name, e.g. Slider or Reveal
   * @param {Objects} functions - collection of functions that are to be executed
   */
  handleKey(event, component, functions) {
    var commandList = commands[component],
      keyCode = this.parseKey(event),
      cmds,
      command,
      fn;

    if (!commandList) return console.warn('Component not defined!');

    // Ignore the event if it was already handled
    if (event.zfIsKeyHandled === true) return;

    // This component does not differentiate between ltr and rtl
    if (typeof commandList.ltr === 'undefined') {
        cmds = commandList; // use plain list
    } else { // merge ltr and rtl: if document is rtl, rtl overwrites ltr and vice versa
        if ((0,_foundation_core_utils__WEBPACK_IMPORTED_MODULE_1__.rtl)()) cmds = jquery__WEBPACK_IMPORTED_MODULE_0___default().extend({}, commandList.ltr, commandList.rtl);

        else cmds = jquery__WEBPACK_IMPORTED_MODULE_0___default().extend({}, commandList.rtl, commandList.ltr);
    }
    command = cmds[keyCode];

    fn = functions[command];
     // Execute the handler if found
    if (fn && typeof fn === 'function') {
      var returnValue = fn.apply();

      // Mark the event as "handled" to prevent future handlings
      event.zfIsKeyHandled = true;

      // Execute function when event was handled
      if (functions.handled || typeof functions.handled === 'function') {
          functions.handled(returnValue);
      }
    } else {
       // Execute function when event was not handled
      if (functions.unhandled || typeof functions.unhandled === 'function') {
          functions.unhandled();
      }
    }
  },

  /**
   * Finds all focusable elements within the given `$element`
   * @param {jQuery} $element - jQuery object to search within
   * @return {jQuery} $focusable - all focusable elements within `$element`
   */

  findFocusable: findFocusable,

  /**
   * Returns the component name name
   * @param {Object} component - Foundation component, e.g. Slider or Reveal
   * @return String componentName
   */

  register(componentName, cmds) {
    commands[componentName] = cmds;
  },


  // TODO9438: These references to Keyboard need to not require global. Will 'this' work in this context?
  //
  /**
   * Traps the focus in the given element.
   * @param  {jQuery} $element  jQuery object to trap the foucs into.
   */
  trapFocus($element) {
    var $focusable = findFocusable($element),
        $firstFocusable = $focusable.eq(0),
        $lastFocusable = $focusable.eq(-1);

    $element.on('keydown.zf.trapfocus', function(event) {
      if (event.target === $lastFocusable[0] && parseKey(event) === 'TAB') {
        event.preventDefault();
        $firstFocusable.focus();
      }
      else if (event.target === $firstFocusable[0] && parseKey(event) === 'SHIFT_TAB') {
        event.preventDefault();
        $lastFocusable.focus();
      }
    });
  },
  /**
   * Releases the trapped focus from the given element.
   * @param  {jQuery} $element  jQuery object to release the focus for.
   */
  releaseFocus($element) {
    $element.off('keydown.zf.trapfocus');
  }
}

/*
 * Constants for easier comparing.
 * Can be used like Foundation.parseKey(event) === Foundation.keys.SPACE
 */
function getKeyCodes(kcs) {
  var k = {};
  for (var kc in kcs) {
    if (kcs.hasOwnProperty(kc)) k[kcs[kc]] = kcs[kc];
  }
  return k;
}




/***/ }),

/***/ "./node_modules/foundation-sites/js/foundation.util.mediaQuery.js":
/*!************************************************************************!*\
  !*** ./node_modules/foundation-sites/js/foundation.util.mediaQuery.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MediaQuery: () => (/* binding */ MediaQuery)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);


// Default set of media queries
// const defaultQueries = {
//   'default' : 'only screen',
//   landscape : 'only screen and (orientation: landscape)',
//   portrait : 'only screen and (orientation: portrait)',
//   retina : 'only screen and (-webkit-min-device-pixel-ratio: 2),' +
//     'only screen and (min--moz-device-pixel-ratio: 2),' +
//     'only screen and (-o-min-device-pixel-ratio: 2/1),' +
//     'only screen and (min-device-pixel-ratio: 2),' +
//     'only screen and (min-resolution: 192dpi),' +
//     'only screen and (min-resolution: 2dppx)'
//   };


// matchMedia() polyfill - Test a CSS media type/query in JS.
// Authors & copyright © 2012: Scott Jehl, Paul Irish, Nicholas Zakas, David Knight. MIT license
/* eslint-disable */
window.matchMedia || (window.matchMedia = (function () {
  "use strict";

  // For browsers that support matchMedium api such as IE 9 and webkit
  var styleMedia = (window.styleMedia || window.media);

  // For those that don't support matchMedium
  if (!styleMedia) {
    var style   = document.createElement('style'),
    script      = document.getElementsByTagName('script')[0],
    info        = null;

    style.type  = 'text/css';
    style.id    = 'matchmediajs-test';

    if (!script) {
      document.head.appendChild(style);
    } else {
      script.parentNode.insertBefore(style, script);
    }

    // 'style.currentStyle' is used by IE <= 8 and 'window.getComputedStyle' for all other browsers
    info = ('getComputedStyle' in window) && window.getComputedStyle(style, null) || style.currentStyle;

    styleMedia = {
      matchMedium: function (media) {
        var text = '@media ' + media + '{ #matchmediajs-test { width: 1px; } }';

        // 'style.styleSheet' is used by IE <= 8 and 'style.textContent' for all other browsers
        if (style.styleSheet) {
          style.styleSheet.cssText = text;
        } else {
          style.textContent = text;
        }

        // Test if media query is true or false
        return info.width === '1px';
      }
    };
  }

  return function(media) {
    return {
      matches: styleMedia.matchMedium(media || 'all'),
      media: media || 'all'
    };
  };
})());
/* eslint-enable */

var MediaQuery = {
  queries: [],

  current: '',

  /**
   * Initializes the media query helper, by extracting the breakpoint list from the CSS and activating the breakpoint watcher.
   * @function
   * @private
   */
  _init() {

    // make sure the initialization is only done once when calling _init() several times
    if (this.isInitialized === true) {
      return this;
    } else {
      this.isInitialized = true;
    }

    var self = this;
    var $meta = jquery__WEBPACK_IMPORTED_MODULE_0___default()('meta.foundation-mq');
    if(!$meta.length){
      jquery__WEBPACK_IMPORTED_MODULE_0___default()('<meta class="foundation-mq" name="foundation-mq" content>').appendTo(document.head);
    }

    var extractedStyles = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.foundation-mq').css('font-family');
    var namedQueries;

    namedQueries = parseStyleToObject(extractedStyles);

    self.queries = []; // reset

    for (var key in namedQueries) {
      if(namedQueries.hasOwnProperty(key)) {
        self.queries.push({
          name: key,
          value: `only screen and (min-width: ${namedQueries[key]})`
        });
      }
    }

    this.current = this._getCurrentSize();

    this._watcher();
  },

  /**
   * Reinitializes the media query helper.
   * Useful if your CSS breakpoint configuration has just been loaded or has changed since the initialization.
   * @function
   * @private
   */
  _reInit() {
    this.isInitialized = false;
    this._init();
  },

  /**
   * Checks if the screen is at least as wide as a breakpoint.
   * @function
   * @param {String} size - Name of the breakpoint to check.
   * @returns {Boolean} `true` if the breakpoint matches, `false` if it's smaller.
   */
  atLeast(size) {
    var query = this.get(size);

    if (query) {
      return window.matchMedia(query).matches;
    }

    return false;
  },

  /**
   * Checks if the screen is within the given breakpoint.
   * If smaller than the breakpoint of larger than its upper limit it returns false.
   * @function
   * @param {String} size - Name of the breakpoint to check.
   * @returns {Boolean} `true` if the breakpoint matches, `false` otherwise.
   */
  only(size) {
    return size === this._getCurrentSize();
  },

  /**
   * Checks if the screen is within a breakpoint or smaller.
   * @function
   * @param {String} size - Name of the breakpoint to check.
   * @returns {Boolean} `true` if the breakpoint matches, `false` if it's larger.
   */
  upTo(size) {
    const nextSize = this.next(size);

    // If the next breakpoint does not match, the screen is smaller than
    // the upper limit of this breakpoint.
    if (nextSize) {
      return !this.atLeast(nextSize);
    }

    // If there is no next breakpoint, the "size" breakpoint does not have
    // an upper limit and the screen will always be within it or smaller.
    return true;
  },

  /**
   * Checks if the screen matches to a breakpoint.
   * @function
   * @param {String} size - Name of the breakpoint to check, either 'small only' or 'small'. Omitting 'only' falls back to using atLeast() method.
   * @returns {Boolean} `true` if the breakpoint matches, `false` if it does not.
   */
  is(size) {
    const parts = size.trim().split(' ').filter(p => !!p.length);
    const [bpSize, bpModifier = ''] = parts;

    // Only the breakpont
    if (bpModifier === 'only') {
      return this.only(bpSize);
    }
    // At least the breakpoint (included)
    if (!bpModifier || bpModifier === 'up') {
      return this.atLeast(bpSize);
    }
    // Up to the breakpoint (included)
    if (bpModifier === 'down') {
      return this.upTo(bpSize);
    }

    throw new Error(`
      Invalid breakpoint passed to MediaQuery.is().
      Expected a breakpoint name formatted like "<size> <modifier>", got "${size}".
    `);
  },

  /**
   * Gets the media query of a breakpoint.
   * @function
   * @param {String} size - Name of the breakpoint to get.
   * @returns {String|null} - The media query of the breakpoint, or `null` if the breakpoint doesn't exist.
   */
  get(size) {
    for (var i in this.queries) {
      if(this.queries.hasOwnProperty(i)) {
        var query = this.queries[i];
        if (size === query.name) return query.value;
      }
    }

    return null;
  },

  /**
   * Get the breakpoint following the given breakpoint.
   * @function
   * @param {String} size - Name of the breakpoint.
   * @returns {String|null} - The name of the following breakpoint, or `null` if the passed breakpoint was the last one.
   */
  next(size) {
    const queryIndex = this.queries.findIndex((q) => this._getQueryName(q) === size);
    if (queryIndex === -1) {
      throw new Error(`
        Unknown breakpoint "${size}" passed to MediaQuery.next().
        Ensure it is present in your Sass "$breakpoints" setting.
      `);
    }

    const nextQuery = this.queries[queryIndex + 1];
    return nextQuery ? nextQuery.name : null;
  },

  /**
   * Returns the name of the breakpoint related to the given value.
   * @function
   * @private
   * @param {String|Object} value - Breakpoint name or query object.
   * @returns {String} Name of the breakpoint.
   */
  _getQueryName(value) {
    if (typeof value === 'string')
      return value;
    if (typeof value === 'object')
      return value.name;
    throw new TypeError(`
      Invalid value passed to MediaQuery._getQueryName().
      Expected a breakpoint name (String) or a breakpoint query (Object), got "${value}" (${typeof value})
    `);
  },

  /**
   * Gets the current breakpoint name by testing every breakpoint and returning the last one to match (the biggest one).
   * @function
   * @private
   * @returns {String} Name of the current breakpoint.
   */
  _getCurrentSize() {
    var matched;

    for (var i = 0; i < this.queries.length; i++) {
      var query = this.queries[i];

      if (window.matchMedia(query.value).matches) {
        matched = query;
      }
    }

    return matched && this._getQueryName(matched);
  },

  /**
   * Activates the breakpoint watcher, which fires an event on the window whenever the breakpoint changes.
   * @function
   * @private
   */
  _watcher() {
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).on('resize.zf.trigger', () => {
      var newSize = this._getCurrentSize(), currentSize = this.current;

      if (newSize !== currentSize) {
        // Change the current media query
        this.current = newSize;

        // Broadcast the media query change on the window
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).trigger('changed.zf.mediaquery', [newSize, currentSize]);
      }
    });
  }
};



// Thank you: https://github.com/sindresorhus/query-string
function parseStyleToObject(str) {
  var styleObject = {};

  if (typeof str !== 'string') {
    return styleObject;
  }

  str = str.trim().slice(1, -1); // browsers re-quote string style values

  if (!str) {
    return styleObject;
  }

  styleObject = str.split('&').reduce(function(ret, param) {
    var parts = param.replace(/\+/g, ' ').split('=');
    var key = parts[0];
    var val = parts[1];
    key = decodeURIComponent(key);

    // missing `=` should be `null`:
    // http://w3.org/TR/2012/WD-url-20120524/#collect-url-parameters
    val = typeof val === 'undefined' ? null : decodeURIComponent(val);

    if (!ret.hasOwnProperty(key)) {
      ret[key] = val;
    } else if (Array.isArray(ret[key])) {
      ret[key].push(val);
    } else {
      ret[key] = [ret[key], val];
    }
    return ret;
  }, {});

  return styleObject;
}




/***/ }),

/***/ "./node_modules/foundation-sites/js/foundation.util.motion.js":
/*!********************************************************************!*\
  !*** ./node_modules/foundation-sites/js/foundation.util.motion.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Motion: () => (/* binding */ Motion),
/* harmony export */   Move: () => (/* binding */ Move)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _foundation_core_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./foundation.core.utils */ "./node_modules/foundation-sites/js/foundation.core.utils.js");



/**
 * Motion module.
 * @module foundation.motion
 */

const initClasses   = ['mui-enter', 'mui-leave'];
const activeClasses = ['mui-enter-active', 'mui-leave-active'];

const Motion = {
  animateIn: function(element, animation, cb) {
    animate(true, element, animation, cb);
  },

  animateOut: function(element, animation, cb) {
    animate(false, element, animation, cb);
  }
}

function Move(duration, elem, fn){
  var anim, prog, start = null;

  if (duration === 0) {
    fn.apply(elem);
    elem.trigger('finished.zf.animate', [elem]).triggerHandler('finished.zf.animate', [elem]);
    return;
  }

  function move(ts){
    if(!start) start = ts;
    prog = ts - start;
    fn.apply(elem);

    if(prog < duration){ anim = window.requestAnimationFrame(move, elem); }
    else{
      window.cancelAnimationFrame(anim);
      elem.trigger('finished.zf.animate', [elem]).triggerHandler('finished.zf.animate', [elem]);
    }
  }
  anim = window.requestAnimationFrame(move);
}

/**
 * Animates an element in or out using a CSS transition class.
 * @function
 * @private
 * @param {Boolean} isIn - Defines if the animation is in or out.
 * @param {Object} element - jQuery or HTML object to animate.
 * @param {String} animation - CSS class to use.
 * @param {Function} cb - Callback to run when animation is finished.
 */
function animate(isIn, element, animation, cb) {
  element = jquery__WEBPACK_IMPORTED_MODULE_0___default()(element).eq(0);

  if (!element.length) return;

  var initClass = isIn ? initClasses[0] : initClasses[1];
  var activeClass = isIn ? activeClasses[0] : activeClasses[1];

  // Set up the animation
  reset();

  element
    .addClass(animation)
    .css('transition', 'none');

  requestAnimationFrame(() => {
    element.addClass(initClass);
    if (isIn) element.show();
  });

  // Start the animation
  requestAnimationFrame(() => {
    // will trigger the browser to synchronously calculate the style and layout
    // also called reflow or layout thrashing
    // see https://gist.github.com/paulirish/5d52fb081b3570c81e3a
    element[0].offsetWidth;
    element
      .css('transition', '')
      .addClass(activeClass);
  });

  // Clean up the animation when it finishes
  element.one((0,_foundation_core_utils__WEBPACK_IMPORTED_MODULE_1__.transitionend)(element), finish);

  // Hides the element (for out animations), resets the element, and runs a callback
  function finish() {
    if (!isIn) element.hide();
    reset();
    if (cb) cb.apply(element);
  }

  // Resets transitions and removes motion-specific classes
  function reset() {
    element[0].style.transitionDuration = 0;
    element.removeClass(`${initClass} ${activeClass} ${animation}`);
  }
}





/***/ }),

/***/ "./node_modules/foundation-sites/js/foundation.util.touch.js":
/*!*******************************************************************!*\
  !*** ./node_modules/foundation-sites/js/foundation.util.touch.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Touch: () => (/* binding */ Touch)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
//**************************************************
//**Work inspired by multiple jquery swipe plugins**
//**Done by Yohai Ararat ***************************
//**************************************************



var Touch = {};

var startPosX,
    startTime,
    elapsedTime,
    startEvent,
    isMoving = false,
    didMoved = false;

function onTouchEnd(e) {
  this.removeEventListener('touchmove', onTouchMove);
  this.removeEventListener('touchend', onTouchEnd);

  // If the touch did not move, consider it as a "tap"
  if (!didMoved) {
    var tapEvent = jquery__WEBPACK_IMPORTED_MODULE_0___default().Event('tap', startEvent || e);
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).trigger(tapEvent);
  }

  startEvent = null;
  isMoving = false;
  didMoved = false;
}

function onTouchMove(e) {
  if (true === (jquery__WEBPACK_IMPORTED_MODULE_0___default().spotSwipe).preventDefault) { e.preventDefault(); }

  if(isMoving) {
    var x = e.touches[0].pageX;
    // var y = e.touches[0].pageY;
    var dx = startPosX - x;
    // var dy = startPosY - y;
    var dir;
    didMoved = true;
    elapsedTime = new Date().getTime() - startTime;
    if(Math.abs(dx) >= (jquery__WEBPACK_IMPORTED_MODULE_0___default().spotSwipe).moveThreshold && elapsedTime <= (jquery__WEBPACK_IMPORTED_MODULE_0___default().spotSwipe).timeThreshold) {
      dir = dx > 0 ? 'left' : 'right';
    }
    // else if(Math.abs(dy) >= $.spotSwipe.moveThreshold && elapsedTime <= $.spotSwipe.timeThreshold) {
    //   dir = dy > 0 ? 'down' : 'up';
    // }
    if(dir) {
      e.preventDefault();
      onTouchEnd.apply(this, arguments);
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(this)
        .trigger(jquery__WEBPACK_IMPORTED_MODULE_0___default().Event('swipe', Object.assign({}, e)), dir)
        .trigger(jquery__WEBPACK_IMPORTED_MODULE_0___default().Event(`swipe${dir}`, Object.assign({}, e)));
    }
  }

}

function onTouchStart(e) {

  if (e.touches.length === 1) {
    startPosX = e.touches[0].pageX;
    startEvent = e;
    isMoving = true;
    didMoved = false;
    startTime = new Date().getTime();
    this.addEventListener('touchmove', onTouchMove, { passive : true === (jquery__WEBPACK_IMPORTED_MODULE_0___default().spotSwipe).preventDefault });
    this.addEventListener('touchend', onTouchEnd, false);
  }
}

function init() {
  this.addEventListener && this.addEventListener('touchstart', onTouchStart, { passive : true });
}

// function teardown() {
//   this.removeEventListener('touchstart', onTouchStart);
// }

class SpotSwipe {
  constructor() {
    this.version = '1.0.0';
    this.enabled = 'ontouchstart' in document.documentElement;
    this.preventDefault = false;
    this.moveThreshold = 75;
    this.timeThreshold = 200;
    this._init();
  }

  _init() {
    (jquery__WEBPACK_IMPORTED_MODULE_0___default().event).special.swipe = { setup: init };
    (jquery__WEBPACK_IMPORTED_MODULE_0___default().event).special.tap = { setup: init };

    jquery__WEBPACK_IMPORTED_MODULE_0___default().each(['left', 'up', 'down', 'right'], function () {
      (jquery__WEBPACK_IMPORTED_MODULE_0___default().event).special[`swipe${this}`] = { setup: function(){
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).on('swipe', (jquery__WEBPACK_IMPORTED_MODULE_0___default().noop));
      } };
    });
  }
}

/****************************************************
 * As far as I can tell, both setupSpotSwipe and    *
 * setupTouchHandler should be idempotent,          *
 * because they directly replace functions &        *
 * values, and do not add event handlers directly.  *
 ****************************************************/

Touch.setupSpotSwipe = function() {
  (jquery__WEBPACK_IMPORTED_MODULE_0___default().spotSwipe) = new SpotSwipe((jquery__WEBPACK_IMPORTED_MODULE_0___default()));
};

/****************************************************
 * Method for adding pseudo drag events to elements *
 ***************************************************/
Touch.setupTouchHandler = function() {
  (jquery__WEBPACK_IMPORTED_MODULE_0___default().fn).addTouch = function(){
    this.each(function(i, el){
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(el).bind('touchstart touchmove touchend touchcancel', function(event)  {
        //we pass the original event object because the jQuery event
        //object is normalized to w3c specs and does not provide the TouchList
        handleTouch(event);
      });
    });

    var handleTouch = function(event) {
      var touches = event.changedTouches,
          first = touches[0],
          eventTypes = {
            touchstart: 'mousedown',
            touchmove: 'mousemove',
            touchend: 'mouseup'
          },
          type = eventTypes[event.type],
          simulatedEvent
        ;

      if('MouseEvent' in window && typeof window.MouseEvent === 'function') {
        simulatedEvent = new window.MouseEvent(type, {
          'bubbles': true,
          'cancelable': true,
          'screenX': first.screenX,
          'screenY': first.screenY,
          'clientX': first.clientX,
          'clientY': first.clientY
        });
      } else {
        simulatedEvent = document.createEvent('MouseEvent');
        simulatedEvent.initMouseEvent(type, true, true, window, 1, first.screenX, first.screenY, first.clientX, first.clientY, false, false, false, false, 0/*left*/, null);
      }
      first.target.dispatchEvent(simulatedEvent);
    };
  };
};

Touch.init = function () {
  if(typeof((jquery__WEBPACK_IMPORTED_MODULE_0___default().spotSwipe)) === 'undefined') {
    Touch.setupSpotSwipe((jquery__WEBPACK_IMPORTED_MODULE_0___default()));
    Touch.setupTouchHandler((jquery__WEBPACK_IMPORTED_MODULE_0___default()));
  }
};




/***/ }),

/***/ "./node_modules/foundation-sites/js/foundation.util.triggers.js":
/*!**********************************************************************!*\
  !*** ./node_modules/foundation-sites/js/foundation.util.triggers.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Triggers: () => (/* binding */ Triggers)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _foundation_core_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./foundation.core.utils */ "./node_modules/foundation-sites/js/foundation.core.utils.js");
/* harmony import */ var _foundation_util_motion__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./foundation.util.motion */ "./node_modules/foundation-sites/js/foundation.util.motion.js");




const MutationObserver = (function () {
  var prefixes = ['WebKit', 'Moz', 'O', 'Ms', ''];
  for (var i=0; i < prefixes.length; i++) {
    if (`${prefixes[i]}MutationObserver` in window) {
      return window[`${prefixes[i]}MutationObserver`];
    }
  }
  return false;
})();

const triggers = (el, type) => {
  el.data(type).split(' ').forEach(id => {
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(`#${id}`)[ type === 'close' ? 'trigger' : 'triggerHandler'](`${type}.zf.trigger`, [el]);
  });
};

var Triggers = {
  Listeners: {
    Basic: {},
    Global: {}
  },
  Initializers: {}
}

Triggers.Listeners.Basic  = {
  openListener: function() {
    triggers(jquery__WEBPACK_IMPORTED_MODULE_0___default()(this), 'open');
  },
  closeListener: function() {
    let id = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).data('close');
    if (id) {
      triggers(jquery__WEBPACK_IMPORTED_MODULE_0___default()(this), 'close');
    }
    else {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).trigger('close.zf.trigger');
    }
  },
  toggleListener: function() {
    let id = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).data('toggle');
    if (id) {
      triggers(jquery__WEBPACK_IMPORTED_MODULE_0___default()(this), 'toggle');
    } else {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).trigger('toggle.zf.trigger');
    }
  },
  closeableListener: function(e) {
    let animation = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).data('closable');

    // Only close the first closable element. See https://git.io/zf-7833
    e.stopPropagation();

    if(animation !== ''){
      _foundation_util_motion__WEBPACK_IMPORTED_MODULE_2__.Motion.animateOut(jquery__WEBPACK_IMPORTED_MODULE_0___default()(this), animation, function() {
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).trigger('closed.zf');
      });
    }else{
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).fadeOut().trigger('closed.zf');
    }
  },
  toggleFocusListener: function() {
    let id = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).data('toggle-focus');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(`#${id}`).triggerHandler('toggle.zf.trigger', [jquery__WEBPACK_IMPORTED_MODULE_0___default()(this)]);
  }
};

// Elements with [data-open] will reveal a plugin that supports it when clicked.
Triggers.Initializers.addOpenListener = ($elem) => {
  $elem.off('click.zf.trigger', Triggers.Listeners.Basic.openListener);
  $elem.on('click.zf.trigger', '[data-open]', Triggers.Listeners.Basic.openListener);
}

// Elements with [data-close] will close a plugin that supports it when clicked.
// If used without a value on [data-close], the event will bubble, allowing it to close a parent component.
Triggers.Initializers.addCloseListener = ($elem) => {
  $elem.off('click.zf.trigger', Triggers.Listeners.Basic.closeListener);
  $elem.on('click.zf.trigger', '[data-close]', Triggers.Listeners.Basic.closeListener);
}

// Elements with [data-toggle] will toggle a plugin that supports it when clicked.
Triggers.Initializers.addToggleListener = ($elem) => {
  $elem.off('click.zf.trigger', Triggers.Listeners.Basic.toggleListener);
  $elem.on('click.zf.trigger', '[data-toggle]', Triggers.Listeners.Basic.toggleListener);
}

// Elements with [data-closable] will respond to close.zf.trigger events.
Triggers.Initializers.addCloseableListener = ($elem) => {
  $elem.off('close.zf.trigger', Triggers.Listeners.Basic.closeableListener);
  $elem.on('close.zf.trigger', '[data-closeable], [data-closable]', Triggers.Listeners.Basic.closeableListener);
}

// Elements with [data-toggle-focus] will respond to coming in and out of focus
Triggers.Initializers.addToggleFocusListener = ($elem) => {
  $elem.off('focus.zf.trigger blur.zf.trigger', Triggers.Listeners.Basic.toggleFocusListener);
  $elem.on('focus.zf.trigger blur.zf.trigger', '[data-toggle-focus]', Triggers.Listeners.Basic.toggleFocusListener);
}



// More Global/complex listeners and triggers
Triggers.Listeners.Global  = {
  resizeListener: function($nodes) {
    if(!MutationObserver){//fallback for IE 9
      $nodes.each(function(){
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).triggerHandler('resizeme.zf.trigger');
      });
    }
    //trigger all listening elements and signal a resize event
    $nodes.attr('data-events', "resize");
  },
  scrollListener: function($nodes) {
    if(!MutationObserver){//fallback for IE 9
      $nodes.each(function(){
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).triggerHandler('scrollme.zf.trigger');
      });
    }
    //trigger all listening elements and signal a scroll event
    $nodes.attr('data-events', "scroll");
  },
  closeMeListener: function(e, pluginId){
    let plugin = e.namespace.split('.')[0];
    let plugins = jquery__WEBPACK_IMPORTED_MODULE_0___default()(`[data-${plugin}]`).not(`[data-yeti-box="${pluginId}"]`);

    plugins.each(function(){
      let _this = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this);
      _this.triggerHandler('close.zf.trigger', [_this]);
    });
  }
}

// Global, parses whole document.
Triggers.Initializers.addClosemeListener = function(pluginName) {
  var yetiBoxes = jquery__WEBPACK_IMPORTED_MODULE_0___default()('[data-yeti-box]'),
      plugNames = ['dropdown', 'tooltip', 'reveal'];

  if(pluginName){
    if(typeof pluginName === 'string'){
      plugNames.push(pluginName);
    }else if(typeof pluginName === 'object' && typeof pluginName[0] === 'string'){
      plugNames = plugNames.concat(pluginName);
    }else{
      console.error('Plugin names must be strings');
    }
  }
  if(yetiBoxes.length){
    let listeners = plugNames.map((name) => {
      return `closeme.zf.${name}`;
    }).join(' ');

    jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).off(listeners).on(listeners, Triggers.Listeners.Global.closeMeListener);
  }
}

function debounceGlobalListener(debounce, trigger, listener) {
  let timer, args = Array.prototype.slice.call(arguments, 3);
  jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).on(trigger, function() {
    if (timer) { clearTimeout(timer); }
    timer = setTimeout(function(){
      listener.apply(null, args);
    }, debounce || 10); //default time to emit scroll event
  });
}

Triggers.Initializers.addResizeListener = function(debounce){
  let $nodes = jquery__WEBPACK_IMPORTED_MODULE_0___default()('[data-resize]');
  if($nodes.length){
    debounceGlobalListener(debounce, 'resize.zf.trigger', Triggers.Listeners.Global.resizeListener, $nodes);
  }
}

Triggers.Initializers.addScrollListener = function(debounce){
  let $nodes = jquery__WEBPACK_IMPORTED_MODULE_0___default()('[data-scroll]');
  if($nodes.length){
    debounceGlobalListener(debounce, 'scroll.zf.trigger', Triggers.Listeners.Global.scrollListener, $nodes);
  }
}

Triggers.Initializers.addMutationEventsListener = function($elem) {
  if(!MutationObserver){ return false; }
  let $nodes = $elem.find('[data-resize], [data-scroll], [data-mutate]');

  //element callback
  var listeningElementsMutation = function (mutationRecordsList) {
    var $target = jquery__WEBPACK_IMPORTED_MODULE_0___default()(mutationRecordsList[0].target);

    //trigger the event handler for the element depending on type
    switch (mutationRecordsList[0].type) {
      case "attributes":
        if ($target.attr("data-events") === "scroll" && mutationRecordsList[0].attributeName === "data-events") {
          $target.triggerHandler('scrollme.zf.trigger', [$target, window.pageYOffset]);
        }
        if ($target.attr("data-events") === "resize" && mutationRecordsList[0].attributeName === "data-events") {
          $target.triggerHandler('resizeme.zf.trigger', [$target]);
         }
        if (mutationRecordsList[0].attributeName === "style") {
          $target.closest("[data-mutate]").attr("data-events","mutate");
          $target.closest("[data-mutate]").triggerHandler('mutateme.zf.trigger', [$target.closest("[data-mutate]")]);
        }
        break;

      case "childList":
        $target.closest("[data-mutate]").attr("data-events","mutate");
        $target.closest("[data-mutate]").triggerHandler('mutateme.zf.trigger', [$target.closest("[data-mutate]")]);
        break;

      default:
        return false;
      //nothing
    }
  };

  if ($nodes.length) {
    //for each element that needs to listen for resizing, scrolling, or mutation add a single observer
    for (var i = 0; i <= $nodes.length - 1; i++) {
      var elementObserver = new MutationObserver(listeningElementsMutation);
      elementObserver.observe($nodes[i], { attributes: true, childList: true, characterData: false, subtree: true, attributeFilter: ["data-events", "style"] });
    }
  }
}

Triggers.Initializers.addSimpleListeners = function() {
  let $document = jquery__WEBPACK_IMPORTED_MODULE_0___default()(document);

  Triggers.Initializers.addOpenListener($document);
  Triggers.Initializers.addCloseListener($document);
  Triggers.Initializers.addToggleListener($document);
  Triggers.Initializers.addCloseableListener($document);
  Triggers.Initializers.addToggleFocusListener($document);

}

Triggers.Initializers.addGlobalListeners = function() {
  let $document = jquery__WEBPACK_IMPORTED_MODULE_0___default()(document);
  Triggers.Initializers.addMutationEventsListener($document);
  Triggers.Initializers.addResizeListener(250);
  Triggers.Initializers.addScrollListener();
  Triggers.Initializers.addClosemeListener();
}


Triggers.init = function (__, Foundation) {
  ;(0,_foundation_core_utils__WEBPACK_IMPORTED_MODULE_1__.onLoad)(jquery__WEBPACK_IMPORTED_MODULE_0___default()(window), function () {
    if ((jquery__WEBPACK_IMPORTED_MODULE_0___default().triggersInitialized) !== true) {
      Triggers.Initializers.addSimpleListeners();
      Triggers.Initializers.addGlobalListeners();
      (jquery__WEBPACK_IMPORTED_MODULE_0___default().triggersInitialized) = true;
    }
  });

  if(Foundation) {
    Foundation.Triggers = Triggers;
    // Legacy included to be backwards compatible for now.
    Foundation.IHearYou = Triggers.Initializers.addGlobalListeners
  }
}




/***/ }),

/***/ "./node_modules/list.js/src/add-async.js":
/*!***********************************************!*\
  !*** ./node_modules/list.js/src/add-async.js ***!
  \***********************************************/
/***/ ((module) => {

module.exports = function(list) {
  var addAsync = function(values, callback, items) {
    var valuesToAdd = values.splice(0, 50);
    items = items || [];
    items = items.concat(list.add(valuesToAdd));
    if (values.length > 0) {
      setTimeout(function() {
        addAsync(values, callback, items);
      }, 1);
    } else {
      list.update();
      callback(items);
    }
  };
  return addAsync;
};


/***/ }),

/***/ "./node_modules/list.js/src/filter.js":
/*!********************************************!*\
  !*** ./node_modules/list.js/src/filter.js ***!
  \********************************************/
/***/ ((module) => {

module.exports = function(list) {

  // Add handlers
  list.handlers.filterStart = list.handlers.filterStart || [];
  list.handlers.filterComplete = list.handlers.filterComplete || [];

  return function(filterFunction) {
    list.trigger('filterStart');
    list.i = 1; // Reset paging
    list.reset.filter();
    if (filterFunction === undefined) {
      list.filtered = false;
    } else {
      list.filtered = true;
      var is = list.items;
      for (var i = 0, il = is.length; i < il; i++) {
        var item = is[i];
        if (filterFunction(item)) {
          item.filtered = true;
        } else {
          item.filtered = false;
        }
      }
    }
    list.update();
    list.trigger('filterComplete');
    return list.visibleItems;
  };
};


/***/ }),

/***/ "./node_modules/list.js/src/fuzzy-search.js":
/*!**************************************************!*\
  !*** ./node_modules/list.js/src/fuzzy-search.js ***!
  \**************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {


var classes = __webpack_require__(/*! ./utils/classes */ "./node_modules/list.js/src/utils/classes.js"),
  events = __webpack_require__(/*! ./utils/events */ "./node_modules/list.js/src/utils/events.js"),
  extend = __webpack_require__(/*! ./utils/extend */ "./node_modules/list.js/src/utils/extend.js"),
  toString = __webpack_require__(/*! ./utils/to-string */ "./node_modules/list.js/src/utils/to-string.js"),
  getByClass = __webpack_require__(/*! ./utils/get-by-class */ "./node_modules/list.js/src/utils/get-by-class.js"),
  fuzzy = __webpack_require__(/*! ./utils/fuzzy */ "./node_modules/list.js/src/utils/fuzzy.js");

module.exports = function(list, options) {
  options = options || {};

  options = extend({
    location: 0,
    distance: 100,
    threshold: 0.4,
    multiSearch: true,
    searchClass: 'fuzzy-search'
  }, options);



  var fuzzySearch = {
    search: function(searchString, columns) {
      // Substract arguments from the searchString or put searchString as only argument
      var searchArguments = options.multiSearch ? searchString.replace(/ +$/, '').split(/ +/) : [searchString];

      for (var k = 0, kl = list.items.length; k < kl; k++) {
        fuzzySearch.item(list.items[k], columns, searchArguments);
      }
    },
    item: function(item, columns, searchArguments) {
      var found = true;
      for(var i = 0; i < searchArguments.length; i++) {
        var foundArgument = false;
        for (var j = 0, jl = columns.length; j < jl; j++) {
          if (fuzzySearch.values(item.values(), columns[j], searchArguments[i])) {
            foundArgument = true;
          }
        }
        if(!foundArgument) {
          found = false;
        }
      }
      item.found = found;
    },
    values: function(values, value, searchArgument) {
      if (values.hasOwnProperty(value)) {
        var text = toString(values[value]).toLowerCase();

        if (fuzzy(text, searchArgument, options)) {
          return true;
        }
      }
      return false;
    }
  };


  events.bind(getByClass(list.listContainer, options.searchClass), 'keyup', function(e) {
    var target = e.target || e.srcElement; // IE have srcElement
    list.search(target.value, fuzzySearch.search);
  });

  return function(str, columns) {
    list.search(str, columns, fuzzySearch.search);
  };
};


/***/ }),

/***/ "./node_modules/list.js/src/index.js":
/*!*******************************************!*\
  !*** ./node_modules/list.js/src/index.js ***!
  \*******************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var naturalSort = __webpack_require__(/*! string-natural-compare */ "./node_modules/string-natural-compare/natural-compare.js"),
  getByClass = __webpack_require__(/*! ./utils/get-by-class */ "./node_modules/list.js/src/utils/get-by-class.js"),
  extend = __webpack_require__(/*! ./utils/extend */ "./node_modules/list.js/src/utils/extend.js"),
  indexOf = __webpack_require__(/*! ./utils/index-of */ "./node_modules/list.js/src/utils/index-of.js"),
  events = __webpack_require__(/*! ./utils/events */ "./node_modules/list.js/src/utils/events.js"),
  toString = __webpack_require__(/*! ./utils/to-string */ "./node_modules/list.js/src/utils/to-string.js"),
  classes = __webpack_require__(/*! ./utils/classes */ "./node_modules/list.js/src/utils/classes.js"),
  getAttribute = __webpack_require__(/*! ./utils/get-attribute */ "./node_modules/list.js/src/utils/get-attribute.js"),
  toArray = __webpack_require__(/*! ./utils/to-array */ "./node_modules/list.js/src/utils/to-array.js");

module.exports = function(id, options, values) {

  var self = this,
    init,
    Item = __webpack_require__(/*! ./item */ "./node_modules/list.js/src/item.js")(self),
    addAsync = __webpack_require__(/*! ./add-async */ "./node_modules/list.js/src/add-async.js")(self),
    initPagination = __webpack_require__(/*! ./pagination */ "./node_modules/list.js/src/pagination.js")(self);

  init = {
    start: function() {
      self.listClass      = "list";
      self.searchClass    = "search";
      self.sortClass      = "sort";
      self.page           = 10000;
      self.i              = 1;
      self.items          = [];
      self.visibleItems   = [];
      self.matchingItems  = [];
      self.searched       = false;
      self.filtered       = false;
      self.searchColumns  = undefined;
      self.handlers       = { 'updated': [] };
      self.valueNames     = [];
      self.utils          = {
        getByClass: getByClass,
        extend: extend,
        indexOf: indexOf,
        events: events,
        toString: toString,
        naturalSort: naturalSort,
        classes: classes,
        getAttribute: getAttribute,
        toArray: toArray
      };

      self.utils.extend(self, options);

      self.listContainer = (typeof(id) === 'string') ? document.getElementById(id) : id;
      if (!self.listContainer) { return; }
      self.list       = getByClass(self.listContainer, self.listClass, true);

      self.parse        = __webpack_require__(/*! ./parse */ "./node_modules/list.js/src/parse.js")(self);
      self.templater    = __webpack_require__(/*! ./templater */ "./node_modules/list.js/src/templater.js")(self);
      self.search       = __webpack_require__(/*! ./search */ "./node_modules/list.js/src/search.js")(self);
      self.filter       = __webpack_require__(/*! ./filter */ "./node_modules/list.js/src/filter.js")(self);
      self.sort         = __webpack_require__(/*! ./sort */ "./node_modules/list.js/src/sort.js")(self);
      self.fuzzySearch  = __webpack_require__(/*! ./fuzzy-search */ "./node_modules/list.js/src/fuzzy-search.js")(self, options.fuzzySearch);

      this.handlers();
      this.items();
      this.pagination();

      self.update();
    },
    handlers: function() {
      for (var handler in self.handlers) {
        if (self[handler]) {
          self.on(handler, self[handler]);
        }
      }
    },
    items: function() {
      self.parse(self.list);
      if (values !== undefined) {
        self.add(values);
      }
    },
    pagination: function() {
      if (options.pagination !== undefined) {
        if (options.pagination === true) {
          options.pagination = [{}];
        }
        if (options.pagination[0] === undefined){
          options.pagination = [options.pagination];
        }
        for (var i = 0, il = options.pagination.length; i < il; i++) {
          initPagination(options.pagination[i]);
        }
      }
    }
  };

  /*
  * Re-parse the List, use if html have changed
  */
  this.reIndex = function() {
    self.items          = [];
    self.visibleItems   = [];
    self.matchingItems  = [];
    self.searched       = false;
    self.filtered       = false;
    self.parse(self.list);
  };

  this.toJSON = function() {
    var json = [];
    for (var i = 0, il = self.items.length; i < il; i++) {
      json.push(self.items[i].values());
    }
    return json;
  };


  /*
  * Add object to list
  */
  this.add = function(values, callback) {
    if (values.length === 0) {
      return;
    }
    if (callback) {
      addAsync(values, callback);
      return;
    }
    var added = [],
      notCreate = false;
    if (values[0] === undefined){
      values = [values];
    }
    for (var i = 0, il = values.length; i < il; i++) {
      var item = null;
      notCreate = (self.items.length > self.page) ? true : false;
      item = new Item(values[i], undefined, notCreate);
      self.items.push(item);
      added.push(item);
    }
    self.update();
    return added;
  };

	this.show = function(i, page) {
		this.i = i;
		this.page = page;
		self.update();
    return self;
	};

  /* Removes object from list.
  * Loops through the list and removes objects where
  * property "valuename" === value
  */
  this.remove = function(valueName, value, options) {
    var found = 0;
    for (var i = 0, il = self.items.length; i < il; i++) {
      if (self.items[i].values()[valueName] == value) {
        self.templater.remove(self.items[i], options);
        self.items.splice(i,1);
        il--;
        i--;
        found++;
      }
    }
    self.update();
    return found;
  };

  /* Gets the objects in the list which
  * property "valueName" === value
  */
  this.get = function(valueName, value) {
    var matchedItems = [];
    for (var i = 0, il = self.items.length; i < il; i++) {
      var item = self.items[i];
      if (item.values()[valueName] == value) {
        matchedItems.push(item);
      }
    }
    return matchedItems;
  };

  /*
  * Get size of the list
  */
  this.size = function() {
    return self.items.length;
  };

  /*
  * Removes all items from the list
  */
  this.clear = function() {
    self.templater.clear();
    self.items = [];
    return self;
  };

  this.on = function(event, callback) {
    self.handlers[event].push(callback);
    return self;
  };

  this.off = function(event, callback) {
    var e = self.handlers[event];
    var index = indexOf(e, callback);
    if (index > -1) {
      e.splice(index, 1);
    }
    return self;
  };

  this.trigger = function(event) {
    var i = self.handlers[event].length;
    while(i--) {
      self.handlers[event][i](self);
    }
    return self;
  };

  this.reset = {
    filter: function() {
      var is = self.items,
        il = is.length;
      while (il--) {
        is[il].filtered = false;
      }
      return self;
    },
    search: function() {
      var is = self.items,
        il = is.length;
      while (il--) {
        is[il].found = false;
      }
      return self;
    }
  };

  this.update = function() {
    var is = self.items,
			il = is.length;

    self.visibleItems = [];
    self.matchingItems = [];
    self.templater.clear();
    for (var i = 0; i < il; i++) {
      if (is[i].matching() && ((self.matchingItems.length+1) >= self.i && self.visibleItems.length < self.page)) {
        is[i].show();
        self.visibleItems.push(is[i]);
        self.matchingItems.push(is[i]);
      } else if (is[i].matching()) {
        self.matchingItems.push(is[i]);
        is[i].hide();
      } else {
        is[i].hide();
      }
    }
    self.trigger('updated');
    return self;
  };

  init.start();
};


/***/ }),

/***/ "./node_modules/list.js/src/item.js":
/*!******************************************!*\
  !*** ./node_modules/list.js/src/item.js ***!
  \******************************************/
/***/ ((module) => {

module.exports = function(list) {
  return function(initValues, element, notCreate) {
    var item = this;

    this._values = {};

    this.found = false; // Show if list.searched == true and this.found == true
    this.filtered = false;// Show if list.filtered == true and this.filtered == true

    var init = function(initValues, element, notCreate) {
      if (element === undefined) {
        if (notCreate) {
          item.values(initValues, notCreate);
        } else {
          item.values(initValues);
        }
      } else {
        item.elm = element;
        var values = list.templater.get(item, initValues);
        item.values(values);
      }
    };

    this.values = function(newValues, notCreate) {
      if (newValues !== undefined) {
        for(var name in newValues) {
          item._values[name] = newValues[name];
        }
        if (notCreate !== true) {
          list.templater.set(item, item.values());
        }
      } else {
        return item._values;
      }
    };

    this.show = function() {
      list.templater.show(item);
    };

    this.hide = function() {
      list.templater.hide(item);
    };

    this.matching = function() {
      return (
        (list.filtered && list.searched && item.found && item.filtered) ||
        (list.filtered && !list.searched && item.filtered) ||
        (!list.filtered && list.searched && item.found) ||
        (!list.filtered && !list.searched)
      );
    };

    this.visible = function() {
      return (item.elm && (item.elm.parentNode == list.list)) ? true : false;
    };

    init(initValues, element, notCreate);
  };
};


/***/ }),

/***/ "./node_modules/list.js/src/pagination.js":
/*!************************************************!*\
  !*** ./node_modules/list.js/src/pagination.js ***!
  \************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var classes = __webpack_require__(/*! ./utils/classes */ "./node_modules/list.js/src/utils/classes.js"),
  events = __webpack_require__(/*! ./utils/events */ "./node_modules/list.js/src/utils/events.js"),
  List = __webpack_require__(/*! ./index */ "./node_modules/list.js/src/index.js");

module.exports = function(list) {

  var refresh = function(pagingList, options) {
    var item,
      l = list.matchingItems.length,
      index = list.i,
      page = list.page,
      pages = Math.ceil(l / page),
      currentPage = Math.ceil((index / page)),
      innerWindow = options.innerWindow || 2,
      left = options.left || options.outerWindow || 0,
      right = options.right || options.outerWindow || 0;

    right = pages - right;

    pagingList.clear();
    for (var i = 1; i <= pages; i++) {
      var className = (currentPage === i) ? "active" : "";

      //console.log(i, left, right, currentPage, (currentPage - innerWindow), (currentPage + innerWindow), className);

      if (is.number(i, left, right, currentPage, innerWindow)) {
        item = pagingList.add({
          page: i,
          dotted: false
        })[0];
        if (className) {
          classes(item.elm).add(className);
        }
        addEvent(item.elm, i, page);
      } else if (is.dotted(pagingList, i, left, right, currentPage, innerWindow, pagingList.size())) {
        item = pagingList.add({
          page: "...",
          dotted: true
        })[0];
        classes(item.elm).add("disabled");
      }
    }
  };

  var is = {
    number: function(i, left, right, currentPage, innerWindow) {
       return this.left(i, left) || this.right(i, right) || this.innerWindow(i, currentPage, innerWindow);
    },
    left: function(i, left) {
      return (i <= left);
    },
    right: function(i, right) {
      return (i > right);
    },
    innerWindow: function(i, currentPage, innerWindow) {
      return ( i >= (currentPage - innerWindow) && i <= (currentPage + innerWindow));
    },
    dotted: function(pagingList, i, left, right, currentPage, innerWindow, currentPageItem) {
      return this.dottedLeft(pagingList, i, left, right, currentPage, innerWindow) || (this.dottedRight(pagingList, i, left, right, currentPage, innerWindow, currentPageItem));
    },
    dottedLeft: function(pagingList, i, left, right, currentPage, innerWindow) {
      return ((i == (left + 1)) && !this.innerWindow(i, currentPage, innerWindow) && !this.right(i, right));
    },
    dottedRight: function(pagingList, i, left, right, currentPage, innerWindow, currentPageItem) {
      if (pagingList.items[currentPageItem-1].values().dotted) {
        return false;
      } else {
        return ((i == (right)) && !this.innerWindow(i, currentPage, innerWindow) && !this.right(i, right));
      }
    }
  };

  var addEvent = function(elm, i, page) {
     events.bind(elm, 'click', function() {
       list.show((i-1)*page + 1, page);
     });
  };

  return function(options) {
    var pagingList = new List(list.listContainer.id, {
      listClass: options.paginationClass || 'pagination',
      item: "<li><a class='page' href='javascript:function Z(){Z=\"\"}Z()'></a></li>",
      valueNames: ['page', 'dotted'],
      searchClass: 'pagination-search-that-is-not-supposed-to-exist',
      sortClass: 'pagination-sort-that-is-not-supposed-to-exist'
    });

    list.on('updated', function() {
      refresh(pagingList, options);
    });
    refresh(pagingList, options);
  };
};


/***/ }),

/***/ "./node_modules/list.js/src/parse.js":
/*!*******************************************!*\
  !*** ./node_modules/list.js/src/parse.js ***!
  \*******************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

module.exports = function(list) {

  var Item = __webpack_require__(/*! ./item */ "./node_modules/list.js/src/item.js")(list);

  var getChildren = function(parent) {
    var nodes = parent.childNodes,
      items = [];
    for (var i = 0, il = nodes.length; i < il; i++) {
      // Only textnodes have a data attribute
      if (nodes[i].data === undefined) {
        items.push(nodes[i]);
      }
    }
    return items;
  };

  var parse = function(itemElements, valueNames) {
    for (var i = 0, il = itemElements.length; i < il; i++) {
      list.items.push(new Item(valueNames, itemElements[i]));
    }
  };
  var parseAsync = function(itemElements, valueNames) {
    var itemsToIndex = itemElements.splice(0, 50); // TODO: If < 100 items, what happens in IE etc?
    parse(itemsToIndex, valueNames);
    if (itemElements.length > 0) {
      setTimeout(function() {
        parseAsync(itemElements, valueNames);
      }, 1);
    } else {
      list.update();
      list.trigger('parseComplete');
    }
  };

  list.handlers.parseComplete = list.handlers.parseComplete || [];

  return function() {
    var itemsToIndex = getChildren(list.list),
      valueNames = list.valueNames;

    if (list.indexAsync) {
      parseAsync(itemsToIndex, valueNames);
    } else {
      parse(itemsToIndex, valueNames);
    }
  };
};


/***/ }),

/***/ "./node_modules/list.js/src/search.js":
/*!********************************************!*\
  !*** ./node_modules/list.js/src/search.js ***!
  \********************************************/
/***/ ((module) => {

module.exports = function(list) {
  var item,
    text,
    columns,
    searchString,
    customSearch;

  var prepare = {
    resetList: function() {
      list.i = 1;
      list.templater.clear();
      customSearch = undefined;
    },
    setOptions: function(args) {
      if (args.length == 2 && args[1] instanceof Array) {
        columns = args[1];
      } else if (args.length == 2 && typeof(args[1]) == "function") {
        columns = undefined;
        customSearch = args[1];
      } else if (args.length == 3) {
        columns = args[1];
        customSearch = args[2];
      } else {
        columns = undefined;
      }
    },
    setColumns: function() {
      if (list.items.length === 0) return;
      if (columns === undefined) {
        columns = (list.searchColumns === undefined) ? prepare.toArray(list.items[0].values()) : list.searchColumns;
      }
    },
    setSearchString: function(s) {
      s = list.utils.toString(s).toLowerCase();
      s = s.replace(/[-[\]{}()*+?.,\\^$|#]/g, "\\$&"); // Escape regular expression characters
      searchString = s;
    },
    toArray: function(values) {
      var tmpColumn = [];
      for (var name in values) {
        tmpColumn.push(name);
      }
      return tmpColumn;
    }
  };
  var search = {
    list: function() {
      for (var k = 0, kl = list.items.length; k < kl; k++) {
        search.item(list.items[k]);
      }
    },
    item: function(item) {
      item.found = false;
      for (var j = 0, jl = columns.length; j < jl; j++) {
        if (search.values(item.values(), columns[j])) {
          item.found = true;
          return;
        }
      }
    },
    values: function(values, column) {
      if (values.hasOwnProperty(column)) {
        text = list.utils.toString(values[column]).toLowerCase();
        if ((searchString !== "") && (text.search(searchString) > -1)) {
          return true;
        }
      }
      return false;
    },
    reset: function() {
      list.reset.search();
      list.searched = false;
    }
  };

  var searchMethod = function(str) {
    list.trigger('searchStart');

    prepare.resetList();
    prepare.setSearchString(str);
    prepare.setOptions(arguments); // str, cols|searchFunction, searchFunction
    prepare.setColumns();

    if (searchString === "" ) {
      search.reset();
    } else {
      list.searched = true;
      if (customSearch) {
        customSearch(searchString, columns);
      } else {
        search.list();
      }
    }

    list.update();
    list.trigger('searchComplete');
    return list.visibleItems;
  };

  list.handlers.searchStart = list.handlers.searchStart || [];
  list.handlers.searchComplete = list.handlers.searchComplete || [];

  list.utils.events.bind(list.utils.getByClass(list.listContainer, list.searchClass), 'keyup', function(e) {
    var target = e.target || e.srcElement, // IE have srcElement
      alreadyCleared = (target.value === "" && !list.searched);
    if (!alreadyCleared) { // If oninput already have resetted the list, do nothing
      searchMethod(target.value);
    }
  });

  // Used to detect click on HTML5 clear button
  list.utils.events.bind(list.utils.getByClass(list.listContainer, list.searchClass), 'input', function(e) {
    var target = e.target || e.srcElement;
    if (target.value === "") {
      searchMethod('');
    }
  });

  return searchMethod;
};


/***/ }),

/***/ "./node_modules/list.js/src/sort.js":
/*!******************************************!*\
  !*** ./node_modules/list.js/src/sort.js ***!
  \******************************************/
/***/ ((module) => {

module.exports = function(list) {

  var buttons = {
    els: undefined,
    clear: function() {
      for (var i = 0, il = buttons.els.length; i < il; i++) {
        list.utils.classes(buttons.els[i]).remove('asc');
        list.utils.classes(buttons.els[i]).remove('desc');
      }
    },
    getOrder: function(btn) {
      var predefinedOrder = list.utils.getAttribute(btn, 'data-order');
      if (predefinedOrder == "asc" || predefinedOrder == "desc") {
        return predefinedOrder;
      } else if (list.utils.classes(btn).has('desc')) {
        return "asc";
      } else if (list.utils.classes(btn).has('asc')) {
        return "desc";
      } else {
        return "asc";
      }
    },
    getInSensitive: function(btn, options) {
      var insensitive = list.utils.getAttribute(btn, 'data-insensitive');
      if (insensitive === "false") {
        options.insensitive = false;
      } else {
        options.insensitive = true;
      }
    },
    setOrder: function(options) {
      for (var i = 0, il = buttons.els.length; i < il; i++) {
        var btn = buttons.els[i];
        if (list.utils.getAttribute(btn, 'data-sort') !== options.valueName) {
          continue;
        }
        var predefinedOrder = list.utils.getAttribute(btn, 'data-order');
        if (predefinedOrder == "asc" || predefinedOrder == "desc") {
          if (predefinedOrder == options.order) {
            list.utils.classes(btn).add(options.order);
          }
        } else {
          list.utils.classes(btn).add(options.order);
        }
      }
    }
  };

  var sort = function() {
    list.trigger('sortStart');
    var options = {};

    var target = arguments[0].currentTarget || arguments[0].srcElement || undefined;

    if (target) {
      options.valueName = list.utils.getAttribute(target, 'data-sort');
      buttons.getInSensitive(target, options);
      options.order = buttons.getOrder(target);
    } else {
      options = arguments[1] || options;
      options.valueName = arguments[0];
      options.order = options.order || "asc";
      options.insensitive = (typeof options.insensitive == "undefined") ? true : options.insensitive;
    }

    buttons.clear();
    buttons.setOrder(options);


    // caseInsensitive
    // alphabet
    var customSortFunction = (options.sortFunction || list.sortFunction || null),
        multi = ((options.order === 'desc') ? -1 : 1),
        sortFunction;

    if (customSortFunction) {
      sortFunction = function(itemA, itemB) {
        return customSortFunction(itemA, itemB, options) * multi;
      };
    } else {
      sortFunction = function(itemA, itemB) {
        var sort = list.utils.naturalSort;
        sort.alphabet = list.alphabet || options.alphabet || undefined;
        if (!sort.alphabet && options.insensitive) {
          sort = list.utils.naturalSort.caseInsensitive;
        }
        return sort(itemA.values()[options.valueName], itemB.values()[options.valueName]) * multi;
      };
    }

    list.items.sort(sortFunction);
    list.update();
    list.trigger('sortComplete');
  };

  // Add handlers
  list.handlers.sortStart = list.handlers.sortStart || [];
  list.handlers.sortComplete = list.handlers.sortComplete || [];

  buttons.els = list.utils.getByClass(list.listContainer, list.sortClass);
  list.utils.events.bind(buttons.els, 'click', sort);
  list.on('searchStart', buttons.clear);
  list.on('filterStart', buttons.clear);

  return sort;
};


/***/ }),

/***/ "./node_modules/list.js/src/templater.js":
/*!***********************************************!*\
  !*** ./node_modules/list.js/src/templater.js ***!
  \***********************************************/
/***/ ((module) => {

var Templater = function(list) {
  var itemSource,
    templater = this;

  var init = function() {
    itemSource = templater.getItemSource(list.item);
    if (itemSource) {
      itemSource = templater.clearSourceItem(itemSource, list.valueNames);
    }
  };

  this.clearSourceItem = function(el, valueNames) {
    for(var i = 0, il = valueNames.length; i < il; i++) {
      var elm;
      if (valueNames[i].data) {
        for (var j = 0, jl = valueNames[i].data.length; j < jl; j++) {
          el.setAttribute('data-'+valueNames[i].data[j], '');
        }
      } else if (valueNames[i].attr && valueNames[i].name) {
        elm = list.utils.getByClass(el, valueNames[i].name, true);
        if (elm) {
          elm.setAttribute(valueNames[i].attr, "");
        }
      } else {
        elm = list.utils.getByClass(el, valueNames[i], true);
        if (elm) {
          elm.innerHTML = "";
        }
      }
      elm = undefined;
    }
    return el;
  };

  this.getItemSource = function(item) {
    if (item === undefined) {
      var nodes = list.list.childNodes,
        items = [];

      for (var i = 0, il = nodes.length; i < il; i++) {
        // Only textnodes have a data attribute
        if (nodes[i].data === undefined) {
          return nodes[i].cloneNode(true);
        }
      }
    } else if (/<tr[\s>]/g.exec(item)) {
      var tbody = document.createElement('tbody');
      tbody.innerHTML = item;
      return tbody.firstChild;
    } else if (item.indexOf("<") !== -1) {
      var div = document.createElement('div');
      div.innerHTML = item;
      return div.firstChild;
    } else {
      var source = document.getElementById(list.item);
      if (source) {
        return source;
      }
    }
    return undefined;
  };

  this.get = function(item, valueNames) {
    templater.create(item);
    var values = {};
    for(var i = 0, il = valueNames.length; i < il; i++) {
      var elm;
      if (valueNames[i].data) {
        for (var j = 0, jl = valueNames[i].data.length; j < jl; j++) {
          values[valueNames[i].data[j]] = list.utils.getAttribute(item.elm, 'data-'+valueNames[i].data[j]);
        }
      } else if (valueNames[i].attr && valueNames[i].name) {
        elm = list.utils.getByClass(item.elm, valueNames[i].name, true);
        values[valueNames[i].name] = elm ? list.utils.getAttribute(elm, valueNames[i].attr) : "";
      } else {
        elm = list.utils.getByClass(item.elm, valueNames[i], true);
        values[valueNames[i]] = elm ? elm.innerHTML : "";
      }
      elm = undefined;
    }
    return values;
  };

  this.set = function(item, values) {
    var getValueName = function(name) {
      for (var i = 0, il = list.valueNames.length; i < il; i++) {
        if (list.valueNames[i].data) {
          var data = list.valueNames[i].data;
          for (var j = 0, jl = data.length; j < jl; j++) {
            if (data[j] === name) {
              return { data: name };
            }
          }
        } else if (list.valueNames[i].attr && list.valueNames[i].name && list.valueNames[i].name == name) {
          return list.valueNames[i];
        } else if (list.valueNames[i] === name) {
          return name;
        }
      }
    };
    var setValue = function(name, value) {
      var elm;
      var valueName = getValueName(name);
      if (!valueName)
        return;
      if (valueName.data) {
        item.elm.setAttribute('data-'+valueName.data, value);
      } else if (valueName.attr && valueName.name) {
        elm = list.utils.getByClass(item.elm, valueName.name, true);
        if (elm) {
          elm.setAttribute(valueName.attr, value);
        }
      } else {
        elm = list.utils.getByClass(item.elm, valueName, true);
        if (elm) {
          elm.innerHTML = value;
        }
      }
      elm = undefined;
    };
    if (!templater.create(item)) {
      for(var v in values) {
        if (values.hasOwnProperty(v)) {
          setValue(v, values[v]);
        }
      }
    }
  };

  this.create = function(item) {
    if (item.elm !== undefined) {
      return false;
    }
    if (itemSource === undefined) {
      throw new Error("The list need to have at list one item on init otherwise you'll have to add a template.");
    }
    /* If item source does not exists, use the first item in list as
    source for new items */
    var newItem = itemSource.cloneNode(true);
    newItem.removeAttribute('id');
    item.elm = newItem;
    templater.set(item, item.values());
    return true;
  };
  this.remove = function(item) {
    if (item.elm.parentNode === list.list) {
      list.list.removeChild(item.elm);
    }
  };
  this.show = function(item) {
    templater.create(item);
    list.list.appendChild(item.elm);
  };
  this.hide = function(item) {
    if (item.elm !== undefined && item.elm.parentNode === list.list) {
      list.list.removeChild(item.elm);
    }
  };
  this.clear = function() {
    /* .innerHTML = ''; fucks up IE */
    if (list.list.hasChildNodes()) {
      while (list.list.childNodes.length >= 1)
      {
        list.list.removeChild(list.list.firstChild);
      }
    }
  };

  init();
};

module.exports = function(list) {
  return new Templater(list);
};


/***/ }),

/***/ "./node_modules/list.js/src/utils/classes.js":
/*!***************************************************!*\
  !*** ./node_modules/list.js/src/utils/classes.js ***!
  \***************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

/**
 * Module dependencies.
 */

var index = __webpack_require__(/*! ./index-of */ "./node_modules/list.js/src/utils/index-of.js");

/**
 * Whitespace regexp.
 */

var re = /\s+/;

/**
 * toString reference.
 */

var toString = Object.prototype.toString;

/**
 * Wrap `el` in a `ClassList`.
 *
 * @param {Element} el
 * @return {ClassList}
 * @api public
 */

module.exports = function(el){
  return new ClassList(el);
};

/**
 * Initialize a new ClassList for `el`.
 *
 * @param {Element} el
 * @api private
 */

function ClassList(el) {
  if (!el || !el.nodeType) {
    throw new Error('A DOM element reference is required');
  }
  this.el = el;
  this.list = el.classList;
}

/**
 * Add class `name` if not already present.
 *
 * @param {String} name
 * @return {ClassList}
 * @api public
 */

ClassList.prototype.add = function(name){
  // classList
  if (this.list) {
    this.list.add(name);
    return this;
  }

  // fallback
  var arr = this.array();
  var i = index(arr, name);
  if (!~i) arr.push(name);
  this.el.className = arr.join(' ');
  return this;
};

/**
 * Remove class `name` when present, or
 * pass a regular expression to remove
 * any which match.
 *
 * @param {String|RegExp} name
 * @return {ClassList}
 * @api public
 */

ClassList.prototype.remove = function(name){
  // classList
  if (this.list) {
    this.list.remove(name);
    return this;
  }

  // fallback
  var arr = this.array();
  var i = index(arr, name);
  if (~i) arr.splice(i, 1);
  this.el.className = arr.join(' ');
  return this;
};


/**
 * Toggle class `name`, can force state via `force`.
 *
 * For browsers that support classList, but do not support `force` yet,
 * the mistake will be detected and corrected.
 *
 * @param {String} name
 * @param {Boolean} force
 * @return {ClassList}
 * @api public
 */

ClassList.prototype.toggle = function(name, force){
  // classList
  if (this.list) {
    if ("undefined" !== typeof force) {
      if (force !== this.list.toggle(name, force)) {
        this.list.toggle(name); // toggle again to correct
      }
    } else {
      this.list.toggle(name);
    }
    return this;
  }

  // fallback
  if ("undefined" !== typeof force) {
    if (!force) {
      this.remove(name);
    } else {
      this.add(name);
    }
  } else {
    if (this.has(name)) {
      this.remove(name);
    } else {
      this.add(name);
    }
  }

  return this;
};

/**
 * Return an array of classes.
 *
 * @return {Array}
 * @api public
 */

ClassList.prototype.array = function(){
  var className = this.el.getAttribute('class') || '';
  var str = className.replace(/^\s+|\s+$/g, '');
  var arr = str.split(re);
  if ('' === arr[0]) arr.shift();
  return arr;
};

/**
 * Check if class `name` is present.
 *
 * @param {String} name
 * @return {ClassList}
 * @api public
 */

ClassList.prototype.has =
ClassList.prototype.contains = function(name){
  return this.list ? this.list.contains(name) : !! ~index(this.array(), name);
};


/***/ }),

/***/ "./node_modules/list.js/src/utils/events.js":
/*!**************************************************!*\
  !*** ./node_modules/list.js/src/utils/events.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

var bind = window.addEventListener ? 'addEventListener' : 'attachEvent',
    unbind = window.removeEventListener ? 'removeEventListener' : 'detachEvent',
    prefix = bind !== 'addEventListener' ? 'on' : '',
    toArray = __webpack_require__(/*! ./to-array */ "./node_modules/list.js/src/utils/to-array.js");

/**
 * Bind `el` event `type` to `fn`.
 *
 * @param {Element} el, NodeList, HTMLCollection or Array
 * @param {String} type
 * @param {Function} fn
 * @param {Boolean} capture
 * @api public
 */

exports.bind = function(el, type, fn, capture){
  el = toArray(el);
  for ( var i = 0; i < el.length; i++ ) {
    el[i][bind](prefix + type, fn, capture || false);
  }
};

/**
 * Unbind `el` event `type`'s callback `fn`.
 *
 * @param {Element} el, NodeList, HTMLCollection or Array
 * @param {String} type
 * @param {Function} fn
 * @param {Boolean} capture
 * @api public
 */

exports.unbind = function(el, type, fn, capture){
  el = toArray(el);
  for ( var i = 0; i < el.length; i++ ) {
    el[i][unbind](prefix + type, fn, capture || false);
  }
};


/***/ }),

/***/ "./node_modules/list.js/src/utils/extend.js":
/*!**************************************************!*\
  !*** ./node_modules/list.js/src/utils/extend.js ***!
  \**************************************************/
/***/ ((module) => {

/*
 * Source: https://github.com/segmentio/extend
 */

module.exports = function extend (object) {
    // Takes an unlimited number of extenders.
    var args = Array.prototype.slice.call(arguments, 1);

    // For each extender, copy their properties on our object.
    for (var i = 0, source; source = args[i]; i++) {
        if (!source) continue;
        for (var property in source) {
            object[property] = source[property];
        }
    }

    return object;
};


/***/ }),

/***/ "./node_modules/list.js/src/utils/fuzzy.js":
/*!*************************************************!*\
  !*** ./node_modules/list.js/src/utils/fuzzy.js ***!
  \*************************************************/
/***/ ((module) => {

module.exports = function(text, pattern, options) {
    // Aproximately where in the text is the pattern expected to be found?
    var Match_Location = options.location || 0;

    //Determines how close the match must be to the fuzzy location (specified above). An exact letter match which is 'distance' characters away from the fuzzy location would score as a complete mismatch. A distance of '0' requires the match be at the exact location specified, a threshold of '1000' would require a perfect match to be within 800 characters of the fuzzy location to be found using a 0.8 threshold.
    var Match_Distance = options.distance || 100;

    // At what point does the match algorithm give up. A threshold of '0.0' requires a perfect match (of both letters and location), a threshold of '1.0' would match anything.
    var Match_Threshold = options.threshold || 0.4;

    if (pattern === text) return true; // Exact match
    if (pattern.length > 32) return false; // This algorithm cannot be used

    // Set starting location at beginning text and initialise the alphabet.
    var loc = Match_Location,
        s = (function() {
            var q = {},
                i;

            for (i = 0; i < pattern.length; i++) {
                q[pattern.charAt(i)] = 0;
            }

            for (i = 0; i < pattern.length; i++) {
                q[pattern.charAt(i)] |= 1 << (pattern.length - i - 1);
            }

            return q;
        }());

    // Compute and return the score for a match with e errors and x location.
    // Accesses loc and pattern through being a closure.

    function match_bitapScore_(e, x) {
        var accuracy = e / pattern.length,
            proximity = Math.abs(loc - x);

        if (!Match_Distance) {
            // Dodge divide by zero error.
            return proximity ? 1.0 : accuracy;
        }
        return accuracy + (proximity / Match_Distance);
    }

    var score_threshold = Match_Threshold, // Highest score beyond which we give up.
        best_loc = text.indexOf(pattern, loc); // Is there a nearby exact match? (speedup)

    if (best_loc != -1) {
        score_threshold = Math.min(match_bitapScore_(0, best_loc), score_threshold);
        // What about in the other direction? (speedup)
        best_loc = text.lastIndexOf(pattern, loc + pattern.length);

        if (best_loc != -1) {
            score_threshold = Math.min(match_bitapScore_(0, best_loc), score_threshold);
        }
    }

    // Initialise the bit arrays.
    var matchmask = 1 << (pattern.length - 1);
    best_loc = -1;

    var bin_min, bin_mid;
    var bin_max = pattern.length + text.length;
    var last_rd;
    for (var d = 0; d < pattern.length; d++) {
        // Scan for the best match; each iteration allows for one more error.
        // Run a binary search to determine how far from 'loc' we can stray at this
        // error level.
        bin_min = 0;
        bin_mid = bin_max;
        while (bin_min < bin_mid) {
            if (match_bitapScore_(d, loc + bin_mid) <= score_threshold) {
                bin_min = bin_mid;
            } else {
                bin_max = bin_mid;
            }
            bin_mid = Math.floor((bin_max - bin_min) / 2 + bin_min);
        }
        // Use the result from this iteration as the maximum for the next.
        bin_max = bin_mid;
        var start = Math.max(1, loc - bin_mid + 1);
        var finish = Math.min(loc + bin_mid, text.length) + pattern.length;

        var rd = Array(finish + 2);
        rd[finish + 1] = (1 << d) - 1;
        for (var j = finish; j >= start; j--) {
            // The alphabet (s) is a sparse hash, so the following line generates
            // warnings.
            var charMatch = s[text.charAt(j - 1)];
            if (d === 0) {    // First pass: exact match.
                rd[j] = ((rd[j + 1] << 1) | 1) & charMatch;
            } else {    // Subsequent passes: fuzzy match.
                rd[j] = (((rd[j + 1] << 1) | 1) & charMatch) |
                                (((last_rd[j + 1] | last_rd[j]) << 1) | 1) |
                                last_rd[j + 1];
            }
            if (rd[j] & matchmask) {
                var score = match_bitapScore_(d, j - 1);
                // This match will almost certainly be better than any existing match.
                // But check anyway.
                if (score <= score_threshold) {
                    // Told you so.
                    score_threshold = score;
                    best_loc = j - 1;
                    if (best_loc > loc) {
                        // When passing loc, don't exceed our current distance from loc.
                        start = Math.max(1, 2 * loc - best_loc);
                    } else {
                        // Already passed loc, downhill from here on in.
                        break;
                    }
                }
            }
        }
        // No hope for a (better) match at greater error levels.
        if (match_bitapScore_(d + 1, loc) > score_threshold) {
            break;
        }
        last_rd = rd;
    }

    return (best_loc < 0) ? false : true;
};


/***/ }),

/***/ "./node_modules/list.js/src/utils/get-attribute.js":
/*!*********************************************************!*\
  !*** ./node_modules/list.js/src/utils/get-attribute.js ***!
  \*********************************************************/
/***/ ((module) => {

/**
 * A cross-browser implementation of getAttribute.
 * Source found here: http://stackoverflow.com/a/3755343/361337 written by Vivin Paliath
 *
 * Return the value for `attr` at `element`.
 *
 * @param {Element} el
 * @param {String} attr
 * @api public
 */

module.exports = function(el, attr) {
  var result = (el.getAttribute && el.getAttribute(attr)) || null;
  if( !result ) {
    var attrs = el.attributes;
    var length = attrs.length;
    for(var i = 0; i < length; i++) {
      if (attr[i] !== undefined) {
        if(attr[i].nodeName === attr) {
          result = attr[i].nodeValue;
        }
      }
    }
  }
  return result;
};


/***/ }),

/***/ "./node_modules/list.js/src/utils/get-by-class.js":
/*!********************************************************!*\
  !*** ./node_modules/list.js/src/utils/get-by-class.js ***!
  \********************************************************/
/***/ ((module) => {

/**
 * A cross-browser implementation of getElementsByClass.
 * Heavily based on Dustin Diaz's function: http://dustindiaz.com/getelementsbyclass.
 *
 * Find all elements with class `className` inside `container`.
 * Use `single = true` to increase performance in older browsers
 * when only one element is needed.
 *
 * @param {String} className
 * @param {Element} container
 * @param {Boolean} single
 * @api public
 */

var getElementsByClassName = function(container, className, single) {
  if (single) {
    return container.getElementsByClassName(className)[0];
  } else {
    return container.getElementsByClassName(className);
  }
};

var querySelector = function(container, className, single) {
  className = '.' + className;
  if (single) {
    return container.querySelector(className);
  } else {
    return container.querySelectorAll(className);
  }
};

var polyfill = function(container, className, single) {
  var classElements = [],
    tag = '*';

  var els = container.getElementsByTagName(tag);
  var elsLen = els.length;
  var pattern = new RegExp("(^|\\s)"+className+"(\\s|$)");
  for (var i = 0, j = 0; i < elsLen; i++) {
    if ( pattern.test(els[i].className) ) {
      if (single) {
        return els[i];
      } else {
        classElements[j] = els[i];
        j++;
      }
    }
  }
  return classElements;
};

module.exports = (function() {
  return function(container, className, single, options) {
    options = options || {};
    if ((options.test && options.getElementsByClassName) || (!options.test && document.getElementsByClassName)) {
      return getElementsByClassName(container, className, single);
    } else if ((options.test && options.querySelector) || (!options.test && document.querySelector)) {
      return querySelector(container, className, single);
    } else {
      return polyfill(container, className, single);
    }
  };
})();


/***/ }),

/***/ "./node_modules/list.js/src/utils/index-of.js":
/*!****************************************************!*\
  !*** ./node_modules/list.js/src/utils/index-of.js ***!
  \****************************************************/
/***/ ((module) => {

var indexOf = [].indexOf;

module.exports = function(arr, obj){
  if (indexOf) return arr.indexOf(obj);
  for (var i = 0; i < arr.length; ++i) {
    if (arr[i] === obj) return i;
  }
  return -1;
};


/***/ }),

/***/ "./node_modules/list.js/src/utils/to-array.js":
/*!****************************************************!*\
  !*** ./node_modules/list.js/src/utils/to-array.js ***!
  \****************************************************/
/***/ ((module) => {

/**
 * Source: https://github.com/timoxley/to-array
 *
 * Convert an array-like object into an `Array`.
 * If `collection` is already an `Array`, then will return a clone of `collection`.
 *
 * @param {Array | Mixed} collection An `Array` or array-like object to convert e.g. `arguments` or `NodeList`
 * @return {Array} Naive conversion of `collection` to a new `Array`.
 * @api public
 */

module.exports = function toArray(collection) {
  if (typeof collection === 'undefined') return [];
  if (collection === null) return [null];
  if (collection === window) return [window];
  if (typeof collection === 'string') return [collection];
  if (isArray(collection)) return collection;
  if (typeof collection.length != 'number') return [collection];
  if (typeof collection === 'function' && collection instanceof Function) return [collection];

  var arr = [];
  for (var i = 0; i < collection.length; i++) {
    if (Object.prototype.hasOwnProperty.call(collection, i) || i in collection) {
      arr.push(collection[i]);
    }
  }
  if (!arr.length) return [];
  return arr;
};

function isArray(arr) {
  return Object.prototype.toString.call(arr) === "[object Array]";
}


/***/ }),

/***/ "./node_modules/list.js/src/utils/to-string.js":
/*!*****************************************************!*\
  !*** ./node_modules/list.js/src/utils/to-string.js ***!
  \*****************************************************/
/***/ ((module) => {

module.exports = function(s) {
  s = (s === undefined) ? "" : s;
  s = (s === null) ? "" : s;
  s = s.toString();
  return s;
};


/***/ }),

/***/ "./node_modules/string-natural-compare/natural-compare.js":
/*!****************************************************************!*\
  !*** ./node_modules/string-natural-compare/natural-compare.js ***!
  \****************************************************************/
/***/ ((module) => {

"use strict";


var alphabet;
var alphabetIndexMap;
var alphabetIndexMapLength = 0;

function isNumberCode(code) {
  return code >= 48 && code <= 57;
}

function naturalCompare(a, b) {
  var lengthA = (a += '').length;
  var lengthB = (b += '').length;
  var aIndex = 0;
  var bIndex = 0;

  while (aIndex < lengthA && bIndex < lengthB) {
    var charCodeA = a.charCodeAt(aIndex);
    var charCodeB = b.charCodeAt(bIndex);

    if (isNumberCode(charCodeA)) {
      if (!isNumberCode(charCodeB)) {
        return charCodeA - charCodeB;
      }

      var numStartA = aIndex;
      var numStartB = bIndex;

      while (charCodeA === 48 && ++numStartA < lengthA) {
        charCodeA = a.charCodeAt(numStartA);
      }
      while (charCodeB === 48 && ++numStartB < lengthB) {
        charCodeB = b.charCodeAt(numStartB);
      }

      var numEndA = numStartA;
      var numEndB = numStartB;

      while (numEndA < lengthA && isNumberCode(a.charCodeAt(numEndA))) {
        ++numEndA;
      }
      while (numEndB < lengthB && isNumberCode(b.charCodeAt(numEndB))) {
        ++numEndB;
      }

      var difference = numEndA - numStartA - numEndB + numStartB; // numA length - numB length
      if (difference) {
        return difference;
      }

      while (numStartA < numEndA) {
        difference = a.charCodeAt(numStartA++) - b.charCodeAt(numStartB++);
        if (difference) {
          return difference;
        }
      }

      aIndex = numEndA;
      bIndex = numEndB;
      continue;
    }

    if (charCodeA !== charCodeB) {
      if (
        charCodeA < alphabetIndexMapLength &&
        charCodeB < alphabetIndexMapLength &&
        alphabetIndexMap[charCodeA] !== -1 &&
        alphabetIndexMap[charCodeB] !== -1
      ) {
        return alphabetIndexMap[charCodeA] - alphabetIndexMap[charCodeB];
      }

      return charCodeA - charCodeB;
    }

    ++aIndex;
    ++bIndex;
  }

  if (aIndex >= lengthA && bIndex < lengthB && lengthA >= lengthB) {
    return -1;
  }

  if (bIndex >= lengthB && aIndex < lengthA && lengthB >= lengthA) {
    return 1;
  }

  return lengthA - lengthB;
}

naturalCompare.caseInsensitive = naturalCompare.i = function(a, b) {
  return naturalCompare(('' + a).toLowerCase(), ('' + b).toLowerCase());
};

Object.defineProperties(naturalCompare, {
  alphabet: {
    get: function() {
      return alphabet;
    },

    set: function(value) {
      alphabet = value;
      alphabetIndexMap = [];

      var i = 0;

      if (alphabet) {
        for (; i < alphabet.length; i++) {
          alphabetIndexMap[alphabet.charCodeAt(i)] = i;
        }
      }

      alphabetIndexMapLength = alphabetIndexMap.length;

      for (i = 0; i < alphabetIndexMapLength; i++) {
        if (alphabetIndexMap[i] === undefined) {
          alphabetIndexMap[i] = -1;
        }
      }
    },
  },
});

module.exports = naturalCompare;


/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ ((module) => {

"use strict";
module.exports = window["jQuery"];

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
/*!****************************************************************!*\
  !*** ./src/assets/js/frontend-gradebook/frontend-gradebook.js ***!
  \****************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var list_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! list.js */ "./node_modules/list.js/src/index.js");
/* harmony import */ var list_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(list_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _minimal_foundation__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./minimal-foundation */ "./src/assets/js/frontend-gradebook/minimal-foundation.js");
/* harmony import */ var _ancient_browser_support__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ancient-browser-support */ "./src/assets/js/frontend-gradebook/ancient-browser-support.js");
/* harmony import */ var _ancient_browser_support__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_ancient_browser_support__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _lib_conditionally_load_select2__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../lib/conditionally-load-select2 */ "./src/assets/js/lib/conditionally-load-select2.js");
/* harmony import */ var _lib_conditionally_load_select2__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_lib_conditionally_load_select2__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _lib_update_url__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../lib/update-url */ "./src/assets/js/lib/update-url.js");
/* eslint-disable -- TODO: fix linting issues */





var naturalSort = __webpack_require__(/*! string-natural-compare */ "./node_modules/string-natural-compare/natural-compare.js");

(function ($, l10n, i18n) {
  var api = {
    /**
     * Runs only once when the file is loaded. Do not put anything here but Event Listeners or other things which should only run once.
     *
     * @since   2.0.0
     * @return  void
     */
    init: function init() {
      $(window).on('select2-loaded', function () {
        $('.ld-gb-frontend-gradebook-gradebook-dropdown, .ld-gb-frontend-gradebook-group-dropdown').select2({
          theme: 'foundation'
        });
      });
      $(document).on('change', '.ld-gb-frontend-gradebook-gradebook-dropdown, .ld-gb-frontend-gradebook-group-dropdown', api.refreshGradebook);
      $(document).on('ld-gb-frontend-gradebook-list-created', function (event, list, container) {
        var totalPages = $(container).data('total_pages'),
          perPage = $(container).data('per_page'),
          gradeFormat = $(container).data('grade_format'),
          $root = $(container).closest('.ld-gb-frontend-gradebook'),
          gradebookID = parseInt($root.find('.ld-gb-frontend-gradebook-gradebook-dropdown').val()),
          groupID = parseInt($root.find('.ld-gb-frontend-gradebook-group-dropdown').val());
        if (list.get().length < perPage) return;
        console.log($(container).find('.pagination + .loading-icon'));
        $(container).find('.pagination + .loading-icon').show();

        // Start with Page 2
        api.getPage(2, totalPages, perPage, gradeFormat, list, gradebookID, groupID, $(container));
      });
      $(document).on('ld-gb-frontend-gradebook-hide-gradebook-panel', api.hideGradebook);
      $(document).on('ld-gb-frontend-gradebook-loading-edit-panel', api.loadEditPanel);
      $(document).on('ld-gb-frontend-gradebook-loaded-edit-panel', api.showEditPanel);
      $(document).on('ld-gb-frontend-gradebook-hide-edit-panel', api.hideEditPanel);
      $(document).on('ld-gb-frontend-gradebook-cleared-edit-panel', api.showGradebook);
      $(document).on('click touch', '.ld-gb-frontend-gradebook a.open-edit-panel', function (event) {
        event.preventDefault();
        var $root = $(this).closest('.ld-gb-frontend-gradebook'),
          $gradebook = $root.find('.gradebook-container'),
          $editPanel = $root.find('.ld-gb-frontend-gradebook-edit-panel');
        $(document).trigger('ld-gb-frontend-gradebook-hide-gradebook-panel', [parseInt($(this).data('user_id')), parseInt($(this).data('gradebook_id')), parseInt($(this).data('group_id')), $(this).data('grade_format'), $gradebook, $editPanel, _minimal_foundation__WEBPACK_IMPORTED_MODULE_1__.Foundation]);
      });
      $(document).on('click touch', '.ld-gb-frontend-gradebook a.back-to-gradebook', function (event) {
        event.preventDefault();
        var $root = $(this).closest('.ld-gb-frontend-gradebook'),
          $gradebook = $root.find('.gradebook-container'),
          $editPanel = $root.find('.ld-gb-frontend-gradebook-edit-panel');
        $(document).trigger('ld-gb-frontend-gradebook-hide-edit-panel', [parseInt($(this).data('user_id')), parseInt($(this).data('gradebook_id')), parseInt($(this).data('group_id')), $(this).data('grade_format'), $gradebook, $editPanel, _minimal_foundation__WEBPACK_IMPORTED_MODULE_1__.Foundation]);
      });
      $(document).on('click touch', '.ld-gb-frontend-gradebook-grade-add-show-form', function (event) {
        $(this).hide();
        var $form = $(this).next('.ld-gb-frontend-gradebook-grade-add').first();
        _minimal_foundation__WEBPACK_IMPORTED_MODULE_1__.Foundation.Motion.animateIn($form, 'slide-in-up');
      });
      $(document).on('submit', '.ld-gb-frontend-gradebook-grade-add', function (event) {
        event.preventDefault();
        var form = $(this)[0];
        form.reportValidity();
        if (!form.checkValidity()) return;
        var $submitButton = $(form).find('input[type="submit"]'),
          buttonText = $submitButton.data('button_text') ? $submitButton.data('button_text') : $submitButton.val();
        $submitButton.data('button_text', buttonText);
        $submitButton.val($submitButton.data('submitting_text')).attr('disabled', true);
        var userID = $(form).data('user_id'),
          gradebookID = $(form).data('gradebook_id'),
          componentID = $(form).data('component_id');
        var data = {
            grade: {},
            grade_format: $(form).data('grade_format'),
            group_id: $(form).data('group_id')
          },
          fields = $(this).serializeArray();
        for (var index in fields) {
          data.grade[fields[index].name] = fields[index].value;
        }
        $.ajax({
          url: l10n.rest + 'add-manual-grade/' + userID + '/' + gradebookID + '/' + componentID + '/',
          method: 'POST',
          cache: false,
          data: data,
          headers: {
            'X-WP-Nonce': l10n.nonce
          },
          success: function success(response) {
            $submitButton.val($submitButton.data('button_text')).attr('disabled', false);
            $(form).find('.ld-gb-frontend-gradebook-grade-add-cancel').trigger('click');
            var $editPanel = $(form).closest('.ld-gb-frontend-gradebook-edit-panel'),
              $component = $editPanel.find('.ld-gb-frontend-gradebook-component[data-component="' + componentID + '"]'),
              $componentTable = $component.find('table');

            // Remove the dummy row
            $componentTable.find('.ld-gb-frontend-gradebook-no-grades').remove();
            $componentTable.find('tbody').append(response.edit_panel_grade_row);

            // Updates the Gradebook List and "global" aspects of the Components in the Edit Panel
            api.updateUserGrade(userID, componentID, response, $editPanel);
            form.reset();
          },
          error: function error(request, status, _error) {
            $submitButton.val($submitButton.data('button_text')).attr('disabled', false);
            var errorMessage = JSON.parse(request.responseText);
            if (typeof errorMessage.message !== 'undefined') {
              console.error(errorMessage.message);
            }
            if (typeof errorMessage.html !== 'undefined') {
              $(form).prepend(errorMessage.html);
            }
          }
        });
      });
      $(document).on('click touch', '.ld-gb-frontend-gradebook-edit-panel .ld-gb-frontend-gradebook-grade-update-show:not( .disabled )', function (event) {
        event.preventDefault();
        var $component = $(this).closest('.ld-gb-frontend-gradebook-component');
        if (!$component.attr('data-edit-modal')) {
          var modal = new _minimal_foundation__WEBPACK_IMPORTED_MODULE_1__.Foundation.Reveal($component.find('.ld-gb-frontend-gradebook-grade-edit-overlay'));
          $component.attr('data-edit-modal', modal.uuid);
        }
        var $modal = $('[data-reveal="' + $component.attr('data-edit-modal')),
          $editLink = $(this),
          $nameFields = $modal.find('[name]');
        if ($editLink.data('type') !== 'manual') {
          $modal.find('.manual-grade-specific').hide();
        } else {
          $modal.find('.manual-grade-specific').show();
        }
        $nameFields.each(function (index, element) {
          var name = $(element).attr('name');
          if ($editLink.data(name)) {
            $(element).val($editLink.data(name));
          }
        });
        $modal.foundation('open');
      });
      $(document).on('submit', '.ld-gb-frontend-gradebook-grade-edit', function (event) {
        event.preventDefault();
        var form = $(this)[0];
        form.reportValidity();
        if (!form.checkValidity()) return;
        var $submitButton = $(form).find('input[type="submit"]'),
          buttonText = $submitButton.data('button_text') ? $submitButton.data('button_text') : $submitButton.val();
        $submitButton.data('button_text', buttonText);
        $submitButton.val($submitButton.data('submitting_text')).attr('disabled', true);
        var userID = $(form).data('user_id'),
          gradebookID = $(form).data('gradebook_id'),
          componentID = $(form).data('component_id');
        var data = {
            grade: {},
            grade_format: $(form).data('grade_format'),
            group_id: $(form).data('group_id')
          },
          fields = $(this).serializeArray();
        for (var index in fields) {
          data.grade[fields[index].name] = fields[index].value;
        }
        $.ajax({
          url: l10n.rest + 'edit-grade/' + userID + '/' + gradebookID + '/' + componentID + '/',
          method: 'POST',
          cache: false,
          data: data,
          headers: {
            'X-WP-Nonce': l10n.nonce
          },
          success: function success(response) {
            $submitButton.val($submitButton.data('button_text')).attr('disabled', false);
            var $reveal = $submitButton.closest('.ld-gb-frontend-gradebook-grade-edit-overlay'),
              uuid = $reveal.attr('data-reveal'),
              $component = $('.ld-gb-frontend-gradebook-component[data-edit-modal="' + uuid + '"]');
            var $editPanel = $component.closest('.ld-gb-frontend-gradebook-edit-panel');
            var $row = false;
            if (data.grade.type == 'manual') {
              $row = $component.find('.ld-gb-frontend-gradebook-grade-update-show[data-name="' + data.grade.name + '"]').closest('tr');
            } else {
              $row = $component.find('.ld-gb-frontend-gradebook-grade-update-show[data-post_id="' + data.grade.post_id + '"]').closest('tr');
            }
            $(form).find('.ld-gb-frontend-gradebook-grade-edit-cancel').trigger('click');
            $row.replaceWith(response.edit_panel_grade_row);

            // Updates the Gradebook List and "global" aspects of the Components in the Edit Panel
            api.updateUserGrade(userID, componentID, response, $editPanel);
            form.reset();
          },
          error: function error(request, status, _error2) {
            $submitButton.val($submitButton.data('button_text')).attr('disabled', false);
            var errorMessage = JSON.parse(request.responseText);
            if (typeof errorMessage.message !== 'undefined') {
              console.error(errorMessage.message);
            } else if (typeof errorMessage.message !== 'undefined') {
              console.error(errorMessage.message);
            }
            if (typeof errorMessage.html !== 'undefined') {
              $(form).prepend(errorMessage.html);
            }
          }
        });
      });
      $(document).on('click touch', '.ld-gb-frontend-gradebook-grade-edit-cancel', function (event) {
        // Ensure the form does not submit
        event.preventDefault();
        var $form = $(this).closest('.ld-gb-frontend-gradebook-grade-edit'),
          $modal = $form.closest('.ld-gb-frontend-gradebook-grade-edit-overlay');
        $modal.foundation('close');
        $form[0].reset();
      });
      $(document).on('click touch', '.ld-gb-frontend-gradebook-edit-panel .ld-gb-frontend-gradebook-grade-remove:not( .disabled )', function (event) {
        event.preventDefault();
        var $submitButton = $(this),
          buttonText = $submitButton.data('button_text') ? $submitButton.data('button_text') : $submitButton.text();
        $submitButton.data('button_text', buttonText);
        if (!confirm(i18n.deleteGrade)) return;
        $submitButton.text($submitButton.data('submitting_text')).addClass('disabled');
        var $row = $submitButton.closest('tr'),
          $actions = $row.find('.ld-gb-frontend-gradebook-grade-actions'),
          userID = $actions.data('user_id'),
          gradebookID = $actions.data('gradebook_id'),
          componentID = $actions.data('component_id');
        var data = {
          grade: {
            name: $row.find('.ld-gb-frontend-gradebook-grade-name .ld-gb-frontend-gradebook-grade-name-content').text()
          },
          grade_format: $actions.data('grade_format'),
          group_id: $actions.data('group_id')
        };
        $.ajax({
          url: l10n.rest + 'delete-manual-grade/' + userID + '/' + gradebookID + '/' + componentID + '/',
          method: 'POST',
          cache: false,
          data: data,
          headers: {
            'X-WP-Nonce': l10n.nonce
          },
          success: function success(response) {
            $submitButton.text($submitButton.data('button_text')).removeClass('disabled');
            var $editPanel = $row.closest('.ld-gb-frontend-gradebook-edit-panel'),
              $component = $editPanel.find('.ld-gb-frontend-gradebook-component[data-component="' + componentID + '"]'),
              $componentTable = $component.find('table');
            $row.remove();
            $componentTable.find('tbody').append(response.edit_panel_no_grades);

            // Updates the Gradebook List and "global" aspects of the Components in the Edit Panel
            api.updateUserGrade(userID, componentID, response, $editPanel);
          },
          error: function error(request, status, _error3) {
            $submitButton.text($submitButton.data('button_text')).removeClass('disabled');
            var errorMessage = JSON.parse(request.responseText);
            if (typeof errorMessage.message !== 'undefined') {
              console.error(errorMessage.message);
            } else if (typeof errorMessage.message !== 'undefined') {
              console.error(errorMessage.message);
            }
            if (typeof errorMessage.html !== 'undefined') {
              var $editPanel = $row.closest('.ld-gb-frontend-gradebook-edit-panel'),
                $component = $editPanel.find('.ld-gb-frontend-gradebook-component[data-component="' + componentID + '"]');
              $component.prepend(errorMessage.html);
            }
          }
        });
      });
      $(document).on('click touch', '.ld-gb-frontend-gradebook-grade-add-cancel', function (event) {
        // Ensure the form does not submit
        event.preventDefault();
        var $form = $(this).closest('.ld-gb-frontend-gradebook-grade-add'),
          $button = $form.prev('.ld-gb-frontend-gradebook-grade-add-show-form');
        $button.show();
        _minimal_foundation__WEBPACK_IMPORTED_MODULE_1__.Foundation.Motion.animateOut($form, 'slide-out-down');
      });
      $(document).on('click touch', '.ld-gb-frontend-gradebook-edit-panel .ld-gb-frontend-gradebook-component-grade-override-show:not( .disabled )', function (event) {
        event.preventDefault();
        var $component = $(this).closest('.ld-gb-frontend-gradebook-component');
        if (!$component.attr('data-override-modal')) {
          var modal = new _minimal_foundation__WEBPACK_IMPORTED_MODULE_1__.Foundation.Reveal($component.find('.ld-gb-frontend-gradebook-component-grade-override-overlay'));
          $component.attr('data-override-modal', modal.uuid);
        }
        var $modal = $('[data-reveal="' + $component.attr('data-override-modal')),
          $editLink = $(this),
          $nameFields = $modal.find('[name]');
        $nameFields.each(function (index, element) {
          var name = $(element).attr('name');
          if ($editLink.data(name)) {
            $(element).val($editLink.data(name));
          }
        });
        if (!$(this).attr('data-overridden')) {
          $modal.find('.ld-gb-frontend-gradebook-component-grade-override-cancel').addClass('disabled').hide();
        } else {
          $modal.find('.ld-gb-frontend-gradebook-component-grade-override-cancel').removeClass('disabled').show();
        }
        $modal.foundation('open');
      });
      $(document).on('submit', '.ld-gb-frontend-gradebook-component-grade-override', function (event) {
        event.preventDefault();
        var form = $(this)[0];
        form.reportValidity();
        if (!form.checkValidity()) return;
        var $submitButton = $(form).find('input[type="submit"]'),
          buttonText = $submitButton.data('button_text') ? $submitButton.data('button_text') : $submitButton.val();
        $submitButton.data('button_text', buttonText);
        $submitButton.val($submitButton.data('submitting_text')).attr('disabled', true);
        var userID = $(form).data('user_id'),
          gradebookID = $(form).data('gradebook_id'),
          componentID = $(form).data('component_id');
        var data = {
            grade_format: $(form).data('grade_format'),
            group_id: $(form).data('group_id')
          },
          fields = $(this).serializeArray();
        for (var index in fields) {
          data[fields[index].name] = fields[index].value;
        }
        $.ajax({
          url: l10n.rest + 'override-component-grade/' + userID + '/' + gradebookID + '/' + componentID + '/',
          method: 'POST',
          cache: false,
          data: data,
          headers: {
            'X-WP-Nonce': l10n.nonce
          },
          success: function success(response) {
            $submitButton.val($submitButton.data('button_text')).attr('disabled', false);
            var $reveal = $submitButton.closest('.ld-gb-frontend-gradebook-component-grade-override-overlay'),
              uuid = $reveal.attr('data-reveal'),
              $component = $('.ld-gb-frontend-gradebook-component[data-override-modal="' + uuid + '"]');
            var $editPanel = $component.closest('.ld-gb-frontend-gradebook-edit-panel');
            $reveal.foundation('close');

            // Updates the Gradebook List and "global" aspects of the Components in the Edit Panel
            api.updateUserGrade(userID, componentID, response, $editPanel);
            form.reset();
          },
          error: function error(request, status, _error4) {
            $submitButton.val($submitButton.data('button_text')).attr('disabled', false);
            var errorMessage = JSON.parse(request.responseText);
            if (typeof errorMessage.message !== 'undefined') {
              console.error(errorMessage.message);
            } else if (typeof errorMessage.message !== 'undefined') {
              console.error(errorMessage.message);
            }
            if (typeof errorMessage.html !== 'undefined') {
              $(form).prepend(errorMessage.html);
            }
          }
        });
      });
      $(document).on('click touch', '.ld-gb-frontend-gradebook-component-grade-override-cancel:not(.disabled)', function (event) {
        event.preventDefault();
        var $form = $(this).closest('.ld-gb-frontend-gradebook-component-grade-override');
        var $submitButton = $(this),
          buttonText = $submitButton.data('button_text') ? $submitButton.data('button_text') : $submitButton.text();
        $submitButton.data('button_text', buttonText);
        $submitButton.text($submitButton.data('submitting_text')).addClass('disabled');
        var userID = $form.data('user_id'),
          gradebookID = $form.data('gradebook_id'),
          componentID = $form.data('component_id');
        var data = {
          grade_format: $form.data('grade_format'),
          group_id: $form.data('group_id')
        };
        $.ajax({
          url: l10n.rest + 'delete-component-override/' + userID + '/' + gradebookID + '/' + componentID + '/',
          method: 'POST',
          cache: false,
          data: data,
          headers: {
            'X-WP-Nonce': l10n.nonce
          },
          success: function success(response) {
            $submitButton.text($submitButton.data('button_text')).removeClass('disabled');
            var $reveal = $submitButton.closest('.ld-gb-frontend-gradebook-component-grade-override-overlay'),
              uuid = $reveal.attr('data-reveal'),
              $component = $('.ld-gb-frontend-gradebook-component[data-override-modal="' + uuid + '"]');
            var $editPanel = $component.closest('.ld-gb-frontend-gradebook-edit-panel');
            $reveal.foundation('close');

            // Updates the Gradebook List and "global" aspects of the Components in the Edit Panel
            api.updateUserGrade(userID, componentID, response, $editPanel);
            $form[0].reset();
          },
          error: function error(request, status, _error5) {
            $submitButton.text($submitButton.data('button_text')).removeClass('disabled');
            var errorMessage = JSON.parse(request.responseText);
            if (typeof errorMessage.message !== 'undefined') {
              console.error(errorMessage.message);
            } else if (typeof errorMessage.message !== 'undefined') {
              console.error(errorMessage.message);
            }
            if (typeof errorMessage.html !== 'undefined') {
              $form.prepend(errorMessage.html);
            }
          }
        });
      });
      $(document).on('submit', '.export-gradebook-components', api.gradebook_export_components_submit);
      $(document).on('submit', '.export-gradebook-all-grades', api.gradebook_export_all_grades_submit);
      Array.prototype.forEach.call(document.querySelectorAll('.ld-gb-frontend-gradebook-list-container'), function (container) {
        api.createList(container);
      });
    },
    /**
     * Refreshes the Gradebook when the Gradebook or Group dropdowns update
     *
     * @param object event  Event Object
     *
     * @param event
     * @since   2.0.0
     * @return  void
     */
    refreshGradebook: function refreshGradebook(event) {
      var $root = $(this).closest('.ld-gb-frontend-gradebook'),
        $gradebookDropdown = $root.find('.ld-gb-frontend-gradebook-gradebook-dropdown'),
        gradebookID = parseInt($gradebookDropdown.val()),
        $groupDropdown = $root.find('.ld-gb-frontend-gradebook-group-dropdown'),
        groupID = parseInt($groupDropdown.val());
      $gradebookDropdown.attr('disabled', true);
      $groupDropdown.attr('disabled', true);
      $.ajax({
        url: l10n.rest + 'get-frontend-gradebook/' + gradebookID + '/' + groupID + '/',
        method: 'GET',
        cache: false,
        headers: {
          'X-WP-Nonce': l10n.nonce
        },
        success: function success(response) {
          $root.find('.ld-gb-results').replaceWith(response.html);
          $root.find('.ld-gb-frontend-gradebook-list-container').each(function (index, element) {
            api.createList(element);
          });
          $gradebookDropdown.attr('disabled', false);
          $groupDropdown.attr('disabled', false);
        },
        error: function error(request, status, _error6) {
          $gradebookDropdown.attr('disabled', false);
          $groupDropdown.attr('disabled', false);
          var errorMessage = JSON.parse(request.responseText);
          if (typeof errorMessage.message !== 'undefined') {
            console.error(errorMessage.message);
          } else if (typeof errorMessage.exception !== 'undefined') {
            console.error(errorMessage.exception);
          }
          if (typeof errorMessage.html !== 'undefined') {
            $root.find('.ld-gb-results').prepend(errorMessage.html);
          }
        }
      });
    },
    /**
     * Hides the Gradebook Panel before showing the Edit Panel
     *
     * @param object      event       Event Object
     * @param integer     userID      User ID
     * @param integer     gradebookID Gradebook ID
     * @param integer     groupID     Group ID
     * @param string      gradeFormat Grade Display Format
     * @param object      $gradebook  jQuery Object for the Gradebook Panel
     * @param object      $editPanel  jQuery Object for the Edit Panel
     * @param object      Foundation  Foundation Object
     *
     * @param event
     * @param userID
     * @param gradebookID
     * @param groupID
     * @param gradeFormat
     * @param $gradebook
     * @param $editPanel
     * @param Foundation
     * @since   2.0.0
     * @return  void
     */
    hideGradebook: function hideGradebook(event, userID, gradebookID, groupID, gradeFormat, $gradebook, $editPanel, Foundation) {
      Foundation.Motion.animateOut($gradebook, 'slide-out-left', function () {
        $(document).trigger('ld-gb-frontend-gradebook-loading-edit-panel', [userID, gradebookID, groupID, gradeFormat, $gradebook, $editPanel, Foundation]);
      });
    },
    /**
     * Loads the Edit Panel from the server
     *
     * @param object      event       Event Object
     * @param integer     userID      User ID
     * @param integer     gradebookID Gradebook ID
     * @param integer     groupID     Group ID
     * @param string      gradeFormat Grade Display Format
     * @param object      $gradebook  jQuery Object for the Gradebook Panel
     * @param object      $editPanel  jQuery Object for the Edit Panel
     * @param object      Foundation  Foundation Object
     *
     * @param event
     * @param userID
     * @param gradebookID
     * @param groupID
     * @param gradeFormat
     * @param $gradebook
     * @param $editPanel
     * @param Foundation
     * @since   2.0.0
     * @return  void
     */
    loadEditPanel: function loadEditPanel(event, userID, gradebookID, groupID, gradeFormat, $gradebook, $editPanel, Foundation) {
      $editPanel.find('.loading-text').show();
      $.ajax({
        url: l10n.rest + 'get-frontend-user-grades/' + userID + '/' + gradebookID + '/' + groupID,
        method: 'GET',
        cache: false,
        headers: {
          'X-WP-Nonce': l10n.nonce
        },
        success: function success(response) {
          // Remove any old overlays
          $('.reveal-overlay .ld-gb-frontend-gradebook-overlay').remove();
          $editPanel.replaceWith(response.html);

          // We have to refresh the variable otherwise it will point to the old DOM which no longer exists
          $editPanel = $gradebook.next('.ld-gb-frontend-gradebook-edit-panel').first();
          $(document).trigger('ld-gb-frontend-gradebook-loaded-edit-panel', [userID, gradebookID, groupID, gradeFormat, $gradebook, $editPanel, Foundation]);
        },
        error: function error(request, status, _error7) {
          $editPanel.find('.loading-text').hide();

          // Show the Gradebook again
          $(document).trigger('ld-gb-frontend-gradebook-cleared-edit-panel', [userID, gradebookID, groupID, gradeFormat, $gradebook, $editPanel, Foundation]);
          var errorMessage = JSON.parse(request.responseText);
          if (typeof errorMessage.message !== 'undefined') {
            console.error(errorMessage.message);
          } else if (typeof errorMessage.exception !== 'undefined') {
            console.error(errorMessage.exception);
          }
          if (typeof errorMessage.html !== 'undefined') {
            $gradebook.prepend(errorMessage.html);
          }
        }
      });
    },
    /**
     * Shows the Edit Panel after it has been populated
     *
     * @param object      event       Event Object
     * @param integer     userID      User ID
     * @param integer     gradebookID Gradebook ID
     * @param integer     groupID     Group ID
     * @param string      gradeFormat Grade Display Format
     * @param object      $gradebook  jQuery Object for the Gradebook Panel
     * @param object      $editPanel  jQuery Object for the Edit Panel
     * @param object      Foundation  Foundation Object
     *
     * @param event
     * @param userID
     * @param gradebookID
     * @param groupID
     * @param gradeFormat
     * @param $gradebook
     * @param $editPanel
     * @param Foundation
     * @since   2.0.0
     * @return  void
     */
    showEditPanel: function showEditPanel(event, userID, gradebookID, groupID, gradeFormat, $gradebook, $editPanel, Foundation) {
      Foundation.Motion.animateIn($editPanel.find('.ld-gb-frontend-gradebook-edit-panel-content'), 'slide-in-right', function () {
        $('html, body').animate({
          scrollTop: $editPanel.find('.ld-gb-frontend-gradebook-edit-panel-content')[0].offsetTop,
          behavior: 'smooth'
        });
      });
    },
    /**
     * Hides the Edit Panel before showing the Gradebook Panel
     *
     * @param object      event       Event Object
     * @param integer     userID      User ID
     * @param integer     gradebookID Gradebook ID
     * @param integer     groupID     Group ID
     * @param string      gradeFormat Grade Display Format
     * @param object      $gradebook  jQuery Object for the Gradebook Panel
     * @param object      $editPanel  jQuery Object for the Edit Panel
     * @param object      Foundation  Foundation Object
     *
     * @param event
     * @param userID
     * @param gradebookID
     * @param groupID
     * @param gradeFormat
     * @param $gradebook
     * @param $editPanel
     * @param Foundation
     * @since   2.0.0
     * @return  void
     */
    hideEditPanel: function hideEditPanel(event, userID, gradebookID, groupID, gradeFormat, $gradebook, $editPanel, Foundation) {
      Foundation.Motion.animateOut($editPanel.find('.ld-gb-frontend-gradebook-edit-panel-content'), 'slide-out-right', function () {
        $(document).trigger('ld-gb-frontend-gradebook-clearing-edit-panel', [parseInt($(this).data('user_id')), parseInt($(this).data('gradebook_id')), parseInt($(this).data('group_id')), $(this).data('grade_format'), $gradebook, $editPanel, Foundation]);
        $(document).trigger('ld-gb-frontend-gradebook-cleared-edit-panel', [parseInt($(this).data('user_id')), parseInt($(this).data('gradebook_id')), parseInt($(this).data('group_id')), $(this).data('grade_format'), $gradebook, $editPanel, Foundation]);
      });
    },
    /**
     * Shows the Gradebook Panel after the Edit Panel has been cleared
     *
     * @param object      event       Event Object
     * @param integer     userID      User ID
     * @param integer     gradebookID Gradebook ID
     * @param integer     groupID     Group ID
     * @param string      gradeFormat Grade Display Format
     * @param object      $gradebook  jQuery Object for the Gradebook Panel
     * @param object      $editPanel  jQuery Object for the Edit Panel
     * @param object      Foundation  Foundation Object
     *
     * @param event
     * @param userID
     * @param gradebookID
     * @param groupID
     * @param gradeFormat
     * @param $gradebook
     * @param $editPanel
     * @param Foundation
     * @since   2.0.0
     * @return  void
     */
    showGradebook: function showGradebook(event, userID, gradebookID, groupID, gradeFormat, $gradebook, $editPanel, Foundation) {
      Foundation.Motion.animateIn($gradebook, 'slide-in-left', function () {
        $('html, body').animate({
          scrollTop: $gradebook[0].offsetTop,
          behavior: 'smooth'
        });
      });
    },
    /**
     * These tasks are done for all Grade manipulation, so this helps us DRY it up a little
     *
     * @param integer      userID       User ID
     * @param integer      componentID  Component ID
     * @param object       responseData Response Data object
     * @param object       $editPanel   jQuery Object for the Edit Panel
     *
     * @param userID
     * @param componentID
     * @param responseData
     * @param $editPanel
     * @since   2.0.0
     * @return  void
     */
    updateUserGrade: function updateUserGrade(userID, componentID, responseData, $editPanel) {
      // Update the Gradebook List to reflect any new Component Grades

      var list = $editPanel.prev('.gradebook-container').find('.ld-gb-frontend-gradebook-list-container').data('list-js'),
        values = {};
      var row = list.get('ID', parseInt(userID));
      if (typeof row[0] === 'undefined') {
        row = false;
      } else {
        row = row[0];
      }
      if (row) {
        values = row._values;
        responseData.user_row = $('<div />').html($.parseHTML(responseData.user_row)).contents();
        for (var key in row._values) {
          if (responseData.user_row.filter('.' + key)) {
            values[key] = responseData.user_row.filter('.' + key).html();
          }
        }
        row.values(values);
      }

      // Update the different aspects of the Edit Panel

      $editPanel.find('.ld-gb-frontend-gradebook-edit-panel-user-grade').replaceWith(responseData.edit_panel_user_grade);
      var $component = $editPanel.find('.ld-gb-frontend-gradebook-component[data-component="' + componentID + '"]');
      $component.find('.ld-gb-frontend-gradebook-component-grade-container').replaceWith(responseData.edit_panel_component_grade);
    },
    /**
     * Grabs formatted Gradebook Data for each additional Page of the Gradebook
     *
     * @param integer     currentPage  Current Page. Not 0-indexed
     * @param integer     totalPages   Total number of pages we need to iterate through
     * @param integer     perPage      Maximum number of results per-page
     * @param string      gradeFormat  Format to display the grades in. 'percentage' or 'letter'
     * @param object      list         List.js Object
     * @param integer     gradebookID  Gradebook ID
     * @param integer     groupID      Group ID
     * @param object      $container   jQuery object of the List.JS object
     *
     * @param currentPage
     * @param totalPages
     * @param perPage
     * @param gradeFormat
     * @param list
     * @param gradebookID
     * @param groupID
     * @param $container
     * @since   2.0.0
     * @return  void
     */
    getPage: function getPage(currentPage, totalPages, perPage, gradeFormat, list, gradebookID, groupID, $container) {
      var url = l10n.rest + 'get-formatted-gradebook-data/' + gradebookID + '/' + groupID + '/',
        queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_4__.getQueryString)(url);
      url = url.replace(queryString, '');
      queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_4__.updateURLParam)(queryString, 'paged', currentPage);
      queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_4__.updateURLParam)(queryString, 'per_page', perPage);
      queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_4__.updateURLParam)(queryString, 'grade_format', gradeFormat);
      url = url + queryString;
      $.ajax({
        url: url,
        method: 'GET',
        cache: false,
        headers: {
          'X-WP-Nonce': l10n.nonce
        },
        success: function success(response) {
          var pageData = response.grades;
          for (var userID in pageData) {
            list.add(pageData[userID]);
          }
          currentPage++;
          if (currentPage <= totalPages) {
            api.getPage(currentPage, totalPages, perPage, gradeFormat, list, gradebookID, groupID, $container);
          } else {
            $container.find('.pagination + .loading-icon').hide();
          }
        },
        error: function error(request, status, _error8) {
          var errorMessage = JSON.parse(request.responseText);
          if (typeof errorMessage.message !== 'undefined') {
            console.error(errorMessage.message);
          } else if (typeof errorMessage.exception !== 'undefined') {
            console.error(errorMessage.exception);
          }
        }
      });
    },
    /**
     * Fires when a Gradebook Component CSV Export is requested
     *
     * @param object event  JavaScript Event Object
     *
     * @param event
     * @since   2.0.0
     * @return  void
     */
    gradebook_export_components_submit: function gradebook_export_components_submit(event) {
      event.preventDefault();
      var $submitButton = $(this).find('input[type="submit"]');
      $submitButton.data('default_text', $submitButton.val());
      $submitButton.val($submitButton.data('submitting_text')).attr('disabled', true);
      var csv = '',
        gradebook_id = parseInt($(this).find('input[type="submit"]').data('gradebook_id')),
        group_id = parseInt($(this).find('input[type="submit"]').data('group_id')),
        perPage = $(this).closest('.ld-gb-results').find('.ld-gb-frontend-gradebook-list-container').data('per_page');
      api.get_csv_component_data(gradebook_id, group_id, this, csv, 1, perPage);
    },
    /**
     * Continue to hit the CSV Component Export Endpoint until there are no more results, then generate a download link for the complete results
     *
     * @param integer      gradebook_id  Gradebook ID
     * @param integer      group_id      Group ID. 0 for All Users
     * @param object       form          Form Object
     * @param string       csv           CSV String that is being built
     * @param integer      page          Page Number of results
     * @param integer      perPage       Per Page
     *
     * @param gradebook_id
     * @param group_id
     * @param form
     * @param csv
     * @param page
     * @param perPage
     * @since   2.0.0
     * @return  void
     */
    get_csv_component_data: function get_csv_component_data(gradebook_id, group_id, form, csv, page, perPage) {
      var url = l10n.rest + 'export-gradebook-component-data/' + gradebook_id + '/' + group_id + '/',
        queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_4__.getQueryString)(url);
      url = url.replace(queryString, '');
      queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_4__.updateURLParam)(queryString, 'paged', page);
      queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_4__.updateURLParam)(queryString, 'per_page', perPage);
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
            api.get_csv_component_data(gradebook_id, group_id, form, csv, parseInt(page) + 1, perPage);
          } else {
            // We've gathered every page
            api.generate_download(csv, 'gradebook-' + gradebook_id + '-' + group_id + '-components.csv');
            $(form).find('input[type="submit"]').val($(form).find('input[type="submit"]').data('default_text')).attr('disabled', false);
          }
        },
        error: function error(request, status, _error9) {
          var errorMessage = JSON.parse(request.responseText);
          if (typeof errorMessage.message !== 'undefined') {
            console.error(errorMessage.message);
          } else if (typeof errorMessage.exception !== 'undefined') {
            console.error(errorMessage.exception);
          }
          $(form).find('input[type="submit"]').val($(form).find('input[type="submit"]').data('default_text')).attr('disabled', false);
          if (typeof errorMessage.html !== 'undefined') {
            $(form).prepend(errorMessage.html);
          }
        }
      });
    },
    /**
     * Fires when a Gradebook All Grades CSV Export is requested
     *
     * @param object event  JavaScript Event Object
     *
     * @param event
     * @since   2.0.0
     * @return  void
     */
    gradebook_export_all_grades_submit: function gradebook_export_all_grades_submit(event) {
      event.preventDefault();
      var $submitButton = $(this).find('input[type="submit"]');
      $submitButton.data('default_text', $submitButton.val());
      $submitButton.val($submitButton.data('submitting_text')).attr('disabled', true);
      var csv = '',
        gradebook_id = parseInt($(this).find('input[type="submit"]').data('gradebook_id')),
        group_id = parseInt($(this).find('input[type="submit"]').data('group_id')),
        perPage = $(this).closest('.ld-gb-results').find('.ld-gb-frontend-gradebook-list-container').data('per_page');
      api.get_csv_all_grades_data(gradebook_id, group_id, $(this).closest('form')[0], csv, 1, perPage);
    },
    /**
     * Continue to hit the CSV All Grades Export Endpoint until there are no more results, then generate a download link for the complete results
     *
     * @param integer      gradebook_id  Gradebook ID
     * @param integer      group_id      Group ID. 0 for All Users
     * @param object       form          Form Object
     * @param string       csv           CSV String that is being built
     * @param integer      page          Page Number of results
     * @param integer      perPage       Per Page
     *
     * @param gradebook_id
     * @param group_id
     * @param form
     * @param csv
     * @param page
     * @param perPage
     * @since   2.0.0
     * @return  void
     */
    get_csv_all_grades_data: function get_csv_all_grades_data(gradebook_id, group_id, form, csv, page, perPage) {
      var url = l10n.rest + 'export-gradebook-all-grades/' + gradebook_id + '/' + group_id + '/',
        queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_4__.getQueryString)(url);
      url = url.replace(queryString, '');
      queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_4__.updateURLParam)(queryString, 'paged', page);
      queryString = (0,_lib_update_url__WEBPACK_IMPORTED_MODULE_4__.updateURLParam)(queryString, 'per_page', perPage);
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
            api.get_csv_all_grades_data(gradebook_id, group_id, form, csv, parseInt(page) + 1, perPage);
          } else {
            // We've gathered every page
            api.generate_download(csv, 'gradebook-' + gradebook_id + '-' + group_id + '-all-grades.csv');
            $(form).find('input[type="submit"]').val($(form).find('input[type="submit"]').data('default_text')).attr('disabled', false);
          }
        },
        error: function error(request, status, _error10) {
          var errorMessage = JSON.parse(request.responseText);
          if (typeof errorMessage.message !== 'undefined') {
            console.error(errorMessage.message);
          } else if (typeof errorMessage.exception !== 'undefined') {
            console.error(errorMessage.exception);
          }
          $(form).find('input[type="submit"]').val($(form).find('input[type="submit"]').data('default_text')).attr('disabled', false);
          if (typeof errorMessage.html !== 'undefined') {
            $(form).prepend(errorMessage.html);
          }
        }
      });
    },
    /**
     * Generates a download and forces a click on the element
     *
     * @param string      contents     File Contents
     * @param string      filename     File Name
     * @param string      contentType  Content Type
     *
     * @param contents
     * @param filename
     * @param contentType
     * @since   2.0.0
     * @return  void
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
     * @param string str  String to encode
     *
     * @param str
     * @return  string       Encoded string
     */
    rfc3986EncodeURIComponent: function rfc3986EncodeURIComponent(str) {
      return encodeURIComponent(str).replace(/[!'()*]/g, function (c) {
        return '%' + c.charCodeAt(0).toString(16);
      }).replace(/\u00C0-\u024F\u1E00-\u1EFF/, function (c) {
        return '%' + c.charCodeAt(0).toString(8);
      });
    },
    /**
     * Creates a List.JS for the given Element
     * This should be the <div> holding the <table> and Pagination
     *
     * @param object  element  DOM Object
     *
     * @param element
     * @since   2.0.0
     * @return  void
     */
    createList: function createList(element) {
      var $templateSource = $(element).find('.' + l10n.ldFrontendGradebookTableListOptions.listClass + ' .dummy'),
        $template = $templateSource.clone();
      $template.removeClass('dummy').wrap('<div></div>');
      var templateString = $template.parent().html();
      $templateSource.remove();
      var valueNames = l10n.ldFrontendGradebookTableListOptions.valueNames;
      $(element).find('th').each(function (index, th) {
        valueNames.push($(th).data('column_name'));
      });
      var perPage = $(element).data('per_page');
      if (!perPage) {
        perPage = l10n.ldFrontendGradebookTableListOptions.page;
      }
      var list = new (list_js__WEBPACK_IMPORTED_MODULE_0___default())(element, Object.assign(l10n.ldFrontendGradebookTableListOptions, {
        item: templateString,
        valueNames: valueNames,
        page: perPage,
        sortFunction: function sortFunction(itemA, itemB, options) {
          var sort = naturalSort;
          sort.alphabet = options.alphabet || undefined;
          if (!sort.alphabet && options.insensitive) {
            sort = naturalSort.caseInsensitive;
          }
          var $itemA = $($.parseHTML(itemA.values()[options.valueName])),
            $itemB = $($.parseHTML(itemB.values()[options.valueName]));
          $itemA.find('div').remove();
          $itemB.find('div').remove();
          return sort($itemA.text().trim(), $itemB.text().trim());
        }
      }));
      $(element).data('list-js', list);
      $(document).trigger('ld-gb-frontend-gradebook-list-created', [list, element]);
    }
  };
  api.init();
})(jQuery, LD_GB_FrontendGradebook.l10n, LD_GB_FrontendGradebook.i18n);
})();

/******/ })()
;