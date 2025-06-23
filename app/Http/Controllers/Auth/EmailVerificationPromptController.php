<?php

namespace App\Http\Controllers\Auth;

use App\Data\VerifyEmailPrompts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController
{
	public function __invoke(Request $request): RedirectResponse|Response
	{
		return $request->user()->hasVerifiedEmail()
			? redirect()->intended(route('dashboard', absolute: false))
			: Inertia::render('auth/VerifyEmail', new VerifyEmailPrompts(status: $request->session()->get('status')));
	}
}
