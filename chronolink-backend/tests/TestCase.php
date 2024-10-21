<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;


abstract class TestCase extends BaseTestCase
{
    public function loggedApiClient($user = null)
    {
        if (!$user) {
            $user = User::factory()->create();
        }
        $token = auth('api')->login($user);
        return $this->withHeader('Authorization ', 'Bearer ' . $token);
    }
}
