<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain;

/**
 * @property array<CartItem> $items
 */
final class Cart
{
    public function __construct(
        readonly private string $uuid,
        readonly private Customer $customer,
        readonly private string $paymentMethod,
        private array $items,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /**
     * @return array<CartItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(CartItem $item): void
    {
        $this->items[] = $item;
    }

    public function getTotal(): float
    {
        return array_reduce(
            $this->getItems(),
            fn(float $carry, CartItem $cartItem): float => $carry + $cartItem->getTotal(),
            0
        );
    }
}
