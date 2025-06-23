<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class NewPasswordData extends Data
{
    public function __construct(
        public string $email,
        public string $token,
    )
    {
    }
}
