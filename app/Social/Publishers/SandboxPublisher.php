<?php

namespace App\Social\Publishers;

use App\Models\Post;
use App\Models\SocialAccount;
use App\Social\Contracts\SocialPublisher;
use App\Social\PublishResult;
use Illuminate\Support\Str;

class SandboxPublisher implements SocialPublisher
{
    public function publish(Post $post, SocialAccount $account): PublishResult
    {
        $id = Str::uuid()->toString();

        return PublishResult::ok(
            $id,
            'https://sandbox.local/'.$account->platform->value.'/'.$id,
        );
    }
}
