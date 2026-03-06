<?php
/**
 * Translation utility class file.
 *
 * @since 5.9.5
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Utilities;

/**
 * Translation utility class.
 *
 * @since 5.9.5
 */
class Translation {
	/**
	 * Get the dayjs locale object used to translate the JS packages that use dayjs.
	 *
	 * @since 5.9.5
	 *
	 * @return array{
	 *     name: string,
	 *     weekdays: string[],
	 *     months: string[],
	 *     weekdaysShort: string[],
	 *     monthsShort: string[],
	 *     weekdaysMin: string[],
	 * } The dayjs locale.
	 */
	public static function get_dayjs_locale(): array {
		return [
			'name'          => get_locale(),
			'weekdays'      => self::get_days_names(),
			'months'        => self::get_months_names(),
			'weekdaysShort' => self::get_days_names( 'short' ),
			'monthsShort'   => self::get_months_names( 'short' ),
			'weekdaysMin'   => self::get_days_names( 'min' ),
		];
	}

	/**
	 * Returns Apex Charts locale object.
	 *
	 * @since 5.9.5
	 *
	 * @return array{
	 *     name: string,
	 *     options: array{
	 *         months: string[],
	 *         shortMonths: string[],
	 *         days: string[],
	 *         shortDays: string[],
	 *         toolbar: array<string, string>,
	 *     }
	 * }
	 */
	public static function get_apex_charts_locale(): array {
		return [
			'name'    => get_locale(),
			'options' => [
				'months'      => self::get_months_names(),
				'shortMonths' => self::get_months_names( 'short' ),
				'days'        => self::get_days_names(),
				'shortDays'   => self::get_days_names( 'short' ),
				'toolbar'     => [
					'exportToSVG'   => __( 'Download SVG', 'wdm_instructor_role' ),
					'exportToPNG'   => __( 'Download PNG', 'wdm_instructor_role' ),
					'menu'          => __( 'Menu', 'wdm_instructor_role' ),
					'selection'     => __( 'Selection', 'wdm_instructor_role' ),
					'selectionZoom' => __( 'Selection Zoom', 'wdm_instructor_role' ),
					'zoomIn'        => __( 'Zoom In', 'wdm_instructor_role' ),
					'zoomOut'       => __( 'Zoom Out', 'wdm_instructor_role' ),
					'pan'           => __( 'Panning', 'wdm_instructor_role' ),
					'reset'         => __( 'Reset Zoom', 'wdm_instructor_role' ),
				],
			],
		];
	}

	/**
	 * Returns the days names.
	 *
	 * @since 5.9.5
	 *
	 * @param string $output_type The output type of days' names. It can either be 'normal', 'short', or 'min'. The default is 'normal'.
	 *
	 * @return string[]
	 */
	public static function get_days_names( string $output_type = 'normal' ): array {
		switch ( $output_type ) {
			case 'short':
				$days = [
					_x( 'Sun', 'The short day name', 'wdm_instructor_role' ),
					_x( 'Mon', 'The short day name', 'wdm_instructor_role' ),
					_x( 'Tue', 'The short day name', 'wdm_instructor_role' ),
					_x( 'Wed', 'The short day name', 'wdm_instructor_role' ),
					_x( 'Thu', 'The short day name', 'wdm_instructor_role' ),
					_x( 'Fri', 'The short day name', 'wdm_instructor_role' ),
					_x( 'Sat', 'The short day name', 'wdm_instructor_role' ),
				];
				break;

			case 'min':
				$days = [
					_x( 'Su', 'The shortest day name', 'wdm_instructor_role' ),
					_x( 'Mo', 'The shortest day name', 'wdm_instructor_role' ),
					_x( 'Tu', 'The shortest day name', 'wdm_instructor_role' ),
					_x( 'We', 'The shortest day name', 'wdm_instructor_role' ),
					_x( 'Th', 'The shortest day name', 'wdm_instructor_role' ),
					_x( 'Fr', 'The shortest day name', 'wdm_instructor_role' ),
					_x( 'Sa', 'The shortest day name', 'wdm_instructor_role' ),
				];
				break;

			case 'normal':
			default:
				$days = [
					_x( 'Sunday', 'The day name', 'wdm_instructor_role' ),
					_x( 'Monday', 'The day name', 'wdm_instructor_role' ),
					_x( 'Tuesday', 'The day name', 'wdm_instructor_role' ),
					_x( 'Wednesday', 'The day name', 'wdm_instructor_role' ),
					_x( 'Thursday', 'The day name', 'wdm_instructor_role' ),
					_x( 'Friday', 'The day name', 'wdm_instructor_role' ),
					_x( 'Saturday', 'The day name', 'wdm_instructor_role' ),
				];
				break;
		}

		/**
		 * Filters the names of the days.
		 *
		 * @since 5.9.5
		 *
		 * @param string[] $days        The days' names.
		 * @param string   $output_type The output type of days' names. It can either be 'normal', 'short', or 'min'. The default is 'normal'.
		 *
		 * @return string[] The days' names.
		 */
		return apply_filters(
			'learndash_instructor_role_days_names',
			$days,
			$output_type
		);
	}

	/**
	 * Returns the names of the months.
	 *
	 * @since 5.9.5
	 *
	 * @param string $output_type The output type of months' names. It can either be 'normal' or 'short'. The default is 'normal'.
	 *
	 * @return string[]
	 */
	public static function get_months_names( $output_type = 'normal' ): array {
		switch ( $output_type ) {
			case 'short':
				$months = [
					_x( 'Jan', 'The short month name', 'wdm_instructor_role' ),
					_x( 'Feb', 'The short month name', 'wdm_instructor_role' ),
					_x( 'Mar', 'The short month name', 'wdm_instructor_role' ),
					_x( 'Apr', 'The short month name', 'wdm_instructor_role' ),
					_x( 'May', 'The short month name', 'wdm_instructor_role' ),
					_x( 'Jun', 'The short month name', 'wdm_instructor_role' ),
					_x( 'Jul', 'The short month name', 'wdm_instructor_role' ),
					_x( 'Aug', 'The short month name', 'wdm_instructor_role' ),
					_x( 'Sep', 'The short month name', 'wdm_instructor_role' ),
					_x( 'Oct', 'The short month name', 'wdm_instructor_role' ),
					_x( 'Nov', 'The short month name', 'wdm_instructor_role' ),
					_x( 'Dec', 'The short month name', 'wdm_instructor_role' ),
				];
				break;

			case 'normal':
			default:
				$months = [
					_x( 'January', 'The month name', 'wdm_instructor_role' ),
					_x( 'February', 'The month name', 'wdm_instructor_role' ),
					_x( 'March', 'The month name', 'wdm_instructor_role' ),
					_x( 'April', 'The month name', 'wdm_instructor_role' ),
					_x( 'May', 'The month name', 'wdm_instructor_role' ),
					_x( 'June', 'The month name', 'wdm_instructor_role' ),
					_x( 'July', 'The month name', 'wdm_instructor_role' ),
					_x( 'August', 'The month name', 'wdm_instructor_role' ),
					_x( 'September', 'The month name', 'wdm_instructor_role' ),
					_x( 'October', 'The month name', 'wdm_instructor_role' ),
					_x( 'November', 'The month name', 'wdm_instructor_role' ),
					_x( 'December', 'The month name', 'wdm_instructor_role' ),
				];
				break;
		}

		/**
		 * Filters the names of the months.
		 *
		 * @since 5.9.5
		 *
		 * @param string[] $months      The months' names.
		 * @param string   $output_type The output type of months' names. It can either be 'normal' or 'short'. The default is 'normal'.
		 *
		 * @return string[] The months' names.
		 */
		return apply_filters(
			'learndash_instructor_role_months_names',
			$months,
			$output_type
		);
	}
}
