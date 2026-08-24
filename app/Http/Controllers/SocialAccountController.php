<?php

namespace App\Http\Controllers;

use App\Enums\Platform;
use App\Http\Requests\StoreSandboxAccountRequest;
use App\Models\SocialAccount;
use App\Models\User;
use App\Social\SocialPublisherManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SocialAccountController extends Controller
{
    public function index(Request $request, SocialPublisherManager $publishers): Response
    {
        $user = $request->user();
        assert($user instanceof User);

        $accounts = $user->socialAccounts()
            ->latest()
            ->get()
            ->map(fn (SocialAccount $account): array => [
                'id' => $account->id,
                'platform' => $account->platform->value,
                'platform_label' => $account->platform->label(),
                'name' => $account->name,
                'is_sandbox' => $account->is_sandbox,
                'is_active' => $account->is_active,
                'created_at' => $account->created_at?->timezone(config('app.timezone'))->format('d.m.Y H:i'),
            ]);

        return Inertia::render('accounts/index', [
            'accounts' => $accounts,
            'platforms' => collect(Platform::cases())->map(fn (Platform $platform): array => [
                'value' => $platform->value,
                'label' => $platform->label(),
                'requires_video' => $platform->requiresVideo(),
                'oauth_configured' => $publishers->oauthConfigured($platform),
            ]),
            'sandbox' => (bool) config('social.sandbox'),
        ]);
    }

    public function store(StoreSandboxAccountRequest $request): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $platform = Platform::from($request->validated('platform'));

        SocialAccount::query()->create([
            'user_id' => $user->id,
            'platform' => $platform,
            'name' => $request->validated('name'),
            'external_id' => 'sandbox-'.$platform->value.'-'.$user->id,
            'access_token' => 'sandbox',
            'is_sandbox' => true,
            'is_active' => true,
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $platform->label().' подключён в тестовом режиме.',
        ]);

        return back();
    }

    public function destroy(Request $request, SocialAccount $socialAccount): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        abort_unless($socialAccount->user_id === $user->id || $user->is_admin, 403);

        $socialAccount->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Аккаунт отключён.']);

        return back();
    }
}
