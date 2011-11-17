<?php

class StringUtils {

	public static function startsWith(
		$what,
		$with
	) {
		if ($with == "")
			return true;
		$pos = strpos($what, $with);
		return ($pos !== false) && ($pos == 0);
	}

	public static function endsWith(
		$what,
		$with
	) {
		if ($with == "")
			return true;
		$pos = strpos($what, $with);
		return ($pos !== false) && ($pos == strlen($what) - strlen($with));
	}

}
