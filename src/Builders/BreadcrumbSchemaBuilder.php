<?php

declare(strict_types=1);

namespace ProductSchema\Builders;

use ProductSchema\Contracts\UrlGenerator;
use ProductSchema\DTO\Product;

final readonly class BreadcrumbSchemaBuilder
{
    public function __construct(private UrlGenerator $urls)
    {
    }

    /** @return array<string, mixed> */
    public function build(Product $product): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                $this->item(1, 'Shop', $this->urls->shop()),
                $this->item(2, $product->name, $this->urls->product($product->slug)),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function item(int $position, string $name, string $url): array
    {
        return [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $name,
            'item' => $url,
        ];
    }
}
