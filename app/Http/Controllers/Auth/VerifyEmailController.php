<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request, int $id, string $hash): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired verification link.');
        }
    
        $user = User::findOrFail($id);
    
        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification hash.');
        }
    
        // Now you can verify the email
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            
            // Fire event when email is verified
            event(new Verified($user));
        }

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
