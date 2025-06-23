<?php

namespace App\Http\Controllers\Settings;

use App\Data\PasskeyProp;
use App\Data\PasskeyProps;
use App\Data\PasskeyRegistrationOptionsRequest;
use App\Data\StorePasskeyRequest;
use App\Models\Passkey;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Throwable;

class PasskeyController
{
    public function edit(): Response
    {
        return Inertia::render('settings/Passkey', new PasskeyProps(
            passkeys: Auth::user()->passkeys->map(fn(Passkey $passkey) => PasskeyProp::fromPasskey($passkey))->all(),
        ));
    }

    public function generatePasskeyOptions(PasskeyRegistrationOptionsRequest $request): string
    {
        $generatePassKeyOptionsAction = app(GeneratePasskeyRegisterOptionsAction::class);

        $options = $generatePassKeyOptionsAction->execute(Auth::user());

        if (!is_string($options)) {
            throw ValidationException::withMessages([
                'name' => __('passkeys::passkeys.error_something_went_wrong_generating_the_passkey'),
            ]);
        }

        Session::put('passkey-registration-options', $options);

        return $options;
    }

    /**
     * @throws Exception
     */
    public function store(StorePasskeyRequest $request): RedirectResponse
    {
        $storePasskeyAction = app(StorePasskeyAction::class);

        try {
            $storePasskeyAction->execute(
                authenticatable: Auth::user(),
                passkeyJson: $request->passkey,
                passkeyOptionsJson: Session::pull('passkey-registration-options'),
                hostName: request()->getHost(),
                additionalProperties: [
                    'name' => $request->name
                ],
            );
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'name' => __('passkeys::passkeys.error_something_went_wrong_generating_the_passkey'),
            ]);
        }

        return back();
    }

    public function destroy(Passkey $passkey): RedirectResponse
    {
        Gate::authorize('delete', $passkey);

        $passkey->delete();

        return back();
    }
}
