<?php

namespace App\Http\Controllers\Auth;

use App\Data\NewPasswordData;
use App\Data\NewPasswordRequest;
use App\Data\ResetPasswordProps;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController
{
	public function create(Request $request): Response
	{
		return Inertia::render('auth/ResetPassword', new ResetPasswordProps(
			email: $request->email,
			token: $request->route('token'),
		));
	}

	public function store(NewPasswordRequest $request): RedirectResponse
	{
		// Here we will attempt to reset the user's password. If it is successful, we
		// will update the password on an actual user model and persist it to the
		// database. Otherwise, we will parse the error and return the response.
		$status = Password::reset(
			credentials: [
				'email' => $request->email,
				'password' => $request->password,
				'token' => $request->token,
			],
			callback: fn(User $user, string $password) => $user->forceFill([
				'password' => Hash::make($password),
				'remember_token' => Str::random(60),
			])
				->save(),
		);

		// If the password was successfully reset, we will redirect the user back to
		// the application's home authenticated view. If there is an error, we can
		// redirect them back to where they came from with their error message.
		if ($status == Password::PasswordReset) {
			return to_route('login')->with('status', __($status));
		}

		throw ValidationException::withMessages([
			'email' => [__($status)],
		]);
	}
}
