<?php

namespace App\Data;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RegisterRequest extends Data
{
	public function __construct(
		public string $name,
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
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'lowercase', 'max:255', Rule::email(), Rule::unique(User::class)],
			'password' => ['required', 'confirmed', Password::defaults()],
		];
	}
}
