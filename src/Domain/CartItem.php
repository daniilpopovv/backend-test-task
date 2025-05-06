<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain;

use Raketa\BackendTestTask\Repository\Entity\Product;

final readonly class CartItem
{
    public function __construct(
        private string $uuid,
        private Product $product,
        private int $quantity,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotal(): float
    {
        return $this->getProduct()->getPrice() * $this->getQuantity();
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
