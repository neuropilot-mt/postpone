<?php

namespace App\Models;

use App\Enums\InstagramFormat;
use App\Enums\MediaType;
use App\Enums\PostStatus;
use App\Enums\TargetStatus;
use Carbon\CarbonImmutable;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $caption
 * @property PostStatus $status
 * @property InstagramFormat|null $instagram_format
 * @property CarbonImmutable|null $scheduled_at
 * @property CarbonImmutable|null $published_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, PostMedia> $media
 * @property-read Collection<int, PostTarget> $targets
 */
#[Fillable([
    'user_id',
    'caption',
    'status',
    'instagram_format',
    'scheduled_at',
    'published_at',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'instagram_format' => InstagramFormat::class,
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<PostMedia, $this>
     */
    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<PostTarget, $this>
     */
    public function targets(): HasMany
    {
        return $this->hasMany(PostTarget::class);
    }

    public function hasVideo(): bool
    {
        return $this->media->contains(fn (PostMedia $media): bool => $media->type === MediaType::Video);
    }

    public function hasPhoto(): bool
    {
        return $this->media->contains(fn (PostMedia $media): bool => $media->type === MediaType::Photo);
    }

    public function firstVideo(): ?PostMedia
    {
        return $this->media->first(fn (PostMedia $media): bool => $media->type === MediaType::Video);
    }

    /**
     * @return Collection<int, PostMedia>
     */
    public function photos(): Collection
    {
        return $this->media->filter(fn (PostMedia $media): bool => $media->type === MediaType::Photo)->values();
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeDue(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Scheduled)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now());
    }

    public function refreshAggregateStatus(): void
    {
        $this->load('targets');

        $statuses = $this->targets->pluck('status');

        if ($statuses->every(fn ($status) => $status === TargetStatus::Published)) {
            $this->status = PostStatus::Published;
            $this->published_at ??= now();
        } elseif ($statuses->contains(TargetStatus::Published) && $statuses->contains(TargetStatus::Failed)) {
            $this->status = PostStatus::Partial;
        } elseif ($statuses->every(fn ($status) => $status === TargetStatus::Failed)) {
            $this->status = PostStatus::Failed;
        } elseif ($statuses->contains(TargetStatus::Publishing) || $statuses->contains(TargetStatus::Pending)) {
            $this->status = PostStatus::Publishing;
        }

        $this->save();
    }
}
