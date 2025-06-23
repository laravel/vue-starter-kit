<?php

namespace App\Http\Controllers\Auth;

use App\Data\ConfirmPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmablePasswordController
{
    public function show(): Response
    {
        return Inertia::render('auth/ConfirmPassword');
    }

    public function store(ConfirmPasswordRequest $request): RedirectResponse
    {
        if (!Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        Session::put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
