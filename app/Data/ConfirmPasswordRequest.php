<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ConfirmPasswordRequest extends Data
{
	public function __construct(
		public string $password,
	)
	{
	}
}
