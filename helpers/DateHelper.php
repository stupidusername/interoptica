<?php

namespace app\helpers;

/**
 * Static methods to build date strings (UTC)
 */
class DateHelper {

	const FORMAT = 'Y-m-d';

	/**
	 * @param string $date
	 * @return string
	 */
	public static function currentWeek($date) {
		return gmdate('Y-m-d', strtotime('monday next week -7 days' . $date));
	}

	/**
	 * @param string $date
	 * @return string
	 */
	public static function currentMonth($date) {
		return gmdate('Y-m-01', strtotime($date));
	}

	/**
	 * @param string $date
	 * @return string
	 */
	public static function currentYear($date) {
		return gmdate('Y-01-01', strtotime($date));
	}

	/**
	 * @param string $date
	 * @return string
	 */
	public static function nextWeek($date) {
		return gmdate('Y-m-d', strtotime('monday next week ' . $date));
	}

	/**
	 * @param string $date
	 * @return string
	 */
	public static function nextMonth($date) {
		return gmdate('Y-m-01', strtotime('+1 month ' . $date));
	}

	/**
	 * @param string $date
	 * @return string
	 */
	public static function nextYear($date) {
		return gmdate('Y-01-01', strtotime('+1 year ' . $date));
	}
}
