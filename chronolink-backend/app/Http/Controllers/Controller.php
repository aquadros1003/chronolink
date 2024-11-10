<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(title="ChronoLink API", version="0.1")
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Token based Based",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="apiAuth",
 * )
  */
abstract class Controller
{
    //
}
