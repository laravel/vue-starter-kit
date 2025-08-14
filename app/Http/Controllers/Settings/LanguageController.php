<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LanguageController extends Controller
{
    /**
     * Update the user's language preference.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(config('app.available_locales'))],
        ]);

        if ($request->user()) {
            $request->user()->update([
                'locale' => $validated['locale'],
            ]);
        }

        $request->session()->put('locale', $validated['locale']);

        return back()->with('status', __('messages.language_updated'));
    }
}
