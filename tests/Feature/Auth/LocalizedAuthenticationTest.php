<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizedAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authentication_translations_work_in_french()
    {
        // Set the application locale to French
        app()->setLocale('fr');

        // Test authentication translations
        $this->assertEquals(
            'Ces identifiants ne correspondent pas à nos enregistrements.',
            trans('auth.failed')
        );

        $this->assertEquals(
            'Le mot de passe fourni est incorrect.',
            trans('auth.password')
        );

        $this->assertEquals(
            'Trop de tentatives de connexion. Veuillez réessayer dans 60 secondes.',
            trans('auth.throttle', ['seconds' => 60])
        );
    }

    public function test_authentication_translations_work_in_english()
    {
        // Set the application locale to English
        app()->setLocale('en');

        // Test authentication translations
        $this->assertEquals(
            'These credentials do not match our records.',
            trans('auth.failed')
        );

        $this->assertEquals(
            'The provided password is incorrect.',
            trans('auth.password')
        );

        $this->assertEquals(
            'Too many login attempts. Please try again in 60 seconds.',
            trans('auth.throttle', ['seconds' => 60])
        );
    }

    public function test_validation_translations_work_in_french()
    {
        // Set the application locale to French
        app()->setLocale('fr');

        // Test validation translations
        $this->assertEquals(
            'Le champ adresse e-mail est obligatoire.',
            trans('validation.required', ['attribute' => 'adresse e-mail'])
        );

        $this->assertEquals(
            'Le champ adresse e-mail doit être une adresse e-mail valide.',
            trans('validation.email', ['attribute' => 'adresse e-mail'])
        );

        $this->assertEquals(
            'La confirmation du champ mot de passe ne correspond pas.',
            trans('validation.confirmed', ['attribute' => 'mot de passe'])
        );
    }

    public function test_validation_translations_work_in_english()
    {
        // Set the application locale to English
        app()->setLocale('en');

        // Test validation translations
        $this->assertEquals(
            'The email address field is required.',
            trans('validation.required', ['attribute' => 'email address'])
        );

        $this->assertEquals(
            'The email address must be a valid email address.',
            trans('validation.email', ['attribute' => 'email address'])
        );

        $this->assertEquals(
            'The password confirmation does not match.',
            trans('validation.confirmed', ['attribute' => 'password'])
        );
    }

    public function test_password_reset_translations_work_in_french()
    {
        // Set the application locale to French
        app()->setLocale('fr');

        // Test password reset translations
        $this->assertEquals(
            'Votre mot de passe a été réinitialisé.',
            trans('passwords.reset')
        );

        $this->assertEquals(
            'Nous avons envoyé votre lien de réinitialisation de mot de passe par e-mail.',
            trans('passwords.sent')
        );

        $this->assertEquals(
            'Ce jeton de réinitialisation de mot de passe est invalide.',
            trans('passwords.token')
        );
    }

    public function test_password_reset_translations_work_in_english()
    {
        // Set the application locale to English
        app()->setLocale('en');

        // Test password reset translations
        $this->assertEquals(
            'Your password has been reset.',
            trans('passwords.reset')
        );

        $this->assertEquals(
            'We have emailed your password reset link.',
            trans('passwords.sent')
        );

        $this->assertEquals(
            'This password reset token is invalid.',
            trans('passwords.token')
        );
    }

    public function test_general_messages_translations_work_in_french()
    {
        // Set the application locale to French
        app()->setLocale('fr');

        // Test general messages translations
        $this->assertEquals(
            'Langue mise à jour avec succès.',
            trans('messages.language_updated')
        );

        $this->assertEquals(
            'Profil mis à jour avec succès.',
            trans('messages.profile_updated')
        );

        $this->assertEquals(
            'Mot de passe mis à jour avec succès.',
            trans('messages.password_updated')
        );
    }

    public function test_general_messages_translations_work_in_english()
    {
        // Set the application locale to English
        app()->setLocale('en');

        // Test general messages translations
        $this->assertEquals(
            'Language updated successfully.',
            trans('messages.language_updated')
        );

        $this->assertEquals(
            'Profile updated successfully.',
            trans('messages.profile_updated')
        );

        $this->assertEquals(
            'Password updated successfully.',
            trans('messages.password_updated')
        );
    }
}
