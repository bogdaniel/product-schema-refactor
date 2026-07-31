<?php

declare(strict_types=1);

namespace ProductSchema\DTO;

final readonly class Product
{
    /**
     * @param list<string> $gallery
     * @param list<Variant> $variants
     * @param list<Review> $reviews
     * @param list<QuestionAnswer> $questions
     */
    public function __construct(
        public string $name,
        public string $slug,
        public string $description,
        public string $currency,
        public int $priceInCents,
        public ?string $imageUrl = null,
        public array $gallery = [],
        public ?string $sku = null,
        public ?string $brand = null,
        public bool $inStock = true,
        public array $variants = [],
        public array $reviews = [],
        public array $questions = [],
    ) {
    }
}
