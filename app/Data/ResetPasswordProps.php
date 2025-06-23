<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ResetPasswordProps extends Data
{
	public function __construct(
		public string $email,
		public string $token,
	)
	{
	}
}
