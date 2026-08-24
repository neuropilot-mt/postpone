<?php

namespace App\Http\Controllers;

use App\Enums\Platform;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        assert($user instanceof User);

        $upcoming = $user->posts()
            ->with(['media', 'targets'])
            ->where('status', PostStatus::Scheduled)
            ->orderBy('scheduled_at')
            ->limit(8)
            ->get();

        $recent = $user->posts()
            ->with(['media', 'targets'])
            ->latest()
            ->limit(6)
            ->get();

        return Inertia::render('dashboard', [
            'stats' => [
                'scheduled' => $user->posts()->where('status', PostStatus::Scheduled)->count(),
                'published' => $user->posts()->where('status', PostStatus::Published)->count(),
                'failed' => $user->posts()->whereIn('status', [PostStatus::Failed, PostStatus::Partial])->count(),
                'accounts' => $user->socialAccounts()->where('is_active', true)->count(),
            ],
            'connected_platforms' => $user->socialAccounts()
                ->where('is_active', true)
                ->get()
                ->map(fn (SocialAccount $account): string => $account->platform->value)
                ->unique()
                ->values(),
            'platforms' => collect(Platform::cases())->map(fn (Platform $platform): array => [
                'value' => $platform->value,
                'label' => $platform->label(),
            ]),
            'upcoming' => $upcoming->map(fn (Post $post): array => $this->summary($post)),
            'recent' => $recent->map(fn (Post $post): array => $this->summary($post)),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function summary(Post $post): array
    {
        return [
            'id' => $post->id,
            'caption' => $post->caption,
            'status' => $post->status->value,
            'status_label' => $post->status->label(),
            'scheduled_at' => $post->scheduled_at?->timezone(config('app.timezone'))->format('d.m.Y H:i'),
            'platforms' => $post->targets->map(fn ($target): string => $target->platform->value)->all(),
            'has_video' => $post->hasVideo(),
            'has_photo' => $post->hasPhoto(),
        ];
    }
}
