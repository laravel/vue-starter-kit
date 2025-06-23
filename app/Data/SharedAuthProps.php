<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SharedAuthProps extends Data
{
	public function __construct(
		public ?User $user,
	)
	{
	}
}
