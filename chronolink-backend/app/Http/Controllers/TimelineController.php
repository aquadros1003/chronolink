<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimelineRequest;
use App\Models\Timeline;
use App\Models\TimelinePermission;
use App\Models\User;
use App\Models\UserTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimelineController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/timelines",
     *    summary="Get all user timelines",
     *    operationId="userTimelines",
     *    tags={"Timeline"},
     *    security={{ "apiAuth": {} }},
     *
     *    @OA\Response(response=200, description="List of timelines"),
     *    @OA\Response(response=401, description="Unauthorized"),
     * )
     */
    public function userTimelines()
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $timelines = $user->timelines;

        return response()->json([
            'status' => 'success',
            'message' => 'List of timelines',
            'data' => $timelines,
        ], 200);
    }

    /**
     * @OA\Get(
     *    path="/api/timeline/{timeline}",
     *    summary="Get a timeline",
     *    operationId="show",
     *    tags={"Timeline"},
     *    security={{ "apiAuth": {} }},
     *
     *   @OA\Parameter(
     *     name="timeline",
     *     in="path",
     *     description="Timeline ID",
     *     required=true,
     * ),
     *
     *  @OA\Response(response=200, description="Timeline details", @OA\JsonContent()),
     *  @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *  @OA\Response(response=404, description="Timeline not found", @OA\JsonContent()),
     * )
     */
    public function show(Timeline $timeline)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $timeline = $user->timelines()->find($timeline->id);
        if (! $timeline) {
            return response()->json(['error' => 'Timeline not found'], 404);
        }
        $userTimeline = UserTimeline::where('user_id', $user->id)->where('timeline_id', $timeline->id)->first();
        $permissions = TimelinePermission::where('user_timeline_id', $userTimeline->id)->get();
        $timeline->permissions = $permissions->pluck('permission');

        return response()->json([
            'status' => 'success',
            'message' => 'Timeline details',
            'data' => $timeline,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/create-timeline",
     *     summary="Create a new timeline",
     *     operationId="store",
     *     tags={"Timeline"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *          @OA\JsonContent(
     *             required={"title", "description", "start_date", "end_date"},
     *
     *           @OA\Property(property="title", type="string", example="My first timeline"),
     *           @OA\Property(property="description", type="string", example="This is my first timeline"),
     *    ),
     *   ),
     *
     *  @OA\Response(response=200, description="Timeline created successfully", @OA\JsonContent()),
     *  @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *  @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *  @OA\Response(response=422, description="Validation error", @OA\JsonContent()),
     * )
     */
    public function store(TimelineRequest $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $timeline = Timeline::create(array_merge($request->validated(), ['owner_id' => $user->id]));
        UserTimeline::create([
            'user_id' => $user->id,
            'timeline_id' => $timeline->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Timeline created successfully',
            'data' => $timeline,
        ], 200);
    }

    /**
     * @OA\Put(
     *    path="/api/update-timeline/{timeline}",
     *    summary="Update a timeline",
     *    operationId="update",
     *    tags={"Timeline"},
     *    security={{ "apiAuth": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *          @OA\JsonContent(
     *             required={"title", "description", "start_date", "end_date"},
     *
     *           @OA\Property(property="title", type="string", example="My first timeline"),
     *           @OA\Property(property="description", type="string", example="This is my first timeline"),
     *    ),
     *   ),
     *
     *  @OA\Response(response=200, description="Timeline updated successfully", @OA\JsonContent()),
     *  @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *  @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *  @OA\Response(response=404, description="Timeline not found", @OA\JsonContent()),
     *  @OA\Response(response=422, description="Validation error", @OA\JsonContent()),
     * )
     */
    public function update(TimelineRequest $request, Timeline $timeline)
    {
        $timeline->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Timeline updated successfully',
            'data' => $timeline,
        ], 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/delete-timeline/{timeline}",
     *    summary="Delete a timeline",
     *    operationId="destroy",
     *    tags={"Timeline"},
     *    security={{ "apiAuth": {} }},
     *
     *  @OA\Response(response=200, description="Timeline deleted successfully", @OA\JsonContent()),
     *  @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *  @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * )
     */
    public function destroy(Timeline $timeline)
    {
        $timeline->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Timeline deleted successfully',
            'data' => null,
        ], 200);
    }

    /**
     * @OA\Post(
     *    path="/api/assign-user/{timeline}",
     *    summary="Assign a user to a timeline",
     *    operationId="assignUser",
     *    tags={"Timeline"},
     *    security={{ "apiAuth": {} }},
     *
     *   @OA\Parameter(
     *     name="timeline",
     *     in="path",
     *     description="Timeline ID",
     *     required=true,
     * ),
     *
     * @OA\RequestBody(
     *    required=true,
     *
     *   @OA\JsonContent(
     *     required={"email"},
     *
     *    @OA\Property(property="email", type="string", example="john_doe@test.com"),
     *  ),
     * ),
     *
     *  @OA\Response(response=200, description="User assigned successfully", @OA\JsonContent()),
     *  @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *  @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *  @OA\Response(response=404, description="User not found", @OA\JsonContent()),
     * )
     */
    public function assignUser(Timeline $timeline, Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'data' => null,
            ], 404);
        }
        UserTimeline::create([
            'user_id' => $user->id,
            'timeline_id' => $timeline->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User assigned successfully',
            'data' => $timeline,
        ], 200);
    }
}
