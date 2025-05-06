<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Enum\ProductCategoryType;
use Raketa\BackendTestTask\Repository\Entity\Product;
use Raketa\BackendTestTask\Repository\ProductRepository;

readonly class ProductsView
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function toArray(ProductCategoryType $category): array
    {
        return array_map(
            fn(Product $product): array => [
                'id' => $product->getId(),
                'uuid' => $product->getUuid(),
                'category' => $product->getCategory()->value,
                'description' => $product->getDescription(),
                'thumbnail' => $product->getThumbnail(),
                'price' => $product->getPrice(),
            ],
            $this->productRepository->getByCategory($category)
        );
    }
}
