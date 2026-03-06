/*This file contains the code to convert WC_PRICE function of php to javascript */
function number_format(number, decimals, dec_point, thousands_sep) {
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
		precision = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = typeof thousands_sep === 'undefined' ? ',' : thousands_sep,
		dec = typeof dec_point === 'undefined' ? '.' : dec_point,
		s = '',
		toFixedFix = function (n, precision_param) {
			var k = Math.pow(10, precision_param);
			return '' + (Math.round(n * k) / k).toFixed(precision_param);
		};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (precision ? toFixedFix(n, precision) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < precision) {
		s[1] = s[1] || '';
		s[1] += new Array(precision - s[1].length + 1).join('0');
	}
	return s.join(dec);
}

function preg_quote(str, delimiter) {
	return String(str).replace(
		new RegExp(
			'[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]',
			'g'
		),
		'\\$&'
	);
}

function sprintf() {
	var regex =
		/%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuideEfFgG])/g; // cspell:disable-line
	var a = arguments;
	var i = 0;
	var format = a[i++];

	// pad()
	var pad = function (str, len, chr, leftJustify) {
		if (!chr) {
			chr = ' ';
		}
		var padding =
			str.length >= len
				? ''
				: new Array((1 + len - str.length) >>> 0).join(chr);
		return leftJustify ? str + padding : padding + str;
	};

	// justify()
	var justify = function (
		value,
		prefix,
		leftJustify,
		minWidth,
		zeroPad,
		customPadChar
	) {
		var diff = minWidth - value.length;
		if (diff > 0) {
			if (leftJustify || !zeroPad) {
				value = pad(value, minWidth, customPadChar, leftJustify);
			} else {
				value =
					value.slice(0, prefix.length) +
					pad('', diff, '0', true) +
					value.slice(prefix.length);
			}
		}
		return value;
	};

	// formatBaseX()
	var formatBaseX = function (
		value,
		base,
		prefix,
		leftJustify,
		minWidth,
		precision,
		zeroPad
	) {
		// Note: casts negative numbers to positive ones
		var number = value >>> 0;
		prefix =
			(prefix &&
				number &&
				{
					2: '0b',
					8: '0',
					16: '0x',
				}[base]) ||
			'';
		value = prefix + pad(number.toString(base), precision || 0, '0', false);
		return justify(value, prefix, leftJustify, minWidth, zeroPad);
	};

	// formatString()
	var formatString = function (
		value,
		leftJustify,
		minWidth,
		precision,
		zeroPad,
		customPadChar
	) {
		if (precision != null) {
			value = value.slice(0, precision);
		}
		return justify(
			value,
			'',
			leftJustify,
			minWidth,
			zeroPad,
			customPadChar
		);
	};

	// doFormat()
	var doFormat = function (
		substring,
		valueIndex,
		flags,
		minWidth,
		_,
		precision,
		type
	) {
		var number, prefix, method, textTransform, value;

		if (substring === '%%') {
			return '%';
		}

		// parse flags
		var leftJustify = false;
		var positivePrefix = '';
		var zeroPad = false;
		var prefixBaseX = false;
		var customPadChar = ' ';
		var flags_l = flags.length;
		for (var j = 0; flags && j < flags_l; j++) {
			switch (flags.charAt(j)) {
				case ' ':
					positivePrefix = ' ';
					break;
				case '+':
					positivePrefix = '+';
					break;
				case '-':
					leftJustify = true;
					break;
				case "'":
					customPadChar = flags.charAt(j + 1);
					break;
				case '0':
					zeroPad = true;
					customPadChar = '0';
					break;
				case '#':
					prefixBaseX = true;
					break;
			}
		}

		// parameters may be null, undefined, empty-string or real valued
		// we want to ignore null, undefined and empty-string values
		if (!minWidth) {
			minWidth = 0;
		} else if (minWidth === '*') {
			minWidth = +a[i++];
		} else if (minWidth.charAt(0) == '*') {
			minWidth = +a[minWidth.slice(1, -1)];
		} else {
			minWidth = +minWidth;
		}

		// Note: undocumented perl feature:
		if (minWidth < 0) {
			minWidth = -minWidth;
			leftJustify = true;
		}

		if (!isFinite(minWidth)) {
			throw new Error('sprintf: (minimum-)width must be finite');
		}

		if (!precision) {
			precision =
				'fFeE'.indexOf(type) > -1 ? 6 : type === 'd' ? 0 : undefined;
		} else if (precision === '*') {
			precision = +a[i++];
		} else if (precision.charAt(0) == '*') {
			precision = +a[precision.slice(1, -1)];
		} else {
			precision = +precision;
		}

		// grab value using valueIndex if required?
		value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

		switch (type) {
			case 's':
				return formatString(
					String(value),
					leftJustify,
					minWidth,
					precision,
					zeroPad,
					customPadChar
				);
			case 'c':
				return formatString(
					String.fromCharCode(+value),
					leftJustify,
					minWidth,
					precision,
					zeroPad
				);
			case 'b':
				return formatBaseX(
					value,
					2,
					prefixBaseX,
					leftJustify,
					minWidth,
					precision,
					zeroPad
				);
			case 'o':
				return formatBaseX(
					value,
					8,
					prefixBaseX,
					leftJustify,
					minWidth,
					precision,
					zeroPad
				);
			case 'x':
				return formatBaseX(
					value,
					16,
					prefixBaseX,
					leftJustify,
					minWidth,
					precision,
					zeroPad
				);
			case 'X':
				return formatBaseX(
					value,
					16,
					prefixBaseX,
					leftJustify,
					minWidth,
					precision,
					zeroPad
				).toUpperCase();
			case 'u':
				return formatBaseX(
					value,
					10,
					prefixBaseX,
					leftJustify,
					minWidth,
					precision,
					zeroPad
				);
			case 'i':
			case 'd':
				number = +value || 0;
				number = Math.round(number - (number % 1)); // Plain Math.round doesn't just truncate
				prefix = number < 0 ? '-' : positivePrefix;
				value =
					prefix +
					pad(String(Math.abs(number)), precision, '0', false);
				return justify(value, prefix, leftJustify, minWidth, zeroPad);
			case 'e':
			case 'E':
			case 'f': // Should handle locales (as per setlocale)
			case 'F':
			case 'g':
			case 'G':
				number = +value;
				prefix = number < 0 ? '-' : positivePrefix;
				method = ['toExponential', 'toFixed', 'toPrecision'][
					'efg'.indexOf(type.toLowerCase())
				];
				textTransform = ['toString', 'toUpperCase'][
					'eEfFgG'.indexOf(type) % 2
				];
				value = prefix + Math.abs(number)[method](precision);
				return justify(value, prefix, leftJustify, minWidth, zeroPad)[
					textTransform
				]();
			default:
				return substring;
		}
	};

	return format.replace(regex, doFormat);
}

function empty(mixedVar) {
	//  discuss at: http://locutus.io/php/empty/
	// cspell:disable-next-line .
	// original by: Philippe Baumann
	// cspell:disable-next-line .
	//    input by: Onno Marsman (https://twitter.com/onnomarsman)
	//    input by: LH
	// cspell:disable-next-line .
	//    input by: Stoyan Kyosev (http://www.svest.org/)
	// cspell:disable-next-line .
	// bugfixed by: Kevin van Zonneveld (http://kvz.io)
	// cspell:disable-next-line .
	// improved by: Onno Marsman (https://twitter.com/onnomarsman)
	// cspell:disable-next-line .
	// improved by: Francesco
	// improved by: Marc Jansen
	// cspell:disable-next-line .
	// improved by: Rafał Kukawski (http://blog.kukawski.pl)
	//   example 1: empty(null)
	//   returns 1: true
	//   example 2: empty(undefined)
	//   returns 2: true
	//   example 3: empty([])
	//   returns 3: true
	//   example 4: empty({})
	//   returns 4: true
	// cspell:disable-next-line .
	//   example 5: empty({'aFunc' : function () { alert('humpty'); } })
	//   returns 5: false

	var undef;
	var key;
	var i;
	var len;
	var emptyValues = [undef, null, false, 0, '', '0'];

	for (i = 0, len = emptyValues.length; i < len; i++) {
		if (mixedVar === emptyValues[i]) {
			return true;
		}
	}

	if (typeof mixedVar === 'object') {
		for (key in mixedVar) {
			if (mixedVar.hasOwnProperty(key)) {
				return false;
			}
		}
		return true;
	}

	return false;
}

function WcFormatPrice(price) {
	decimal_separator = wdm_functions_data.decimal_separator;
	thousand_separator = wdm_functions_data.thousand_separator;
	decimals = wdm_functions_data.decimals;
	price_format = wdm_functions_data.price_format;

	negative = price < 0;
	price = parseFloat(negative ? price * -1 : price) || 0;
	price = number_format(
		price,
		decimals,
		decimal_separator,
		thousand_separator
	);

	if (decimals > 0) {
		price = price.replace(
			'/' + preg_quote(decimal_separator, '/') + '0++$/',
			''
		);
		//price = preg_replace( '/' + preg_quote( decimal_separator, '/' ) + '0++$/', '', price );
	}

	return (
		(negative ? '-' : '') +
		sprintf(price_format, wdm_functions_data.currency_symbol, price)
	);
}
