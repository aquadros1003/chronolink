<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserTimelinePemissionsRequest;
use App\Models\Permission;
use App\Models\Timeline;
use App\Models\TimelinePermission;
use App\Models\User;
use App\Models\UserTimeline;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/permissions",
     *    summary="Get all permissions",
     *    operationId="permissions",
     *    tags={"Permissions"},
     *    security={{ "apiAuth": {} }},
     *
     *   @OA\Response(response=200, description="List of permissions", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * )
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'List of permissions',
            'data' => Permission::all(),
        ], 200);
    }

    /**
     * @OA\PUT(
     *      path="/api/timelines/{timeline}/update-user-permissions",
     *      summary="Update user timeline permissions",
     *      operationId="updateUserTimelinePemissions",
     *      tags={"Permissions"},
     *      security={{ "apiAuth": {} }},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"permissions", "user_email"},
     *
     *              @OA\Property(property="permissions", type="array", @OA\Items(type="integer", example=1)),
     *              @OA\Property(property="user_email", type="string", format="email", example="john_doe@test.com"),
     *  ),
     * ),
     *
     * @OA\Response(response=200, description="Permissions updated", @OA\JsonContent()),
     * @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * @OA\Response(response=404, description="User not found", @OA\JsonContent()),
     * @OA\Response(response=422, description="Validation error", @OA\JsonContent()),
     * )
     */
    public function updateUserTimelinePemissions(Timeline $timeline, UpdateUserTimelinePemissionsRequest $request)
    {
        $owner = Auth::user();
        $ownerTimeline = $owner->timelines()->where('timeline_id', $timeline->id)->where('owner_id', $owner->id)->first();
        if (! $ownerTimeline) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $permissions = $request->validated()['permissions'];
        $user_email = $request->validated()['user_email'];
        $user = User::where('email', $user_email)->first();
        $userTimeline = UserTimeline::where('timeline_id', $timeline->id)->where('user_id', $user->id)->first();
        if (! $userTimeline) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $currentPermissions = TimelinePermission::where('user_timeline_id', $userTimeline->id)->get();
        foreach ($currentPermissions as $currentPermission) {
            $currentPermission->delete();
        }
        foreach ($permissions as $permission) {
            TimelinePermission::create([
                'user_timeline_id' => $userTimeline->id,
                'permission_id' => $permission,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Permissions updated',
            'data' => $userTimeline->permissions,
        ], 200);
    }
}
