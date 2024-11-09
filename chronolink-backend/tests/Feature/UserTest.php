<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserEmailsUnauthorized(): void
    {
        $response = $this->withHeader('Accept', 'application/json')->getJson('/api/users/test');
        $response->assertStatus(401);
    }

    public function testUserEmails(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        for ($i = 0; $i < 10; $i++) {
            User::factory()->create([
                'email' => 'test'.$i.'@test.com',
                'password' => Hash::make('password'),
            ]);
        }
        for ($i = 0; $i < 10; $i++) {
            User::factory()->create([
                'email' => 'noone'.$i.'@email.pl',
                'password' => Hash::make('password'),
            ]);
        }
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->getJson('/api/users/test');
        $response->assertStatus(200);
        $response->assertJsonCount(11, 'data');
    }
}
