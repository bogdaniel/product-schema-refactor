<?php

declare(strict_types=1);

namespace ProductSchema\Contracts;

interface UrlGenerator
{
    public function product(string $slug): string;

    public function shop(): string;
}
