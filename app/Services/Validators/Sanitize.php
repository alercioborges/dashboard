<?php

namespace App\Services\Validators;

abstract class Sanitize
{
	protected function sanitize()
	{
		$sanitized = [];

		foreach ($_POST as $field => $value) {

			// Escape dangerous characters
			$value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

			// Remove space if this is first or last caractere
			$value = trim($value);

			//Remove invisible and control characters
			$value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

			$sanitized[$field] = $value;
		}

		return $sanitized;
	}
}
