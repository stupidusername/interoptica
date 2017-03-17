<?php

namespace app\helpers;

class ImportHelper {
	
	const TO_ENCODING = 'UTF-8';
	
	/**
	 * Formats and encodes string values for db insert
	 * @param string $value
	 * @param string $fromEncoding
	 * @return string
	 */
	public static function processValue($value, $fromEncoding = 'ISO-8859-1') {
		if ($fromEncoding != self::TO_ENCODING) {
			$value = mb_convert_encoding($value, self::TO_ENCODING, $fromEncoding);
		}
		return ucwords(mb_strtolower(trim($value)), "*_-. \t\r\n\f\v");
	}
}
