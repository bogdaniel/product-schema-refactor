<?php

declare(strict_types=1);

namespace ProductSchema\DTO;

final readonly class Review
{
    public function __construct(
        public string $author,
        public int $rating,
        public string $body,
        public ?string $title = null,
        public ?string $publishedAt = null,
    ) {
    }
}
