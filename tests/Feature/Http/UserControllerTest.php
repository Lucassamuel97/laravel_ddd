<?php

namespace Tests\Feature\Http;

use App\Infrastructure\Persistence\Eloquent\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_user_successfully(): void
    {
        $response = $this->postJson('/api/users', [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email', 'role'],
            ])
            ->assertJsonPath('data.email', 'john@example.com')
            ->assertJsonPath('data.role', 'user');

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_returns_422_for_duplicate_email(): void
    {
        UserModel::create([
            'name'     => 'Existing User',
            'email'    => 'john@example.com',
            'password' => bcrypt('password'),
            'role'     => 'user',
        ]);

        $response = $this->postJson('/api/users', [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message']);
    }

    public function test_returns_422_for_missing_name(): void
    {
        $response = $this->postJson('/api/users', [
            'email'    => 'john@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_returns_422_for_invalid_email(): void
    {
        $response = $this->postJson('/api/users', [
            'name'     => 'John Doe',
            'email'    => 'not-an-email',
            'password' => 'secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_returns_422_for_short_password(): void
    {
        $response = $this->postJson('/api/users', [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_can_register_user_with_admin_role(): void
    {
        $response = $this->postJson('/api/users', [
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'password' => 'admin123',
            'role'     => 'admin',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'admin');
    }
}
