<?php

namespace App\Services\Validators;

abstract class Sanitize
{
	protected function sanitize()
	{
		$sanitized = [];

		foreach ($_POST as $field => $value) {
			$sanitized[$field] = htmlspecialchars($value, ENT_QUOTES);
		}

		return $sanitized;
	}
}
