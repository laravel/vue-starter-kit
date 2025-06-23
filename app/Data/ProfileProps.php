<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProfileProps extends Data
{
	public function __construct(
		public bool    $mustVerifyEmail,
		public ?string $status,
	)
	{
	}
}
