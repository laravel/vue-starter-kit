<?php

namespace App\Data;

use Illuminate\Contracts\Validation\Rule as RuleContract;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class DeleteUserRequest extends Data
{
	public function __construct(
		public string $password,
	)
	{
	}

    /**
     * @return array<string, array<int, string|RuleContract>>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'password' => ['required', 'current_password'],
        ];
    }
}
