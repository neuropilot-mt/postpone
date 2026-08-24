<?php

namespace App\Jobs;

use App\Enums\PostStatus;
use App\Enums\TargetStatus;
use App\Models\Post;
use App\Models\PostTarget;
use App\Social\SocialPublisherManager;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishPostJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 600;

    public function __construct(public Post $post) {}

    public function uniqueId(): string
    {
        return (string) $this->post->id;
    }

    public function handle(SocialPublisherManager $publishers): void
    {
        $post = $this->post->load(['media', 'targets.socialAccount']);
        $post->status = PostStatus::Publishing;
        $post->save();

        foreach ($post->targets as $target) {
            $this->publishTarget($publishers, $post, $target);
        }

        $post->refreshAggregateStatus();
    }

    private function publishTarget(SocialPublisherManager $publishers, Post $post, PostTarget $target): void
    {
        if ($target->status === TargetStatus::Published) {
            return;
        }

        $target->status = TargetStatus::Publishing;
        $target->save();

        $account = $target->socialAccount;

        if ($account === null || ! $account->is_active) {
            $target->markFailed('Социальный аккаунт недоступен.');

            return;
        }

        $result = $publishers->publish($post, $account);

        if ($result->success) {
            $target->markPublished($result->externalId, $result->externalUrl);

            return;
        }

        $target->markFailed($result->error ?? 'Неизвестная ошибка публикации.');
    }
}
