<?php

namespace App\Services\Validators;

abstract class Sanitize
{
	private const RAW_FIELDS = [
		'password',
		'password-confirm',
		'password_confirmation',
		'token',
		'_METHOD',
		'csrf_name',
		'csrf_value',
	];


	protected function sanitize(array $data): array
	{
		$sanitized = [];

		foreach ($data as $key => $value) {			
			$sanitized[$key] = in_array($key, self::RAW_FIELDS, true)
				? $value
				: $this->sanitizeValue($value);
		}

		return $sanitized;
	}


	private function sanitizeValue(mixed $value): mixed
	{
		if (is_array($value)) {
			return array_map(
				fn(mixed $item): mixed => $this->sanitizeValue($item),
				$value
			);
		}

		if (is_string($value)) {
			$value = htmlspecialchars(
				trim($value),
				ENT_QUOTES | ENT_SUBSTITUTE,
				'UTF-8'
			);

			$value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

			return $value;
		}

		return $value;
	}
}
