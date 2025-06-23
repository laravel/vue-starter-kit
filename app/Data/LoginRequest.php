<?php

namespace App\Data;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class LoginRequest extends Data
{
	public function __construct(
		public string $email,
		public string $password,
		public bool   $remember = false,
	)
	{
	}

	public function authenticate(): void
	{
		$this->ensureIsNotRateLimited();

		if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
			RateLimiter::hit($this->throttleKey());

			throw ValidationException::withMessages([
				'email' => trans('auth.failed'),
			]);
		}

		RateLimiter::clear($this->throttleKey());
	}

	public function ensureIsNotRateLimited(): void
	{
		if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
			return;
		}

		$seconds = RateLimiter::availableIn($this->throttleKey());

		throw ValidationException::withMessages([
			'email' => trans('auth.throttle', [
				'seconds' => $seconds,
				'minutes' => ceil($seconds / 60),
			]),
		]);
	}

	public function throttleKey(): string
	{
		return Str::transliterate(Str::lower($this->email) . '|' . Request::ip());
	}
}
