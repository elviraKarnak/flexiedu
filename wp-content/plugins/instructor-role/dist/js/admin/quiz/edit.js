/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

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
/*!******************************************!*\
  !*** ./src/assets/js/admin/quiz/edit.js ***!
  \******************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/**
 * Handles Quiz Admin Edit functionality.
 *
 * @since 5.9.7
 */

const $ = jQuery;

const learndash = window.learndash || {};

// eslint-disable-next-line no-undef
learndash.instructorRole = learndash.instructorRole || {};

// eslint-disable-next-line no-undef
learndash.instructorRole.quiz = learndash.instructorRole.quiz || {};

// eslint-disable-next-line no-undef
learndash.instructorRole.quiz.edit = learndash.instructorRole.quiz.edit || {};
class QuizAdminEditHandler {
  /**
   * Initializes Quiz Admin Edit by setting up event listeners.
   *
   * @since 5.9.7
   *
   * @return {void}
   */
  init() {
    document.addEventListener('DOMContentLoaded', () => {
      this.actionsDropdown = document.querySelector('.ld-global-header-new-settings .ld-actions');
      this.loadFrontendEditorButtonNotice();
      this.actionsDropdown.addEventListener('click', () => {
        this.dismissFrontendEditorButtonNotice();
      });
      this.actionsDropdown.addEventListener('touch', () => {
        this.dismissFrontendEditorButtonNotice();
      });
    });
    window.addEventListener('resize', () => {
      this.loadFrontendEditorButtonNotice();
    });
    window.addEventListener('orientationchange', () => {
      this.loadFrontendEditorButtonNotice();
    });
  }

  /**
   * Wait for the Editor to be fully loaded.
   *
   * @since 5.9.7
   *
   * @return {Promise} A promise that resolves when the editor is ready.
   */
  waitForEditorReady() {
    return new Promise(resolve => {
      // Check if Gutenberg editor is available.
      const editorStore = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.select)('core/editor');
      if (!editorStore || typeof editorStore.__unstableIsEditorReady !== 'function') {
        // Gutenberg is not active, resolve immediately once Tabs are ready.
        let tabsReady;
        const interval = setInterval(() => {
          tabsReady = document.querySelector('.ld-tabs-ready');
          if (!tabsReady) {
            return;
          }
          clearInterval(interval);
          resolve();
        }, 10);
        return;
      }
      const closeListener = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.subscribe)(() => {
        const isReady = editorStore.__unstableIsEditorReady();
        if (!isReady) {
          return;
        }
        closeListener();

        // Request an additional animation frame to deal with a small jump on hard refresh.
        window.requestAnimationFrame(() => {
          resolve();
        });
      });
    }).then(() => {
      document.body.classList.add('ir-editor-ready');
    });
  }

  /**
   * Add a notice to the Actions dropdown saying that the Frontend Editor button has moved.
   *
   * @since 5.9.7
   *
   * @return {void}
   */
  loadFrontendEditorButtonNotice() {
    if (!learndash.instructorRole.quiz.edit?.frontendQuizEditorButtonMoved) {
      return;
    }
    this.waitForEditorReady().then(() => {
      if (!this.actionsDropdown || (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.select)('core/preferences').get('learndash/instructor-role/pointer', 'frontend-editor-button-moved')) {
        return;
      }
      const pointer = $(this.actionsDropdown).data('pointer');

      // Destroy the pointer if it exists so that we can re-initialize it.
      if (typeof pointer !== 'undefined') {
        $(this.actionsDropdown).pointer('destroy');
        $(this.actionsDropdown).data('pointer', null);
      }
      $(this.actionsDropdown).pointer({
        pointerClass: 'wp-pointer ir-pointer ir-pointer--within-ld-header',
        content: learndash.instructorRole.quiz.edit.frontendQuizEditorButtonMoved,
        position: {
          edge: 'top'
        },
        show: (event, pointerData) => {
          // Used for styling on mobile.
          $(pointerData.pointer).addClass('ir-pointer--initialized');
          $(pointerData.element).data('pointer', pointerData.pointer);
        },
        close: () => {
          (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.dispatch)('core/preferences').set('learndash/instructor-role/pointer', 'frontend-editor-button-moved', Date.now());
        }
      }).pointer('open');
    });
  }

  /**
   * Dismiss the Frontend Editor button notice when clicking or tapping the Actions dropdown.
   *
   * @since 5.9.7
   *
   * @return {void}
   */
  dismissFrontendEditorButtonNotice() {
    const pointer = $(this.actionsDropdown).data('pointer');
    if (typeof pointer === 'undefined') {
      return;
    }
    $(this.actionsDropdown).pointer('close');
  }
}

/**
 * Initializes Quiz Admin Edit functionality.
 *
 * @since 5.9.7
 *
 * @return {void}
 */
const QuizAdminEdit = () => {
  const handler = new QuizAdminEditHandler();
  handler.init();
};
QuizAdminEdit();
/******/ })()
;