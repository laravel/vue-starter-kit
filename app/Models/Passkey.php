<?php

namespace App\Models;

use Spatie\LaravelPasskeys\Models\Passkey as BasePasskey;

class Passkey extends BasePasskey
{
    /**
     * @return array{last_used_at: 'immutable_datetime'}
     */
    public function casts(): array
    {
        return [
            'last_used_at' => 'immutable_datetime',
        ];
    }
}
