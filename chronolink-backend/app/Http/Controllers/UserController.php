<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/users/{search}",
     *    summary="Get a list of users emails",
     *    operationId="userEmails",
     *    tags={"Users"},
     *    security={{ "apiAuth": {} }},
     *
     *   @OA\Parameter(
     *      name="search",
     *      in="path",
     *      description="Search string",
     *      required=true,
     * ),
     *
     * @OA\Response(response=200, description="A list with users emails"),
     * @OA\Response(response=401, description="Unauthorized"),
     * )
     * }
     */
    public function userEmails($search)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'List of users emails',
            'data' => User::where('email', 'like', '%'.$search.'%')->get('email')->pluck('email'),
        ], 200);
    }
}
