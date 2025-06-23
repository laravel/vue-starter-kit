<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PasskeyRegistrationOptionsRequest extends Data
{
	public function __construct(
		public string $name,
	)
	{
	}
}
