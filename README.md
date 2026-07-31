# Product Schema Refactor

A self-contained PHP 8.3+ refactor of a product JSON-LD schema helper, designed around PSR-12, SOLID, DRY, dependency injection, immutable DTOs, static analysis, and automated tests.

## What changed

The original static helper combined product, offer, review, FAQ, breadcrumb, URL generation, filtering, and money conversion in one class. This version separates those responsibilities into focused builders:

- `ProductSchemaBuilder`
- `OfferSchemaBuilder`
- `ReviewSchemaBuilder`
- `FaqSchemaBuilder`
- `BreadcrumbSchemaBuilder`
- `ProductSchema` facade

Framework-specific URL generation is hidden behind the `UrlGenerator` interface, so the schema code can be tested without booting Laravel.

## Requirements

- PHP 8.3 or newer
- Composer 2

## Install and verify

```bash
composer install
composer check
```

The quality pipeline runs:

```bash
vendor/bin/pint --test
vendor/bin/phpstan analyse
vendor/bin/phpunit
```

## Usage

```php
use ProductSchema\Builders\BreadcrumbSchemaBuilder;
use ProductSchema\Builders\FaqSchemaBuilder;
use ProductSchema\Builders\OfferSchemaBuilder;
use ProductSchema\Builders\ProductSchemaBuilder;
use ProductSchema\Builders\ReviewSchemaBuilder;
use ProductSchema\Contracts\UrlGenerator;
use ProductSchema\DTO\Product;
use ProductSchema\ProductSchema;

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

$schema = new ProductSchema(
    new ProductSchemaBuilder(
        new OfferSchemaBuilder($urls),
        new ReviewSchemaBuilder(),
    ),
    new BreadcrumbSchemaBuilder($urls),
    new FaqSchemaBuilder(),
);

$product = new Product(
    name: 'Example product',
    slug: 'example-product',
    description: 'Example description',
    currency: 'EUR',
    priceInCents: 1299,
);

$productJsonLd = $schema->product($product, 'Fallback Brand');
$breadcrumbJsonLd = $schema->breadcrumb($product);
$faqJsonLd = $schema->faq($product);
```

## Laravel adapter

In a Laravel application, implement the URL contract using the router and bind it in a service provider:

```php
use Illuminate\Contracts\Routing\UrlGenerator as LaravelUrlGenerator;
use ProductSchema\Contracts\UrlGenerator;

final readonly class LaravelProductUrlGenerator implements UrlGenerator
{
    public function __construct(private LaravelUrlGenerator $urls)
    {
    }

    public function product(string $slug): string
    {
        return $this->urls->route('shop.show', $slug);
    }

    public function shop(): string
    {
        return $this->urls->to('/shop');
    }
}
```

```php
$this->app->bind(
    UrlGenerator::class,
    LaravelProductUrlGenerator::class,
);
```

## Design decisions

### Single Responsibility Principle

Each builder owns one schema concern. Changing review formatting does not require modifying offer or breadcrumb logic.

### Dependency Inversion Principle

Builders depend on `UrlGenerator`, not Laravel helpers or facades.

### DRY

Money conversion, availability values, breadcrumb item construction, and review mapping live in one place each.

### Immutable input

The DTOs are `readonly`, making schema generation deterministic and easier to reason about.

## Preserved behavior

For aggregate offers, `offerCount` represents all variants, while prices and availability are calculated from in-stock variants. This mirrors the source behavior. Change it to `count($availablePrices)` if the business definition is "currently purchasable offers".

## License

MIT
