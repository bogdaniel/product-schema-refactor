<?php

declare(strict_types=1);

namespace ProductSchema\Builders;

use ProductSchema\DTO\Product;
use ProductSchema\DTO\QuestionAnswer;

final class FaqSchemaBuilder
{
    /** @return array<string, mixed>|null */
    public function build(Product $product): ?array
    {
        $questions = array_values(array_filter(
            $product->questions,
            static fn (QuestionAnswer $item): bool => trim($item->answer) !== '',
        ));

        if ($questions === []) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(
                static fn (QuestionAnswer $item): array => [
                    '@type' => 'Question',
                    'name' => $item->question,
                    'acceptedAnswer' => array_filter([
                        '@type' => 'Answer',
                        'text' => $item->answer,
                        'author' => $item->answeredBy === null ? null : [
                            '@type' => 'Organization',
                            'name' => $item->answeredBy,
                        ],
                    ], static fn (mixed $value): bool => $value !== null),
                ],
                $questions,
            ),
        ];
    }
}
