<?php

namespace Database\Factories;

use App\Models\Permission;
use App\Models\UserTimeline;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimelinePermission>
 */
class TimelinePermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'permission_id' => Permission::factory(),
            'user_timeline_id' => UserTimeline::factory(),
        ];
    }
}
