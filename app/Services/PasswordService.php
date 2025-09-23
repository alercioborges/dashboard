<?php

namespace App\Services;

class PasswordService
{
	private int $cost;

	public function __construct(int $cost = 12)
	{
		$this->cost = $cost;
	}

	public function make(String $password): string
	{
		$options = ['cost' => $this->cost];		
		return password_hash($password, PASSWORD_BCRYPT, $options);
	}

	public function verify(String $password, string $hash): bool
	{
		return password_verify($password, $hash);
	}
}
