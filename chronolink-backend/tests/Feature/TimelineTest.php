<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Timeline;
use App\Models\TimelinePermission;
use App\Models\User;
use App\Models\UserTimeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TimelineTest extends TestCase
{
    use RefreshDatabase;

    public function testTimelineQuery(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline_1 = Timeline::factory()->create([
            'title' => 'My Timeline',
            'description' => 'This is my timeline',
            'owner_id' => $user->id,
        ]);
        $timeline_2 = Timeline::factory()->create([
            'title' => 'Another Timeline',
            'description' => 'This is another timeline',
        ]);
        UserTimeline::factory()->create([
            'user_id' => $user->id,
            'timeline_id' => $timeline_1->id,
        ]);
        UserTimeline::factory()->create([
            'user_id' => $user->id,
            'timeline_id' => $timeline_2->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->getJson('api/timelines');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'List of timelines',
            'data' => [
                [
                    'id' => $timeline_1->id,
                    'title' => 'My Timeline',
                    'description' => 'This is my timeline',
                    'is_owner' => true,
                ],
                [
                    'id' => $timeline_2->id,
                    'title' => 'Another Timeline',
                    'description' => 'This is another timeline',
                    'is_owner' => false,
                ],
            ],
        ]);
    }

    public function testTimelineCreation(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->postJson('api/create-timeline', [
            'title' => 'My Timeline',
            'description' => 'This is my timeline',
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'Timeline created successfully',
        ]);
        $this->assertDatabaseHas('timelines', [
            'title' => 'My Timeline',
            'description' => 'This is my timeline',
        ]);
    }

    public function testTimelineUpdate(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline = Timeline::factory()->create([
            'title' => 'My Timeline',
            'description' => 'This is my timeline',
        ]);
        UserTimeline::factory()->create([
            'user_id' => $user->id,
            'timeline_id' => $timeline->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->putJson("api/update-timeline/{$timeline->id}", [
            'title' => 'My Updated Timeline',
            'description' => 'This is my updated timeline',
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'Timeline updated successfully',
        ]);
        $this->assertDatabaseHas('timelines', [
            'title' => 'My Updated Timeline',
            'description' => 'This is my updated timeline',
        ]);
    }

    public function testTimelineDeletion(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline = Timeline::factory()->create([
            'title' => 'My Timeline',
            'description' => 'This is my timeline',
        ]);
        UserTimeline::factory()->create([
            'user_id' => $user->id,
            'timeline_id' => $timeline->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->deleteJson("api/delete-timeline/{$timeline->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'Timeline deleted successfully',
        ]);
        $this->assertDatabaseMissing('timelines', [
            'title' => 'My Timeline',
            'description' => 'This is my timeline',
        ]);
    }

    public function testTimelinePermissions(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline = Timeline::factory()->create([
            'title' => 'My Timeline',
            'description' => 'This is my timeline',
        ]);
        $userTimeline = UserTimeline::factory()->create([
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
        $permission = Permission::factory()->create([
            'name' => 'CREATE_EVENT',
        ]);
        $permission_2 = Permission::factory()->create([
            'name' => 'UPDATE_EVENT',
        ]);
        TimelinePermission::factory()->create([
            'user_timeline_id' => $userTimeline->id,
            'permission_id' => $permission->id,
        ]);
        TimelinePermission::factory()->create([
            'user_timeline_id' => $userTimeline->id,
            'permission_id' => $permission_2->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->getJson("api/timelines/{$timeline->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'Timeline details',
            'data' => [
                'id' => $timeline->id,
                'title' => 'My Timeline',
                'description' => 'This is my timeline',
                'permissions' => [
                    [
                        'id' => $permission->id,
                        'name' => 'CREATE_EVENT',
                    ],
                    [
                        'id' => $permission_2->id,
                        'name' => 'UPDATE_EVENT',
                    ]],
                'is_owner' => false,
            ],
        ]);
    }

    public function testAssignUserToTimeline(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $user_2 = User::factory()->create([
            'email' => 'john_doe2@test.com',
        ]);
        $timeline = Timeline::factory()->create([
            'title' => 'My Timeline',
            'description' => 'This is my timeline',
        ]);
        UserTimeline::factory()->create([
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->postJson("api/assign-user/{$timeline->id}", [
            'email' => 'john_doe2@test.com',
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'User assigned successfully',
            'data' => [
                'id' => $timeline->id,
                'title' => 'My Timeline',
                'description' => 'This is my timeline',
                'is_owner' => false,
            ],
        ]);
        $this->assertDatabaseHas('user_timeline', [
            'user_id' => $user_2->id,
            'timeline_id' => $timeline->id,
        ]);
    }

    public function testTimelineUsers(): void
    {
        $user = User::factory()->create([
            'email' => 'test123@test.pl',
            'password' => Hash::make('password'),
        ]);
        $user_2 = User::factory()->create([
            'email' => 'test2@test.pl',
        ]);
        $user_3 = User::factory()->create([
            'email' => 'test3@test.pl',
        ]);
        $timeline = Timeline::factory()->create([
            'title' => 'My Timeline',
            'description' => 'This is my timeline',
            'owner_id' => $user->id,
        ]);
        UserTimeline::factory()->create([
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
        $ut2 = UserTimeline::factory()->create([
            'timeline_id' => $timeline->id,
            'user_id' => $user_2->id,
        ]);
        $ut3 = UserTimeline::factory()->create([
            'timeline_id' => $timeline->id,
            'user_id' => $user_3->id,
        ]);
        $permission = Permission::factory()->create([
            'name' => 'CREATE_EVENT',
        ]);
        $permission_2 = Permission::factory()->create([
            'name' => 'UPDATE_EVENT',
        ]);
        $permission_3 = Permission::factory()->create([
            'name' => 'DELETE_EVENT',
        ]);
        TimelinePermission::factory()->create([
            'user_timeline_id' => $ut2->id,
            'permission_id' => $permission->id,
        ]);
        TimelinePermission::factory()->create([
            'user_timeline_id' => $ut2->id,
            'permission_id' => $permission_2->id,
        ]);
        TimelinePermission::factory()->create([
            'user_timeline_id' => $ut3->id,
            'permission_id' => $permission_3->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->getJson("api/timelines/{$timeline->id}/users");
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'List of users assigned to a timeline',
            'data' => [
                [
                    'id' => $user_2->id,
                    'name' => $user_2->name,
                    'email' => 'test2@test.pl',
                    'permissions' => [
                        [
                            'id' => $permission->id,
                            'name' => 'CREATE_EVENT',
                        ],
                        [
                            'id' => $permission_2->id,
                            'name' => 'UPDATE_EVENT',
                        ],
                    ],
                ],
                [
                    'id' => $user_3->id,
                    'name' => $user_3->name,
                    'email' => 'test3@test.pl',
                    'permissions' => [
                        [
                            'id' => $permission_3->id,
                            'name' => 'DELETE_EVENT',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
