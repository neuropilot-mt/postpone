<?php

namespace Database\Seeders;

use App\Enums\Platform;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        foreach (Platform::cases() as $platform) {
            SocialAccount::factory()->platform($platform)->create([
                'user_id' => $admin->id,
                'name' => $platform->label().' тест',
                'external_id' => 'sandbox-'.$platform->value,
            ]);
        }
    }
}
