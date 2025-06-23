<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PasskeyProps extends Data
{
	/**
	 * @param array<PasskeyProp> $passkeys
	 */
	public function __construct(
		public array $passkeys,
	)
	{
	}
}
