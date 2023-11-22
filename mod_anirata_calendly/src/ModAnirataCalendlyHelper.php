<?php

namespace Joomla\Module\AnirataCalendly\Site;

class ModAnirataCalendlyHelper {

	public static function isValidUUID(string $uuid = null): bool
	{
		if ($uuid == null) return false;
		return preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $uuid) !== 1 ? false : true;
	}
	
}

