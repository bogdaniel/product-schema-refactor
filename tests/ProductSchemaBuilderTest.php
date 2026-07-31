<?php

declare(strict_types=1);

namespace ProductSchema\Tests;

use PHPUnit\Framework\TestCase;
use ProductSchema\Builders\OfferSchemaBuilder;
use ProductSchema\Builders\ProductSchemaBuilder;
use ProductSchema\Builders\ReviewSchemaBuilder;
use ProductSchema\Contracts\UrlGenerator;
use ProductSchema\DTO\Product;
use ProductSchema\DTO\Review;
use ProductSchema\DTO\Variant;

final class ProductSchemaBuilderTest extends TestCase
{
    public function testItBuildsAProductWithAggregateOfferAndReviews(): void
    {
        $urls = new class implements UrlGenerator {
            public function product(string $slug): string
            {
                return 'https://example.com/shop/' . $slug;
            }

            public function shop(): string
            {
                return 'https://example.com/shop';
            }
        };

        $builder = new ProductSchemaBuilder(
            new OfferSchemaBuilder($urls),
            new ReviewSchemaBuilder(),
        );

        $schema = $builder->build(new Product(
            name: 'Example product',
            slug: 'example-product',
            description: 'Example description',
            currency: 'EUR',
            priceInCents: 1299,
            variants: [
                new Variant('A', 1099, true),
                new Variant('B', 1599, true),
                new Variant('C', 999, false),
            ],
            reviews: [
                new Review('Alice', 5, 'Excellent'),
                new Review('Bob', 3, 'Good'),
            ],
        ), 'Fallback Brand');

        self::assertSame('Product', $schema['@type']);
        self::assertSame('AggregateOffer', $schema['offers']['@type']);
        self::assertSame(10.99, $schema['offers']['lowPrice']);
        self::assertSame(15.99, $schema['offers']['highPrice']);
        self::assertSame(4.0, $schema['aggregateRating']['ratingValue']);
    }
}
