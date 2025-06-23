<?php

namespace App\Http\Controllers\Auth;

use App\Data\ForgotPasswordProps;
use App\Data\PasswordResetLinkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController
{
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ForgotPassword', new ForgotPasswordProps(
            status: $request->session()->get('status'),
        ));
    }

    public function store(PasswordResetLinkRequest $request): RedirectResponse
    {
        Password::sendResetLink(['email' => $request->email]);

        return back()->with('status', __('A reset link will be sent if the account exists.'));
    }
}
