<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get a list of users",
     *     operationId="index",
     *     tags={"Users"},
     *
     *    @OA\Response(response=200, description="A list with users"),
     *    @OA\Response(response=400, description="Bad request"),
     * )
     */
    public function index()
    {
        return User::all();
    }
}
