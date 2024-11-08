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
        $token = auth('api')->attempt(['email' => $user->email, 'password' => 'password']);

        $apiClient = $this->withHeader('Authorization', 'Bearer '.$token);
        $apiClient = $apiClient->withHeader('Accept', 'application/json');

        return $apiClient;
    }
}
