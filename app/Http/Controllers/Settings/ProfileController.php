<?php

namespace App\Http\Controllers\Settings;

use App\Data\DeleteUserRequest;
use App\Data\ProfileProps;
use App\Data\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController
{
	public function edit(Request $request): Response
	{
		return Inertia::render('settings/Profile', new ProfileProps(
			mustVerifyEmail: $request->user() instanceof MustVerifyEmail,
			status: $request->session()->get('status'),
		));
	}

	public function update(ProfileUpdateRequest $request): RedirectResponse
	{
		$user = Auth::user();

		$user->fill([
			'name' => $request->name,
			'email' => $request->email,
		]);

		if ($user->isDirty('email')) {
			$user->email_verified_at = null;
		}

		$user->save();

		return to_route('profile.edit');
	}

	public function destroy(DeleteUserRequest $request): RedirectResponse
	{
		$user = Auth::user();

		Auth::logout();

		$user->delete();

		Session::invalidate();
		Session::regenerateToken();

		return redirect('/');
	}
}
