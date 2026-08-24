<?php

namespace Database\Factories;

use App\Enums\Platform;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SocialAccount>
 */
class SocialAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'platform' => fake()->randomElement(Platform::cases()),
            'name' => fake()->userName(),
            'external_id' => (string) fake()->numerify('##########'),
            'access_token' => 'sandbox-token',
            'is_sandbox' => true,
            'is_active' => true,
        ];
    }

    public function platform(Platform $platform): static
    {
        return $this->state(fn (): array => [
            'platform' => $platform,
        ]);
    }
}
