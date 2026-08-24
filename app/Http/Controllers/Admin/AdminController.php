<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Platform;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Models\User;
use App\Social\SocialPublisherManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(SocialPublisherManager $publishers): Response
    {
        return Inertia::render('admin/index', [
            'stats' => [
                'users' => User::query()->count(),
                'posts' => Post::query()->count(),
                'accounts' => SocialAccount::query()->count(),
            ],
            'sandbox' => (bool) config('social.sandbox'),
            'oauth' => collect(Platform::cases())->map(fn (Platform $platform): array => [
                'platform' => $platform->value,
                'label' => $platform->label(),
                'configured' => $publishers->oauthConfigured($platform),
            ]),
        ]);
    }

    public function users(): Response
    {
        return Inertia::render('admin/users', [
            'users' => User::query()
                ->withCount(['posts', 'socialAccounts'])
                ->latest()
                ->get()
                ->map(fn (User $user): array => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin,
                    'posts_count' => $user->posts_count,
                    'accounts_count' => $user->social_accounts_count,
                    'created_at' => $user->created_at?->timezone(config('app.timezone'))->format('d.m.Y H:i'),
                ]),
        ]);
    }

    public function toggleAdmin(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();
        assert($actor instanceof User);

        if ($actor->id === $user->id) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Нельзя снять права с самого себя.']);

            return back();
        }

        $user->is_admin = ! $user->is_admin;
        $user->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Права пользователя обновлены.']);

        return back();
    }

    public function posts(): Response
    {
        $posts = Post::query()
            ->with(['user', 'targets', 'media'])
            ->latest()
            ->paginate(30)
            ->through(fn (Post $post): array => [
                'id' => $post->id,
                'caption' => $post->caption,
                'status' => $post->status->value,
                'status_label' => $post->status->label(),
                'author' => $post->user?->name,
                'scheduled_at' => $post->scheduled_at?->timezone(config('app.timezone'))->format('d.m.Y H:i'),
                'platforms' => $post->targets->map(fn ($target): string => $target->platform->label())->all(),
            ]);

        return Inertia::render('admin/posts', [
            'posts' => $posts,
        ]);
    }
}
