<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\LaravelPasskeys\Models\Passkey;

class PasskeyPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Passkey $passkey): bool
    {
        return $user->id === $passkey->authenticatable_id;
    }
}
