<?php

namespace Tests\Feature;

use App\Enums\Platform;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_connect_sandbox_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('accounts.store'), [
                'platform' => Platform::Instagram->value,
                'name' => 'Мой Instagram',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'platform' => Platform::Instagram->value,
            'name' => 'Мой Instagram',
            'is_sandbox' => true,
        ]);
    }

    public function test_user_can_disconnect_own_account(): void
    {
        $user = User::factory()->create();
        $account = SocialAccount::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('accounts.destroy', $account))
            ->assertRedirect();

        $this->assertDatabaseMissing('social_accounts', ['id' => $account->id]);
    }
}
