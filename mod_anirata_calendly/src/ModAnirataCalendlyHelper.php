<?php

namespace Joomla\Module\AnirataCalendly\Site;

class ModAnirataCalendlyHelper {

	public static function isValidUUID(string $uuid = null): bool
	{
		if ($uuid == null) return false;
		return preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $uuid) !== 1 ? false : true;
	}

	//2023-11-23T13:30:00+01:00
	public static function isValidCalendlyDate(string $date = null): bool
	{
		if ($date == null) return false;
		return preg_match('/^20[0-9]{2}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}[+-][0-9]{2}:[0-9]{2}$/i', $date) !== 1 ? false : true;
	}
	
}

