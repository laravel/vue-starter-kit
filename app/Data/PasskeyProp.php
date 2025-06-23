<?php

namespace App\Data;

use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PasskeyProp extends Data
{
	public function __construct(
        public int $id,
		public string $name,
        public ?CarbonInterface $lastUsedAt,
        public ?string $lastUsedAtForHumans,
	)
	{
	}

    public static function fromPasskey(Passkey $passkey): self
    {
        return new self(
            $passkey->id,
            $passkey->name,
            $passkey->last_used_at,
            $passkey->last_used_at?->diffForHumans(),
        );
    }
}
