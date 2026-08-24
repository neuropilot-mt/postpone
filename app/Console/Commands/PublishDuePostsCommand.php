<?php

namespace App\Console\Commands;

use App\Jobs\PublishPostJob;
use App\Models\Post;
use Illuminate\Console\Command;

class PublishDuePostsCommand extends Command
{
    protected $signature = 'posts:publish-due';

    protected $description = 'Publish posts whose scheduled time has arrived';

    public function handle(): int
    {
        $count = 0;

        Post::query()
            ->due()
            ->with(['media', 'targets'])
            ->each(function (Post $post) use (&$count): void {
                PublishPostJob::dispatch($post);
                $count++;
            });

        $this->info("Queued {$count} due posts.");

        return self::SUCCESS;
    }
}
