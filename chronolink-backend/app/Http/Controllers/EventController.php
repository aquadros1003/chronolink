<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * @OA\Post(
     *    path="/api/create-event",
     *    summary="Create an event",
     *    operationId="createEvent",
     *    tags={"Event"},
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
     *          @OA\Property(property="start_date", type="datetime", example="2021-08-01 00:00:00"),
     *          @OA\Property(property="end_date", type="datetime", example="2021-08-01 23:59:59"),
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
     *          @OA\Property(property="start_date", type="datetime", example="2021-08-01 00:00:00"),
     *          @OA\Property(property="end_date", type="datetime", example="2021-08-01 23:59:59"),
     *          @OA\Property(property="timeline_id", type="integer", example=1),
     *          @OA\Property(property="label_id", type="integer", example=1),
     *    ),
     * ),
     *
     *    @OA\Response(response=200, description="Event updated", @OA\JsonContent()),
     *    @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *    @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * )
     */
    public function update(EventRequest $request, Event $event)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $event = $user->events()->where('id', $event->id)->first();
        if (! $event) {
            return response()->json(['error' => 'Event not found'], 404);
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
     * )
     */
    public function destroy(Event $event)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $event = $user->events()->where('id', $event->id)->first();
        if (! $event) {
            return response()->json(['error' => 'Event not found'], 404);
        }
        $event->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Event deleted',
            'data' => null,
        ], 200);
    }
}
