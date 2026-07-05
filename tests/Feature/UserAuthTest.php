<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login_page_is_available_and_uses_user_redirects(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'user_type' => 'user',
            'is_active' => true,
        ]);

        $this->get('/login')
            ->assertStatus(200)
            ->assertSee('Welcome back');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_homepage_renders_dashboard_module_sections(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Your Safety, Our Priority')
            ->assertSee('Quick Access')
            ->assertSee('Emergency Categories');
    }
}