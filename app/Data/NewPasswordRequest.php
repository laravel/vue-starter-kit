<?php

namespace App\Data;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class NewPasswordRequest extends Data
{
	public function __construct(
		public string $token,
		public string $email,
		public string $password,
		public string $password_confirmation,
	)
	{
	}

	/**
	 * @return array<string, array<int, mixed>>
	 */
	public static function rules(ValidationContext $context): array
	{
		return [
			'token' => ['required'],
			'email' => ['required', Rule::email()],
			'password' => ['required', 'confirmed', Password::defaults()],
		];
	}
}
