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

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_query(): void
    {
        $userClient = $this->loggedApiClient();
        $response = $userClient->getJson('api/permissions');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'List of permissions',
        ]);
    }

    public function test_update_timeline_permission(): void
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline = Timeline::factory()->create([
            'title' => 'My Timeline',
            'description' => 'This is my timeline',
            'owner_id' => $user->id,
        ]);
        UserTimeline::factory()->create([
            'user_id' => $user->id,
            'timeline_id' => $timeline->id,
        ]);
        $permission = Permission::factory()->create([
            'name' => 'READ',
        ]);
        $permission2 = Permission::factory()->create([
            'name' => 'WRITE',
        ]);
        $permission3 = Permission::factory()->create([
            'name' => 'DESTROY',
        ]);

        $user2 = User::factory()->create([
            'email' => 'john_doe2@test.com',
            'password' => Hash::make('password'),
        ]);
        $userTimeline2 = UserTimeline::factory()->create([
            'user_id' => $user2->id,
            'timeline_id' => $timeline->id,
        ]);
        TimelinePermission::factory()->create([
            'user_timeline_id' => $userTimeline2->id,
            'permission_id' => $permission->id,
        ]);

        $userClient = $this->loggedApiClient($user);
        $response = $userClient->putJson('api/timelines/'.$timeline->id.'/update-user-permissions', [
            'permissions' => [$permission2->id, $permission3->id],
            'user_email' => $user2->email,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'Permissions updated',
            'data' => [
                [
                    'id' => $permission2->id,
                    'name' => 'WRITE',
                ],
                [
                    'id' => $permission3->id,
                    'name' => 'DESTROY',
                ],
            ],
        ]);
    }
}
