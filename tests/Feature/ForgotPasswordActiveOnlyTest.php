<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ForgotPasswordActiveOnlyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_forgot_password_named_routes_are_disabled(): void
    {
        $this->assertFalse(Route::has('password.request'));
        $this->assertFalse(Route::has('password.email'));
        $this->assertFalse(Route::has('password.reset'));
        $this->assertFalse(Route::has('password.update'));
    }

    public function test_forgot_password_and_reset_endpoints_return_404(): void
    {
        $this->get('/forgot-password')->assertNotFound();
        $this->post('/forgot-password', ['email' => 'test@example.test'])->assertNotFound();
        $this->get('/reset-password/some-token')->assertNotFound();
        $this->post('/reset-password', [
            'token' => 'some-token',
            'email' => 'test@example.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertNotFound();

        $this->assertDatabaseCount('password_reset_tokens', 0);
    }
}
