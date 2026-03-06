<?php
/**
 * Number utility class file.
 *
 * @since 5.9.12
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Utilities;

use LearnDash\Core\Utilities\Cast;

/**
 * Number utility class.
 *
 * Handles number formatting and sanitization with locale-aware support.
 *
 * @since 5.9.12
 */
class Number {
	/**
	 * Sanitize a decimal value, handling both comma and dot decimal separators.
	 *
	 * This method normalizes locale-specific decimal separators (e.g., European comma format)
	 * to the standard dot format before sanitizing the float value.
	 *
	 * Handles various formats intelligently:
	 * - US format: "1,234.56" (comma thousands, dot decimal)
	 * - European format: "1.234,56" (dot thousands, comma decimal)
	 * - Ambiguous: "1,000,00" (last comma treated as decimal)
	 *
	 * @since 5.9.12
	 *
	 * @param mixed $value  Value to sanitize (string, int, or float).
	 *
	 * @return float|string Sanitized decimal value, or empty string if invalid.
	 */
	public static function sanitize_decimal( $value ) {
		// Handle non-scalar values.
		if ( ! is_scalar( $value ) ) {
			return '';
		}

		// Handle boolean false specifically (converts to 0).
		if ( false === $value ) {
			return 0.0;
		}

		// Return empty string if value is empty or null (but allow 0 and false).
		if ( empty( $value ) && '0' !== $value && 0 !== $value ) {
			return '';
		}

		// If already a valid number, return it.
		if ( is_numeric( $value ) && ! is_string( $value ) ) {
			return floatval( $value );
		}

		// Convert to string for processing.
		$string_value = trim( (string) $value );

		// Remove spaces (used as thousands separator in some locales like French).
		$string_value = str_replace( ' ', '', $string_value );

		// Find the last occurrence of comma and dot.
		$last_comma = strrpos( $string_value, ',' );
		$last_dot   = strrpos( $string_value, '.' );

		// Determine which is the decimal separator based on which comes last.
		if ( false !== $last_comma && false !== $last_dot ) {
			if ( $last_comma > $last_dot ) {
				// Comma is the decimal separator (European format: 1.234,56 or ambiguous like 1,000,00).
				// Remove all dots (thousands separators).
				$string_value = str_replace( '.', '', $string_value );
				// Replace only the last comma with a dot.
				$last_comma_pos = strrpos( $string_value, ',' );
				if ( false !== $last_comma_pos ) {
					$string_value = substr_replace( $string_value, '.', $last_comma_pos, 1 );
				}
			} else {
				// Dot is the decimal separator (US format: 1,234.56).
				// Remove all commas (thousands separators).
				$string_value = str_replace( ',', '', $string_value );
			}
		} elseif ( false !== $last_comma ) {
			// Only comma(s) exist - treat last one as decimal separator.
			// Replace only the last comma with a dot.
			$last_comma_pos = strrpos( $string_value, ',' );
			if ( false !== $last_comma_pos ) {
				$string_value = substr_replace( $string_value, '.', $last_comma_pos, 1 );
			}
		} elseif ( false !== $last_dot ) {
			// Only dot(s) exist.
			$dot_count = substr_count( $string_value, '.' );
			if ( $dot_count > 1 ) {
				// Multiple dots - need to determine if these are thousands separators or invalid input.
				// Check if this looks like thousands separator format (groups of 3 digits).
				$parts                = explode( '.', $string_value );
				$looks_like_thousands = true;
				$parts_count          = count( $parts );

				// Check all parts except first and last.
				for ( $i = 1; $i < $parts_count - 1; $i++ ) {
					if ( strlen( $parts[ $i ] ) !== 3 ) {
						$looks_like_thousands = false;
						break;
					}
				}

				// Also check that last part has 0-2 digits (decimal part).
				if ( strlen( end( $parts ) ) > 2 ) {
					$looks_like_thousands = false;
				}

				if ( $looks_like_thousands ) {
					// Treat last dot as decimal, remove all others.
					// Example: "1.000.00" → "1000.00".
					$string_value = strrev( Cast::to_string( preg_replace( '/\./', '@', strrev( $string_value ), 1 ) ) );
					$string_value = str_replace( '.', '', $string_value );
					$string_value = str_replace( '@', '.', $string_value );
				} else {
					// Otherwise, keep everything up to and including the first dot.
					// Example: "50.29.30" → salvage "50.29".
					$first_dot_pos  = strpos( $string_value, '.' );
					$second_dot_pos = strpos( $string_value, '.', $first_dot_pos + 1 );
					if ( false !== $second_dot_pos ) {
						$string_value = substr( $string_value, 0, $second_dot_pos );
					}
				}
			}
			// Single dot - leave it as is.
		}
		// If neither exist, no normalization needed.

		// Remove any characters that aren't digits, dots, plus, minus signs, or scientific notation (e/E).
		$normalized_value = Cast::to_string( preg_replace( '/[^\d.\-+eE]/', '', $string_value ) );

		// Validate that we have at most one decimal point (after normalization).
		$dot_count_after = substr_count( $normalized_value, '.' );
		if ( $dot_count_after > 1 ) {
			// Multiple dots remaining - take everything before the second dot.
			$first_dot  = strpos( $normalized_value, '.' );
			$second_dot = strpos( $normalized_value, '.', $first_dot + 1 );
			if ( false !== $second_dot ) {
				$normalized_value = substr( $normalized_value, 0, $second_dot );
			}
		}

		// Convert to float and return.
		$float_value = filter_var( $normalized_value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

		// Return the validated float or empty string if invalid.
		return ( false !== $float_value ) ? $float_value : '';
	}
}
