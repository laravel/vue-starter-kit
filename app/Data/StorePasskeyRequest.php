<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StorePasskeyRequest extends Data
{
	public function __construct(
		public string $name,
		public string $passkey,
	)
	{
	}

	/**
	 * @return array<string, array<string>>
	 */
	public static function rules(ValidationContext $context): array
	{
		return [
			'passkey' => ['required', 'json'],
		];
	}
}
