<?php

namespace App\Social;

final readonly class PublishResult
{
    public function __construct(
        public bool $success,
        public ?string $externalId = null,
        public ?string $externalUrl = null,
        public ?string $error = null,
    ) {}

    public static function ok(?string $externalId = null, ?string $externalUrl = null): self
    {
        return new self(true, $externalId, $externalUrl);
    }

    public static function failed(string $error): self
    {
        return new self(false, error: $error);
    }
}
