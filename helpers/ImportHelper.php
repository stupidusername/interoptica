<?php

namespace app\helpers;

class ImportHelper {
	
	/**
	 * Formats and encodes string values for db insert
	 * @param string $value
	 * @param string $fromEncoding
	 * @return string
	 */
	public static function processValue($value, $fromEncoding = 'ISO-8859-1') {
		return ucwords(mb_strtolower(mb_convert_encoding($value, 'UTF-8', $fromEncoding)), "*_-. \t\r\n\f\v");
	}
}
