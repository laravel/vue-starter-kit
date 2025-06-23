<?php

namespace App\Http\Controllers\Settings;

use App\Data\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController
{
	public function edit(): Response
	{
		return Inertia::render('settings/Password');
	}

	public function update(PasswordUpdateRequest $request): RedirectResponse
	{
		Auth::user()->update([
			'password' => Hash::make($request->password),
		]);

		return back();
	}
}
