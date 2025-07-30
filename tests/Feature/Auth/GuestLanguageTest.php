<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestLanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_user_can_update_language_preference()
    {
        $response = $this->patch('/language', [
            'locale' => 'fr',
        ]);

        $response->assertRedirect();
        $this->assertEquals('fr', session('locale'));
    }

    public function test_guest_user_language_preference_is_respected()
    {
        // Set session locale to French
        $this->withSession(['locale' => 'fr']);

        // Test that the locale is applied
        $this->get('/login');

        // The middleware should have set the application locale
        $this->assertEquals('fr', app()->getLocale());
    }

    public function test_authenticated_user_language_preference_takes_priority()
    {
        $user = \App\Models\User::factory()->create(['locale' => 'en']);

        // Set session locale to French
        $this->withSession(['locale' => 'fr']);

        $response = $this->actingAs($user)->get('/login');

        // User's locale preference should take priority over session
        $this->assertEquals('en', app()->getLocale());
    }
}
