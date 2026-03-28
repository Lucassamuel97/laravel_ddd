<?php

namespace Tests\Feature\Http;

use App\Infrastructure\Persistence\Eloquent\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_user_by_id(): void
    {
        // Arrange
        $user = UserModel::create([
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'user',
        ]);

        // Act
        $response = $this->getJson('/api/users/' . $user->id);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email', 'role'],
            ])
            ->assertJsonPath('data.id', (string) $user->id)
            ->assertJsonPath('data.name', 'John Doe')
            ->assertJsonPath('data.email', 'john@example.com')
            ->assertJsonPath('data.role', 'user');
    }

    public function test_returns_404_when_get_user_by_id_and_user_does_not_exist(): void
    {
        // Act
        $response = $this->getJson('/api/users/999');

        // Assert
        $response->assertStatus(404)
            ->assertJsonPath('message', 'User with id 999 was not found.');
    }

    public function test_can_list_users_with_pagination(): void
    {
        UserModel::create([
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'user',
        ]);

        UserModel::create([
            'name'     => 'Jane Doe',
            'email'    => 'jane@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'admin',
        ]);

        $response = $this->getJson('/api/users?per_page=1&page=1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'email', 'role'],
                ],
                'meta' => ['total', 'per_page', 'current_page', 'last_page'],
            ])
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2);
    }

    public function test_can_filter_users_by_name_and_email(): void
    {
        UserModel::create([
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'user',
        ]);

        UserModel::create([
            'name'     => 'Jane Doe',
            'email'    => 'jane@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'user',
        ]);

        $response = $this->getJson('/api/users?name=john&email=john@example.com');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'John Doe')
            ->assertJsonPath('data.0.email', 'john@example.com')
            ->assertJsonPath('meta.total', 1);
    }

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
