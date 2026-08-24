<?php

namespace App\Social\Contracts;

use App\Models\Post;
use App\Models\SocialAccount;
use App\Social\PublishResult;

interface SocialPublisher
{
    public function publish(Post $post, SocialAccount $account): PublishResult;
}
