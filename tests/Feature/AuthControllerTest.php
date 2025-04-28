<?php

namespace Tests\Feature;

use App\Exceptions\ApiException;
use App\Exceptions\TestHandler;
use App\Models\User;
use App\Repositories\UserRepository;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear permission cache
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        // Seed roles
        $this->seed(RoleSeeder::class);
        // Register custom test exception handler
        $this->app->singleton(Handler::class, TestHandler::class);
    }

    /**
     * Test successful user registration.
     */
    public function test_successful_registration()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password!123',
            'confirm_password' => 'password!123',
            'role' => 'customer',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'user' => ['id', 'name', 'email', 'role', 'created_at'],
                         'token',
                     ],
                     'status',
                     'message',
                 ])
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Operation successful',
                 ]);
    }

    /**
     * Test registration with validation errors.
     */
    public function test_registration_validation_error()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'role' => 'invalid',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'status' => 'error',
                     'error' => 'validation_failed',
                     'code' => 422,
                 ])
                 ->assertJsonStructure([
                     'status',
                     'error',
                     'message',
                     'code',
                     'details',
                 ]);
    }

    /**
     * Test registration with role assignment failure.
     */
    public function test_registration_role_assignment_failure()
    {
        $this->mock(UserRepository::class, function ($mock) {
            $mock->shouldReceive('register')
                 ->once()
                 ->andThrow(new ApiException(
                     message: 'Failed to assign role.',
                     error: 'role_assignment_failed',
                     code: 400,
                     details: ['reason' => 'Role not found']
                 ));
        });

        $response = $this->postJson('/api/v1/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password!123',
            'confirm_password' => 'password!123',
            'role' => 'customer',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'error',
                     'error' => 'role_assignment_failed',
                     'code' => 400,
                 ])
                 ->assertJsonStructure([
                     'status',
                     'error',
                     'message',
                     'code',
                     'details',
                 ]);
    }

    /**
     * Test successful login.
     */
    public function test_successful_login()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password!123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'john@example.com',
            'password' => 'password!123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'user' => ['id', 'name', 'email', 'role', 'created_at'],
                         'token',
                     ],
                     'status',
                     'message',
                 ])
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Operation successful',
                 ]);
    }

    /**
     * Test login with invalid credentials.
     */
    public function test_login_invalid_credentials()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                     'error' => 'invalid_credentials',
                     'code' => 401,
                 ])
                 ->assertJsonStructure([
                     'status',
                     'error',
                     'message',
                     'code',
                     'details',
                 ]);
    }

    /**
     * Test successful logout.
     */
    public function test_successful_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->postJson('/api/v1/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Logged out successfully',
                 ])
                 ->assertJsonStructure([
                     'data',
                     'status',
                     'message',
                 ]);
    }

    /**
     * Test logout without authentication.
     */
    public function test_logout_unauthenticated()
    {
        $response = $this->postJson('/api/v1/logout');

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                     'error' => 'unauthenticated',
                     'code' => 401,
                 ])
                 ->assertJsonStructure([
                     'status',
                     'error',
                     'message',
                     'code',
                     'details',
                 ]);
    }
}