<?php

namespace App\Models;

use App\Enums\Platform;
use App\Enums\TargetStatus;
use Database\Factories\PostTargetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $post_id
 * @property int $social_account_id
 * @property Platform $platform
 * @property TargetStatus $status
 * @property string|null $external_id
 * @property string|null $external_url
 * @property string|null $error_message
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'post_id',
    'social_account_id',
    'platform',
    'status',
    'external_id',
    'external_url',
    'error_message',
    'published_at',
])]
class PostTarget extends Model
{
    /** @use HasFactory<PostTargetFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'platform' => Platform::class,
            'status' => TargetStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return BelongsTo<SocialAccount, $this>
     */
    public function socialAccount(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function markPublished(?string $externalId = null, ?string $externalUrl = null): void
    {
        $this->forceFill([
            'status' => TargetStatus::Published,
            'external_id' => $externalId,
            'external_url' => $externalUrl,
            'error_message' => null,
            'published_at' => now(),
        ])->save();
    }

    public function markFailed(string $message): void
    {
        $this->forceFill([
            'status' => TargetStatus::Failed,
            'error_message' => $message,
        ])->save();
    }
}
