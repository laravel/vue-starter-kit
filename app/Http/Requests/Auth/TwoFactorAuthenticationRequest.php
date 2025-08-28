<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Features;

class TwoFactorAuthenticationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Features::enabled(Features::twoFactorAuthentication());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Validate the two-factor authentication state for the request.
     */
    public function validateState(): void
    {
        if (! Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm')) {
            return;
        }

        $currentTime = time();

        if ($this->twoFactorAuthenticationDisabled()) {
            $this->session()->put('two_factor_empty_at', $currentTime);
        }

        if ($this->hasJustBegunConfirmingTwoFactorAuthentication()) {
            $this->session()->put('two_factor_confirming_at', $currentTime);
        }

        if ($this->neverFinishedConfirmingTwoFactorAuthentication($currentTime)) {
            app(DisableTwoFactorAuthentication::class)(Auth::user());

            $this->session()->put('two_factor_empty_at', $currentTime);
            $this->session()->remove('two_factor_confirming_at');
        }
    }

    /**
     * Determine if two-factor authentication is totally disabled.
     */
    protected function twoFactorAuthenticationDisabled(): bool
    {
        return is_null($this->user()->two_factor_secret) &&
            is_null($this->user()->two_factor_confirmed_at);
    }

    /**
     * Determine if two-factor authentication is being confirmed within the last request cycle.
     */
    protected function hasJustBegunConfirmingTwoFactorAuthentication(): bool
    {
        return ! is_null($this->user()->two_factor_secret) &&
            is_null($this->user()->two_factor_confirmed_at) &&
            $this->session()->has('two_factor_empty_at') &&
            is_null($this->session()->get('two_factor_confirming_at'));
    }

    /**
     * Determine if two-factor authentication was never totally confirmed once confirmation started.
     */
    protected function neverFinishedConfirmingTwoFactorAuthentication(int $currentTime): bool
    {
        return ! array_key_exists('code', $this->session()->getOldInput()) &&
            is_null($this->user()->two_factor_confirmed_at) &&
            $this->session()->get('two_factor_confirming_at', 0) != $currentTime;
    }
}
