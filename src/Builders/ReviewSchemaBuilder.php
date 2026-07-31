<?php

declare(strict_types=1);

namespace ProductSchema\Builders;

use ProductSchema\DTO\Product;
use ProductSchema\DTO\Review;

final class ReviewSchemaBuilder
{
    /** @return array<string, mixed> */
    public function build(Product $product): array
    {
        if ($product->reviews === []) {
            return [];
        }

        $ratings = array_map(static fn (Review $review): int => $review->rating, $product->reviews);

        return [
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => round(array_sum($ratings) / count($ratings), 2),
                'reviewCount' => count($ratings),
                'bestRating' => 5,
                'worstRating' => 1,
            ],
            'review' => array_map(
                static fn (Review $review): array => array_filter([
                    '@type' => 'Review',
                    'author' => ['@type' => 'Person', 'name' => $review->author],
                    'datePublished' => $review->publishedAt,
                    'reviewBody' => $review->body,
                    'name' => $review->title,
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => $review->rating,
                        'bestRating' => 5,
                        'worstRating' => 1,
                    ],
                ], static fn (mixed $value): bool => $value !== null),
                array_slice($product->reviews, 0, 10),
            ),
        ];
    }
}
