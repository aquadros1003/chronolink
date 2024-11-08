<?php

namespace Tests\Feature;

// use App\Models\Label;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_event()
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
            'start_date' => '2021-10-10 00:00:00',
            'end_date' => '2021-10-11 23:59:59',
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
            'start_date' => '2021-10-10 00:00:00',
            'end_date' => '2021-10-11 23:59:59',
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_update_event()
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline = Timeline::factory()->create();
        $event = $user->events()->create([
            'title' => 'My Event',
            'location' => 'My location',
            'start_date' => '2021-10-10 00:00:00',
            'end_date' => '2021-10-11 23:59:59',
            'timeline_id' => $timeline->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->putJson('api/update-event/'.$event->id, [
            'title' => 'Updated Event',
            'location' => 'Updated location',
            'start_date' => '2021-10-11 00:00:00',
            'end_date' => '2021-10-12 23:59:59',
            'timeline_id' => $timeline->id,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'Event updated',
        ]);
        $this->assertDatabaseHas('events', [
            'title' => 'Updated Event',
            'location' => 'Updated location',
            'start_date' => '2021-10-11 00:00:00',
            'end_date' => '2021-10-12 23:59:59',
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_delete_event()
    {
        $user = User::factory()->create([
            'email' => 'john_doe@test.com',
            'password' => Hash::make('password'),
        ]);
        $timeline = Timeline::factory()->create();
        $event = $user->events()->create([
            'title' => 'My Event',
            'location' => 'My location',
            'start_date' => '2021-10-10 00:00:00',
            'end_date' => '2021-10-11 23:59:59',
            'timeline_id' => $timeline->id,
        ]);
        $userClient = $this->loggedApiClient($user);
        $response = $userClient->deleteJson('api/delete-event/'.$event->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('events', [
            'title' => 'My Event',
            'location' => 'My location',
            'start_date' => '2021-10-10 00:00:00',
            'end_date' => '2021-10-11 23:59:59',
            'timeline_id' => $timeline->id,
            'user_id' => $user->id,
        ]);
    }
}
