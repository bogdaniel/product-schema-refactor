<?php

declare(strict_types=1);

namespace ProductSchema;

use ProductSchema\Builders\BreadcrumbSchemaBuilder;
use ProductSchema\Builders\FaqSchemaBuilder;
use ProductSchema\Builders\ProductSchemaBuilder;
use ProductSchema\DTO\Product;

final readonly class ProductSchema
{
    public function __construct(
        private ProductSchemaBuilder $products,
        private BreadcrumbSchemaBuilder $breadcrumbs,
        private FaqSchemaBuilder $faqs,
    ) {
    }

    /** @return array<string, mixed> */
    public function product(Product $product, string $fallbackBrand): array
    {
        return $this->products->build($product, $fallbackBrand);
    }

    /** @return array<string, mixed> */
    public function breadcrumb(Product $product): array
    {
        return $this->breadcrumbs->build($product);
    }

    /** @return array<string, mixed>|null */
    public function faq(Product $product): ?array
    {
        return $this->faqs->build($product);
    }
}
