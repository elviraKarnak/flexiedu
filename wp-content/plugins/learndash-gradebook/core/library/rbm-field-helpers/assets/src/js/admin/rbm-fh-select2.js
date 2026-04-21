// This file is combined with Select2 on-build to rename Select2 or our purposes
(function (factory) {
	
    var existingVersion = jQuery.fn.select2 || null;

    if (existingVersion) {
        delete jQuery.fn.select2;
    }

    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = function (root, jQuery) {

            if (jQuery === undefined) {
                // require('jQuery') returns a factory that requires window to
                // build a jQuery instance, we normalize how we use modules
                // that require this pattern but the window provided is a noop
                // if it's defined (how jquery works)
                if (typeof window !== 'undefined') {
                    jQuery = require('jquery');
                }
                else {
                    jQuery = require('jquery')(root);
                }
            }

            factory(jQuery);

            return jQuery;
        };
    } else {
        // Browser globals
        factory(jQuery);
    }

    jQuery.fn.rbmfhselect2 = jQuery.fn.select2;

    if (existingVersion) {
        delete jQuery.fn.select2;
        jQuery.fn.select2 = existingVersion;
    }

}