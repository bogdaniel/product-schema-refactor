<?php

declare(strict_types=1);

namespace ProductSchema\Builders;

use ProductSchema\Contracts\UrlGenerator;
use ProductSchema\DTO\Product;

final readonly class OfferSchemaBuilder
{
    private const IN_STOCK = 'https://schema.org/InStock';

    private const OUT_OF_STOCK = 'https://schema.org/OutOfStock';

    public function __construct(private UrlGenerator $urls)
    {
    }

    /** @return array<string, mixed> */
    public function build(Product $product): array
    {
        if ($product->variants === []) {
            return [
                '@type' => 'Offer',
                'priceCurrency' => $product->currency,
                'price' => $this->price($product->priceInCents),
                'availability' => $product->inStock ? self::IN_STOCK : self::OUT_OF_STOCK,
                'url' => $this->urls->product($product->slug),
            ];
        }

        $availablePrices = [];

        foreach ($product->variants as $variant) {
            if ($variant->inStock) {
                $availablePrices[] = $this->price($variant->priceInCents);
            }
        }

        $fallbackPrice = $this->price($product->priceInCents);

        return [
            '@type' => 'AggregateOffer',
            'priceCurrency' => $product->currency,
            'lowPrice' => $availablePrices === [] ? $fallbackPrice : min($availablePrices),
            'highPrice' => $availablePrices === [] ? $fallbackPrice : max($availablePrices),
            'offerCount' => count($product->variants),
            'availability' => $availablePrices === [] ? self::OUT_OF_STOCK : self::IN_STOCK,
        ];
    }

    private function price(int $cents): float
    {
        return $cents / 100;
    }
}
