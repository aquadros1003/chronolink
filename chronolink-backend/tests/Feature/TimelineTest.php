<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TimelineTest extends TestCase
{
    use RefreshDatabase;

    public function testTimelineMeQuery(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline_1 = $user->timelines()->create([
            'title' => 'My Timeline',
            'description' => 'This is my timeline',
        ]);
        $timeline_2 = $user->timelines()->create([
            'title' => 'Another Timeline',
            'description' => 'This is another timeline',
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->getJson('/api/auth/me');
        $response->assertStatus(200);
        $response->assertJson([
            'timelines' => [
                [
                    'id' => $timeline_1->id,
                    'title' => 'My Timeline',
                    'description' => 'This is my timeline',
                ],
                [
                    'id' => $timeline_2->id,
                    'title' => 'Another Timeline',
                    'description' => 'This is another timeline',
                ],
            ],
        ]);
    }
}
