<?php

namespace App\Http\Controllers;

use App\Enums\Platform;
use App\Models\User;
use App\Social\OAuth\SocialOAuthService;
use App\Social\SocialPublisherManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class SocialOAuthController extends Controller
{
    public function redirect(Request $request, string $platform, SocialPublisherManager $publishers, SocialOAuthService $oauth): RedirectResponse
    {
        $resolved = Platform::tryFrom($platform);

        if (! $resolved instanceof Platform) {
            abort(404);
        }

        if (! $publishers->oauthConfigured($resolved)) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'OAuth для '.$resolved->label().' ещё не настроен. Добавьте ключи в .env.',
            ]);

            return back();
        }

        return redirect()->away($oauth->redirectUrl($resolved));
    }

    public function callback(Request $request, string $platform, SocialOAuthService $oauth): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $resolved = Platform::tryFrom($platform);

        if (! $resolved instanceof Platform) {
            abort(404);
        }

        if ($request->filled('error')) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => (string) $request->string('error_description', 'Авторизация отклонена.'),
            ]);

            return to_route('accounts.index');
        }

        try {
            $account = $oauth->connect(
                $user,
                $resolved,
                (string) $request->string('code'),
                $request->string('state')->toString() ?: null,
            );

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => $account->platform->label().' подключён: '.$account->name,
            ]);
        } catch (Throwable $exception) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => $exception->getMessage(),
            ]);
        }

        return to_route('accounts.index');
    }
}
