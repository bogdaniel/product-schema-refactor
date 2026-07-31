<?php

declare(strict_types=1);

namespace ProductSchema\Builders;

use ProductSchema\DTO\Product;

final readonly class ProductSchemaBuilder
{
    public function __construct(
        private OfferSchemaBuilder $offers,
        private ReviewSchemaBuilder $reviews,
    ) {
    }

    /** @return array<string, mixed> */
    public function build(Product $product, string $fallbackBrand): array
    {
        $images = array_values(array_filter(
            [$product->imageUrl, ...$product->gallery],
            static fn (?string $image): bool => $image !== null && $image !== '',
        ));

        return array_merge([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->description,
            'image' => $images,
            'sku' => $product->sku ?? $product->slug,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand ?? $fallbackBrand,
            ],
            'offers' => $this->offers->build($product),
        ], $this->reviews->build($product));
    }
}
