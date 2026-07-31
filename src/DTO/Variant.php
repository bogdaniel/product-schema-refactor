<?php

declare(strict_types=1);

namespace ProductSchema\DTO;

final readonly class Variant
{
    public function __construct(
        public string $sku,
        public int $priceInCents,
        public bool $inStock,
    ) {
    }
}
