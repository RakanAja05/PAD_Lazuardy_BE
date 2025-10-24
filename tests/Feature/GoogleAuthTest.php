<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    public function it_returns_error_if_google_callback_fails()
    {
        $response = $this->get('/auth/google/callback');
        $response->assertStatus(500);
    }

    #[Test]
    public function it_can_simulate_successful_login()
    {
        $user = User::factory()->create([
            'google_id' => 'test123',
            'email' => 'test@example.com',
            'role' => 'student',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
             ->get('/api/me');

        $response->assertStatus(200);
    }
}
