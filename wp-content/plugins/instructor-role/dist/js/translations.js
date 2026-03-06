/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***************************************!*\
  !*** ./src/assets/js/translations.js ***!
  \***************************************/
// eslint-disable-next-line no-undef
window.learndash_instructor_role = window.learndash_instructor_role || {};

/**
 * Global translations object that contains translations related methods.
 *
 * @since 5.9.5
 */
window.learndash_instructor_role.translations = {
  /**
   * Get the corresponding JS locale from a WordPress locale.
   *
   * @since 5.9.5
   *
   * @param {string} wpLocale - WordPress locale.
   *
   * @return {string} - JS locale.
   */
  getJsLocaleFromWpLocale(wpLocale) {
    return wpLocale.replace('_', '-');
  }
};
/******/ })()
;