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

class LabelTest extends TestCase
{
    use RefreshDatabase;

    public function testLabelQuery(): void
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
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
        $label1 = $timeline->labels()->create([
            'name' => 'My Label',
            'color' => '#FF0000',
            'user_id' => $user->id,
        ]);
        $label2 = $timeline->labels()->create([
            'name' => 'My Label 2',
            'color' => '#00FF00',
            'user_id' => $user->id,
        ]);
        $label3 = $timeline->labels()->create([
            'name' => 'My Label 3',
            'color' => '#0000FF',
            'user_id' => $user->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->getJson('api/labels/'.$timeline->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'List of labels',
            'data' => [
                [
                    'id' => $label1->id,
                    'name' => 'My Label',
                    'color' => '#FF0000',
                ],
                [
                    'id' => $label2->id,
                    'name' => 'My Label 2',
                    'color' => '#00FF00',
                ],
                [
                    'id' => $label3->id,
                    'name' => 'My Label 3',
                    'color' => '#0000FF',
                ],
            ],
        ]);
    }

    public function testCreateLabel()
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
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->postJson(uri: 'api/create-label', data: [
            'name' => 'My Label',
            'color' => '#FF0000',
            'timeline_id' => $timeline->id,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'Label created',
        ]);
        $this->assertDatabaseHas('labels', [
            'name' => 'My Label',
            'color' => '#FF0000',
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
    }

    public function testUpdateLabel()
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
            'name' => 'UPDATE_LABEL',
        ]);
        TimelinePermission::factory()->create([
            'user_timeline_id' => $userTimeline->id,
            'permission_id' => $permission->id,
        ]);
        $label = $timeline->labels()->create([
            'name' => 'My Label',
            'color' => '#FF0000',
            'user_id' => $user->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->putJson(uri: 'api/update-label/'.$label->id, data: [
            'name' => 'My Updated Label',
            'color' => '#00FF00',
            'timeline_id' => $timeline->id,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'Label updated',
            'data' => [
                'id' => $label->id,
                'name' => 'My Updated Label',
                'color' => '#00FF00',
            ],
        ]);
        $this->assertDatabaseHas('labels', [
            'name' => 'My Updated Label',
            'color' => '#00FF00',
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
    }

    public function testDeleteLabel()
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
            'name' => 'DELETE_LABEL',
        ]);
        TimelinePermission::factory()->create([
            'user_timeline_id' => $userTimeline->id,
            'permission_id' => $permission->id,
        ]);
        $label = $timeline->labels()->create([
            'name' => 'My Label',
            'color' => '#FF0000',
            'user_id' => $user->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->deleteJson(uri: 'api/delete-label/'.$label->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'Label deleted',
        ]);
        $this->assertDatabaseMissing('labels', [
            'name' => 'My Label',
            'color' => '#FF0000',
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
    }
}
