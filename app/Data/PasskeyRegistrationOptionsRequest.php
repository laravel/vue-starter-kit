<?php

namespace App\Data;

use Spatie\LaravelData\Data;
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
