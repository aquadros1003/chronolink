<?php

namespace Tests\Feature;

use App\Models\Timeline;
use App\Models\User;
use App\Models\UserTimeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanCreateEvent()
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline = Timeline::factory()->create();
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->postJson('api/create-event', [
            'title' => 'My Event',
            'location' => 'My location',
            'start_date' => '2024-10-10',
            'end_date' => '2024-10-11',
            'timeline_id' => $timeline->id,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'Event created',
        ]);
        $this->assertDatabaseHas('events', [
            'title' => 'My Event',
            'location' => 'My location',
            'start_date' => '2024-10-10',
            'end_date' => '2024-10-11',
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
    }

    public function testUserCanUpdateEvent()
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline = Timeline::factory()->create(["owner_id" => $user->id]);
        UserTimeline::factory()->create([
            'user_id' => $user->id,
            'timeline_id' => $timeline->id,
        ]);
        $label = $timeline->labels()->create([
            'name' => 'My Label',
            'color' => '#FF0000',
            'user_id' => $user->id,
        ]);
        $event = $user->events()->create([
            'title' => 'My Event',
            'location' => 'My location',
            'description' => 'My description',
            'start_date' => '2024-10-10',
            'end_date' => '2024-10-11',
            'timeline_id' => $timeline->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->putJson('api/update-event/'.$event->id, [
            'title' => 'Updated Event',
            'location' => 'Updated location',
            'description' => 'Updated description',
            'start_date' => '2024-10-11',
            'end_date' => '2024-10-12',
            'timeline_id' => $timeline->id,
            'label_id' => $label->id,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'Event updated',
            'data' => [
                'id' => $event->id,
                'title' => 'Updated Event',
                'location' => 'Updated location',
                'description' => 'Updated description',
                'start_date' => '2024-10-11',
                'end_date' => '2024-10-12',
                'label' => [
                    'id' => $label->id,
                    'name' => 'My Label',
                    'color' => '#FF0000',
                ],
            ],
        ]);
        $this->assertDatabaseHas('events', [
            'title' => 'Updated Event',
            'location' => 'Updated location',
            'start_date' => '2024-10-11',
            'end_date' => '2024-10-12',
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
    }

    public function testUserCanDeleteEvent()
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline = Timeline::factory()->create(["owner_id" => $user->id]);
        UserTimeline::factory()->create([
            'user_id' => $user->id,
            'timeline_id' => $timeline->id,
        ]);
        $event = $user->events()->create([
            'title' => 'My Event',
            'location' => 'My location',
            'start_date' => '2024-10-10',
            'end_date' => '2024-10-11',
            'timeline_id' => $timeline->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->deleteJson('api/delete-event/'.$event->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('events', [
            'title' => 'My Event',
            'location' => 'My location',
            'start_date' => '2024-10-10',
            'end_date' => '2024-10-11',
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
    }

    public function testTimelineEvents()
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline = Timeline::factory()->create(
            ['title' => 'My Timeline', 'owner_id' => $user->id]
        );
        UserTimeline::factory()->create([
            'user_id' => $user->id,
            'timeline_id' => $timeline->id,
        ]);
        $event1 = $user->events()->create([
            'title' => 'My Event',
            'location' => 'My location',
            'start_date' => '2021-10-10',
            'end_date' => '2021-10-11',
            'timeline_id' => $timeline->id,
        ]);
        $event2 = $user->events()->create([
            'title' => 'My Event 2',
            'location' => 'My location 2',
            'start_date' => '2021-10-12',
            'end_date' => '2021-10-13',
            'timeline_id' => $timeline->id,
        ]);
        $event3 = $user->events()->create([
            'title' => 'My Event 2',
            'location' => 'My location 2',
            'start_date' => '2021-10-12',
            'end_date' => '2021-10-13',
            'timeline_id' => $timeline->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->getJson('api/events/'.$timeline->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'List of events',
        ]);
        $response->assertJsonFragment([
            'id' => $event1->id,
            'title' => 'My Event',
            'location' => 'My location',
            'start_date' => '2021-10-10',
            'end_date' => '2021-10-11',
        ]);
        $response->assertJsonFragment([
            'id' => $event2->id,
            'title' => 'My Event 2',
            'location' => 'My location 2',
            'start_date' => '2021-10-12',
            'end_date' => '2021-10-13',
        ]);
        $response->assertJsonFragment([
            'id' => $event3->id,
            'title' => 'My Event 2',
            'location' => 'My location 2',
            'start_date' => '2021-10-12',
            'end_date' => '2021-10-13',
        ]);
    }
}
