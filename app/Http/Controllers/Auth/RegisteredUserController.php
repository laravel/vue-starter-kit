<?php

namespace App\Http\Controllers\Auth;

use App\Data\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController
{
	public function store(RegisterRequest $request): RedirectResponse
	{
		$user = User::query()->create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => Hash::make($request->password),
		]);

		event(new Registered($user));

		Auth::login($user);

		return to_route('dashboard');
	}

	public function create(): Response
	{
		return Inertia::render('auth/Register');
	}
}
