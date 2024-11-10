<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Get the authenticated user",
     *     operationId="me",
     *     security={{ "apiAuth": {} }},
     *     tags={"Auth"},
     *
     *     @OA\Response(response=200, description="The authenticated user"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     * )
     */
    public function me()
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return response()->json($user, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     operationId="register",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *
     *            @OA\Property(property="name", type="string", example="John Doe"),
     *            @OA\Property(property="email", type="string", format="email", example="john_doe@test.com"),
     *            @OA\Property(property="password", type="string", format="password", example="password"),
     *            @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
     *        ),
     *   ),
     *
     *  @OA\Response(response=200, description="User registered successfully"),
     *  @OA\Response(response=400, description="Bad request"),
     *  @OA\Response(response=422, description="Validation error"),
     * )
     */
    public function register(UserRequest $request)
    {
        $credentials = $request->validated();

        $user = new User;
        $user->name = $credentials['name'];
        $user->email = $credentials['email'];
        $user->password = Hash::make($credentials['password']);
        $user->save();

        return $this->login($request);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login a user",
     *     operationId="login",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="john_doe@test.com"),
     *            @OA\Property(property="password", type="string", format="password", example="password"),
     *       ),
     * ),
     *
     * @OA\Response(response=200, description="User logged in successfully"),
     * @OA\Response(response=401, description="Unauthorized"),
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout the authenticated user",
     *     operationId="logout",
     *     security={{ "apiAuth": {} }},
     *     tags={"Auth"},
     *
     *     @OA\Response(response=200, description="User logged out successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     * )
     */
    public function logout()
    {
        try {
            Auth::logout();
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh the token",
     *     operationId="refresh",
     *     security={{ "apiAuth": {} }},
     *     tags={"Auth"},
     *
     *     @OA\Response(response=200, description="Token refreshed successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     * )
     */
    public function refresh()
    {
        try {
            return $this->respondWithToken(Auth::refresh());
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
        ]);
    }
}
