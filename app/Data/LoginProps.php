<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class LoginProps extends Data
{
	public function __construct(
		public bool $canResetPassword,
        public ?string $status,
        public ?string $passkeyStatus,
	)
	{
	}
}
