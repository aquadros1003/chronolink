<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\Timeline;
use App\Models\UserTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/events/{timeline}",
     *    summary="Get all events",
     *    operationId="events",
     *    tags={"Event"},
     *    security={{ "apiAuth": {} }},
     *
     *  @OA\Parameter(
     *    name="timeline",
     *    in="path",
     *    description="Timeline ID",
     *    required=true,
     * ),
     * @OA\Parameter(
     *   name="per_page",
     *   in="query",
     *   description="Number of events per page",
     *   required=false,
     * ),
     *
     * @OA\Response(response=200, description="List of events", @OA\JsonContent()),
     * @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * )
     */
    public function index(Timeline $timeline, Request $request)
    {
        $user = Auth::user();
        $userTimeline = UserTimeline::where('user_id', $user->id)
            ->where('timeline_id', $timeline->id)
            ->first();
        if (! $userTimeline) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $events = $timeline->events()->orderBy('start_date', 'asc')->paginate($request->per_page ?? 10);

        return response()->json(['status' => 'success', 'message' => 'List of events', 'data' => $events], 200);
    }

    /**
     * @OA\Post(
     *    path="/api/create-event",
     *    summary="Create an event",
     *    operationId="createEvent",
     *    tags={"Event"},
     *    security={{ "apiAuth": {} }},
     *
     *    @OA\RequestBody(
     *        required=true,
     *
     *        @OA\JsonContent(
     *           required={"title", "start_date", "end_date", "timeline_id"},
     *
     *          @OA\Property(property="title", type="string", example="Event title"),
     *          @OA\Property(property="description", type="string", example="Event description"),
     *          @OA\Property(property="location", type="string", example="Event location"),
     *          @OA\Property(property="start_date", type="date", example="2021-08-01"),
     *          @OA\Property(property="end_date", type="date", example="2021-08-02"),
     *          @OA\Property(property="timeline_id", type="integer", example=1),
     *          @OA\Property(property="label_id", type="integer", example=1),
     *    ),
     * ),
     *
     *    @OA\Response(response=200, description="Event created", @OA\JsonContent()),
     *    @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *    @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * )
     */
    public function store(EventRequest $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $event = $user->events()->create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Event created',
            'data' => $event,
        ], 200);
    }

    /**
     * @OA\Put(
     *    path="/api/update-event/{event}",
     *    summary="Update an event",
     *    operationId="updateEvent",
     *    tags={"Event"},
     *    security={{ "apiAuth": {} }},
     *
     *    @OA\Parameter(
     *        name="event",
     *        in="path",
     *        description="Event ID",
     *        required=true,
     *    ),
     *
     *    @OA\RequestBody(
     *        required=true,
     *
     *        @OA\JsonContent(
     *           required={"title", "start_date", "end_date", "timeline_id"},
     *
     *          @OA\Property(property="title", type="string", example="Event title"),
     *          @OA\Property(property="description", type="string", example="Event description"),
     *          @OA\Property(property="location", type="string", example="Event location"),
     *          @OA\Property(property="start_date", type="date", example="2021-08-01"),
     *          @OA\Property(property="end_date", type="date", example="2021-08-02"),
     *          @OA\Property(property="timeline_id", type="integer", example=1),
     *          @OA\Property(property="label_id", type="integer", example=1),
     *    ),
     * ),
     *
     *    @OA\Response(response=200, description="Event updated", @OA\JsonContent()),
     *    @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *    @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *    @OA\Response(response=404, description="Event not found", @OA\JsonContent()),
     *    @OA\Response(response=422, description="Validation error", @OA\JsonContent()),
     * )
     */
    public function update(EventRequest $request, Event $event)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $timeline = $event->timeline()->first();
        $userTimeline = UserTimeline::where('user_id', $user->id)
            ->where('timeline_id', $timeline->id)
            ->first();
        if (! $userTimeline) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if ($userTimeline->timeline->owner_id !== $user->id && ! $userTimeline->permissions->contains('name', 'CAN_DELETE_EVENT')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $event->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Event updated',
            'data' => $event,
        ], 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/delete-event/{event}",
     *    summary="Delete an event",
     *    operationId="deleteEvent",
     *    security={{ "apiAuth": {} }},
     *    tags={"Event"},
     *
     *    @OA\Parameter(
     *        name="event",
     *        in="path",
     *        description="Event ID",
     *        required=true,
     *    ),
     *
     *    @OA\Response(response=200, description="Event deleted", @OA\JsonContent()),
     *    @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *    @OA\Response(response=404, description="Event not found", @OA\JsonContent()),
     * )
     */
    public function destroy(Event $event)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $timeline = $event->timeline()->first();
        $userTimeline = UserTimeline::where('user_id', $user->id)
            ->where('timeline_id', $timeline->id)
            ->first();
        if (! $userTimeline) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if ($userTimeline->timeline->owner_id !== $user->id && ! $userTimeline->permissions->contains('name', 'CAN_DELETE_EVENT')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $event->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Event deleted',
            'data' => null,
        ], 200);
    }
}
