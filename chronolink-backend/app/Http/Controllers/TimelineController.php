<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimelineRequest;
use App\Models\Timeline;

class TimelineController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/create-timeline",
     *     summary="Create a new timeline",
     *     operationId="store",
     *     tags={"Timeline"},
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
     *  @OA\Response(response=200, description="Timeline created successfully"),
     *  @OA\Response(response=400, description="Bad request"),
     *  @OA\Response(response=401, description="Unauthorized"),
     * )
     */
    public function store(TimelineRequest $request)
    {
        $timeline = Timeline::create($request->validated());

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
     *  @OA\Response(response=200, description="Timeline updated successfully"),
     *  @OA\Response(response=400, description="Bad request"),
     *  @OA\Response(response=401, description="Unauthorized"),
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
     *
     *  @OA\Response(response=200, description="Timeline deleted successfully"),
     *  @OA\Response(response=400, description="Bad request"),
     *  @OA\Response(response=401, description="Unauthorized"),
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
}
