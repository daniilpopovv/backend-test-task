<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartItem;

readonly class CartView
{
    public function toArray(Cart $cart): array
    {
        $customer = $cart->getCustomer();

        return [
            'uuid' => $cart->getUuid(),
            'customer' => [
                'id' => $customer->getId(),
                'name' => implode(' ', [
                    $customer->getLastName(),
                    $customer->getFirstName(),
                    $customer->getMiddleName(),
                ]),
                'email' => $customer->getEmail(),
            ],
            'payment_method' => $cart->getPaymentMethod(),
            'items' => array_map(function (CartItem $cartItem): array {
                $product = $cartItem->getProduct();

                return [
                    'uuid' => $cartItem->getUuid(),
                    'total' => $cartItem->getTotal(),
                    'quantity' => $cartItem->getQuantity(),
                    'product' => [
                        'id' => $product->getId(),
                        'uuid' => $product->getUuid(),
                        'name' => $product->getName(),
                        'thumbnail' => $product->getThumbnail(),
                        'price' => $product->getPrice(),
                    ],
                ];
            }, $cart->getItems()),
            'total' => $cart->getTotal(),
        ];
    }
}
