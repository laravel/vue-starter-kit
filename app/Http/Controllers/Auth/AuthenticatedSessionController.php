<?php

namespace App\Http\Controllers\Auth;

use App\Data\LoginProps;
use App\Data\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController
{
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', new LoginProps(
            canResetPassword: Route::has('password.request'),
            status: $request->session()->get('status'),
            passkeyStatus: $request->session()->get('authenticatePasskey::message'),
        ));
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        Session::regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
