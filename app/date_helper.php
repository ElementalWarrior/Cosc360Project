<?php

class date_helper {
	public static function convertFromUTC($date) {
		$utc = new DateTimeZone('UTC');
		$zone = new DateTimeZone(date_default_timezone_get());
		if($date instanceOf DateTime) {
			$date = $date->format();
		}
		$date = new DateTime($date, $utc);
		$date->setTimezone($zone);
		return $date;
	}
		public static function convertToUTC($date) {
			$utc = new DateTimeZone('UTC');
			$zone = new DateTimeZone(date_default_timezone_get());
			if($date instanceOf DateTime) {
				$date = $date->format('Y-m-d H:i:s');
			}
			$date = new DateTime($date, $zone);
			$date->setTimezone($utc);
			return $date;
		}
} ?>
