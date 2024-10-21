<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;


class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function testUserRegistration(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john_doe@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
        ]);
    }

    public function testUserLogin(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john_doe@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
        ]);
    }

    public function testMeQuery(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->getJson('/api/auth/me');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
        ]);
    }

    public function testUserLogout(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->postJson('/api/auth/logout');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
        ]);
    }
}