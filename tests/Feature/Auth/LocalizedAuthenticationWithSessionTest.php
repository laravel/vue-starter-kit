<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizedAuthenticationWithSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authentication_error_messages_are_localized_with_session_locale()
    {
        // Set session locale to French
        $this->withSession(['locale' => 'fr']);

        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');

        // Check that the error message is in French
        $this->assertStringContainsString(
            'Ces identifiants ne correspondent pas à nos enregistrements',
            $response->getSession()->get('errors')->first('email')
        );
    }

    public function test_validation_error_messages_are_localized_with_session_locale()
    {
        // Set session locale to French
        $this->withSession(['locale' => 'fr']);

        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);

        // Check that validation error messages are in French
        $this->assertStringContainsString(
            'adresse e-mail',
            $response->getSession()->get('errors')->first('email')
        );

        $this->assertStringContainsString(
            'obligatoire',
            $response->getSession()->get('errors')->first('password')
        );
    }

    public function test_authentication_error_messages_use_session_locale_for_guest_users()
    {
        // Create a user with French locale preference
        $user = User::factory()->create(['locale' => 'fr']);

        // Set session locale to English (this should take priority for guest users)
        $this->withSession(['locale' => 'en']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');

        // Check that the error message is in English (session locale takes priority for guest users)
        $this->assertStringContainsString(
            'These credentials do not match our records',
            $response->getSession()->get('errors')->first('email')
        );
    }

    public function test_user_locale_is_applied_after_successful_authentication()
    {
        // Create a user with French locale preference
        $user = User::factory()->create(['locale' => 'fr']);

        // Set session locale to English
        $this->withSession(['locale' => 'en']);

        // First, try to access a protected route (should use session locale)
        $response = $this->actingAs($user)->get('/dashboard');

        // After authentication, the user's locale preference should be applied
        $this->assertEquals('fr', app()->getLocale());
    }
}
