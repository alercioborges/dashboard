<?php

namespace App\Services;

class TokenService
{
	public function generateToken(): string
	{
		return bin2hex(random_bytes(32));
	}

	public function hashToken(string $token): string
	{
		return hash('sha256', $token);
	}
}
