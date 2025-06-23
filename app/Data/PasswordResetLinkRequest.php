<?php

namespace App\Data;

use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PasswordResetLinkRequest extends Data
{
	public function __construct(
		public string $email,
	)
	{
	}

    /**
     * @return array<string, array<int, string|RuleContract>>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'email' => ['required', Rule::email()],
        ];
    }
}
