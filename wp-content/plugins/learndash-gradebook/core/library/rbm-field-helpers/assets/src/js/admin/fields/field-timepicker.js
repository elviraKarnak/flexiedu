import Field from './field.js';
import FieldsInitialize from '../fields-init.js';

/**
 * Time Picker Field functionality.
 *
 * Also includes Date/Time Picker and Time Picker.
 *
 * @since 1.4.0
 */
class FieldTimePicker extends Field {

    /**
     * Class constructor.
     *
     * @since 1.4.0
     */
    constructor($field) {

        super($field, 'timepicker');

        this.initField();
    }

    /**
     * Initializes the Time Picker.
     *
     * @since 1.4.0
     */
    initField() {

        let option_functions = ['onChange', 'onOpen', 'onClose', 'onMonthChange', 'onYearChange', 'onReady', 'onValueUpdate', 'onDayCreate'];

        // Function support
        jQuery.each(this.options.timepickerOptions, (name, value) => {

            if ( option_functions.indexOf(name) !== -1 &&
                !jQuery.isFunction(this.options.timepickerOptions[name]) &&
                jQuery.isFunction(window[value]) ) {

                this.options.timepickerOptions[name] = window[value];
            }
        });

        // We need to ensure that the field instance for our specific field loads its default date in properly
        this.options.timepickerOptions.defaultDate = this.$field.data( 'defaultdate' );

        let value = this.$field.val();

        if ( ! value ) value = this.options.timepickerOptions.defaultDate;

        this.flatpickr = this.$field.flatpickr(this.options.timepickerOptions);

        this.flatpickr.setDate( value, true );
    
    }

    /**
     * Cleans up after a repeater add/init.
     *
     * @since 1.4.0
     */
    fieldCleanup() {

        let value = this.$field.val();

        if ( ! value ) value = this.options.timepickerOptions.defaultDate;

        if ( typeof this.flatpickr !== 'undefined' ) {

            this.flatpickr.destroy();

        }

        this.$field.val( value );

    }
    
    /**
     * Runs cleanup before the Repeater creates a dummy row to ensure we do not get weird double inputs
     *
     * @param   {object}  $repeater  jQuery DOM Object
     * @param   {array}  options     Array of Field Options
     *
     * @since   1.5.0
     * @return  void
     */
    repeaterBeforeInit( $repeater, options ) {

        this.fieldCleanup();

    }

    /**
     * Ensure that the purposefully unloaded Flatpickr reloads
     *
     * @param   {object}  $repeater  jQuery DOM Object
     * @param   {array}  options     Array of Field Options
     *
     * @since   1.5.0
     * @return  void
     */
    repeaterOnInit( $repeater, options ) {

        this.initField();

    }

}

/**
 * Finds and initializes all Time Picker fields.
 *
 * @since 1.4.0
 */
class FieldTimePickerInitialize {

    /**
     * Class constructor.
     *
     * @since 1.4.0
     *
     * @param {jQuery} $root Root element to initialize fields inside.
     */
    constructor($root) {

        const api = this;

        this.fields = [];

        let $fields = $root.find('[data-fieldhelpers-field-timepicker]');

        if ( $fields.length ) {

            if ( !jQuery.isFunction(jQuery.fn.flatpickr) ) {

                console.error('Field Helpers Error: Trying to initialize Time Picker field but ' +
                    '"flatpickr" is not enqueued.');
                return;
            }

            $fields.each(function () {

                api.initializeField(jQuery(this));
            });
        }
    }

    /**
     * Initializes the field.
     *
     * @since 1.4.0
     *
     * @param {jQuery} $field
     */
    initializeField($field) {

        this.fields.push({
            $field,
            api: new FieldTimePicker($field),
        });
    }
}

export default FieldTimePickerInitialize;