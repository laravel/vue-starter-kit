<?php

namespace App\Models;

use Override;
use Spatie\LaravelPasskeys\Models\Passkey as BasePasskey;

class Passkey extends BasePasskey
{
	/**
	 * @return array{last_used_at: 'immutable_datetime'}
	 */
	#[Override]
	public function casts(): array
	{
		return [
			'last_used_at' => 'immutable_datetime',
		];
	}
}
