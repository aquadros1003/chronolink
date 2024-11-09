<?php

namespace App\Http\Controllers;

use App\Http\Requests\LabelRequest;
use App\Models\Label;
use App\Models\Timeline;
use App\Models\UserTimeline;
use Illuminate\Support\Facades\Auth;

class LabelController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/labels/{timeline}",
     *    summary="Get all labels",
     *    operationId="labels",
     *    tags={"Label"},
     *
     *   @OA\Parameter(
     *     name="timeline",
     *     in="path",
     *     description="Timeline ID",
     *     required=true,
     * ),
     *
     * @OA\Response(response=200, description="List of labels", @OA\JsonContent()),
     * @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * )
     */
    public function index(Timeline $timeline)
    {
        $user = Auth::user();
        $userTimeline = UserTimeline::where('user_id', $user->id)
            ->where('timeline_id', $timeline->id)
            ->first();
        if (! $userTimeline) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $labels = $timeline->labels;

        return response()->json(['status' => 'success', 'message' => 'List of labels', 'data' => $labels], 200);
    }

    /**
     * @OA\Post(
     *    path="/api/create-label",
     *    summary="Create a label",
     *    operationId="createLabel",
     *    tags={"Label"},
     *
     *    @OA\RequestBody(
     *        required=true,
     *
     *        @OA\JsonContent(
     *           required={"name", "color", "timeline_id"},
     *
     *          @OA\Property(property="name", type="string", example="Label name"),
     *          @OA\Property(property="color", type="string", example="#000000"),
     *          @OA\Property(property="timeline_id", type="string", example="00000000-0000-0000-0000-000000000000"),
     *    ),
     * ),
     *
     * @OA\Response(response=200, description="Label created", @OA\JsonContent()),
     * @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * )
     */
    public function store(LabelRequest $request)
    {
        $user = Auth::user();
        $timeline_id = $request->validated()['timeline_id'];

        $userTimeline = UserTimeline::where('user_id', $user->id)
            ->where('timeline_id', $timeline_id)
            ->first();

        if (! $userTimeline) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if ($userTimeline->timeline->owner_id !== $user->id && ! $userTimeline->permissions->contains('name', 'CREATE_LABEL')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $label = $userTimeline->timeline->labels()->create(array_merge($request->validated(), ['user_id' => $user->id]));

        return response()->json(['status' => 'success', 'message' => 'Label created', 'data' => $label], 200);
    }

    /**
     * @OA\Put(
     *      path="/api/update-label/{label}",
     *      summary="Update a label",
     *      operationId="updateLabel",
     *      tags={"Label"},
     *
     *     @OA\Parameter(
     *         name="label",
     *         in="path",
     *         description="Label ID",
     *         required=true,
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"name", "color", "timeline_id"},
     *
     *              @OA\Property(property="name", type="string", example="Label name"),
     *              @OA\Property(property="color", type="string", example="#000000"),
     *              @OA\Property(property="timeline_id", type="string", example="00000000-0000-0000-0000-000000000000"),
     *  ),
     * ),
     *
     * @OA\Response(response=200, description="Label updated", @OA\JsonContent()),
     * @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * @OA\Response(response=404, description="Label not found", @OA\JsonContent()),
     * )
     */
    public function update(Label $label, LabelRequest $request)
    {
        $user = Auth::user();
        $timeline_id = $request->validated()['timeline_id'];

        $userTimeline = UserTimeline::where('user_id', $user->id)
            ->where('timeline_id', $timeline_id)
            ->first();
        if (! $userTimeline) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if ($userTimeline->timeline->owner_id !== $user->id && ! $userTimeline->permissions->contains('name', 'UPDATE_LABEL')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $label->update($request->validated());

        return response()->json(['status' => 'success', 'message' => 'Label updated', 'data' => $label], 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/delete-label/{label}",
     *      summary="Delete a label",
     *      operationId="deleteLabel",
     *      tags={"Label"},
     *
     *     @OA\Parameter(
     *         name="label",
     *         in="path",
     *         description="Label ID",
     *         required=true,
     *      ),
     *
     * @OA\Response(response=200, description="Label deleted", @OA\JsonContent()),
     * @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * @OA\Response(response=404, description="Label not found", @OA\JsonContent()),
     * )
     */
    public function destroy(Label $label)
    {
        $user = Auth::user();
        $timeline_id = $label->timeline_id;

        $userTimeline = UserTimeline::where('user_id', $user->id)
            ->where('timeline_id', $timeline_id)
            ->first();
        if (! $userTimeline) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if ($userTimeline->timeline->owner_id !== $user->id && ! $userTimeline->permissions->contains('name', 'DELETE_LABEL')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $label->delete();

        return response()->json(['status' => 'success', 'message' => 'Label deleted'], 200);
    }
}
