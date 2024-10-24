<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function loggedApiClient($user = null)
    {
        if (! $user) {
            $user = User::factory()->create();
        }
        $token = auth('api')->login($user);

        return $this->withHeader('Authorization ', 'Bearer '.$token);
    }
}
