<?php

namespace App\Data;

use App\Data\Transformers\UriTransformer;
use App\Models\User as UserModel;
use Carbon\CarbonInterface;
use Illuminate\Support\Uri;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class User extends Data
{
    public function __construct(
        public int              $id,
        public string           $name,
        public string           $email,
        #[WithTransformer(UriTransformer::class)]
        public ?Uri             $avatar,
        public ?CarbonInterface $email_verified_at,
        public CarbonInterface  $created_at,
    )
    {
    }

    public static function fromUser(?UserModel $user): ?self
    {
        if ($user === null) {
            return null;
        }

        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            avatar: $user->avatar,
            email_verified_at: $user->email_verified_at,
            created_at: $user->created_at,
        );
    }
}
