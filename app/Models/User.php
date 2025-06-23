<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\AsUri;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;

class User extends Authenticatable implements HasPasskeys
{
	/** @use HasFactory<UserFactory> */
	use HasFactory;
	use Notifiable;
	use InteractsWithPasskeys;

	/**
	 * @var list<string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * @return array{avatar: 'Illuminate\Database\Eloquent\Casts\AsUri', email_verified_at: 'immutable_datetime', password: 'hashed', created_at: 'immutable_datetime'}
	 */
	protected function casts(): array
	{
		return [
			'avatar' => AsUri::class,
			'email_verified_at' => 'immutable_datetime',
			'password' => 'hashed',
			'created_at' => 'immutable_datetime',
		];
	}
}
