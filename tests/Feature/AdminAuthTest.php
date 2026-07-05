<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_accepts_default_admin_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@resq.local',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');

        $user = User::where('email', 'admin@resq.local')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->canAccessAdmin());
        $this->assertAuthenticatedAs($user);
    }
}
