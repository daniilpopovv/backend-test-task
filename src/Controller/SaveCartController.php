<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartItem;
use Raketa\BackendTestTask\Domain\Customer;
use Raketa\BackendTestTask\Repository\CartManager;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Throwable;

readonly class SaveCartController
{
    public function __construct(
        private CartManager $cartManager,
        private ProductRepository $productRepository
    ) {
    }

    public function saveCart(RequestInterface $request): ResponseInterface
    {
        $rawRequest = json_decode($request->getBody()->getContents(), true);
        $sessionId = session_id();

        $response = new JsonResponse();

        if (!$sessionId) {
            $response
                ->withStatus(403)
                ->setBody(['message' => 'Authorization error']);

            return $response;
        }

        try {
            $rawRequestCustomer = $rawRequest['customer'];
            $customer = new Customer(
                $rawRequestCustomer['id'],
                $rawRequestCustomer['first_name'],
                $rawRequestCustomer['last_name'],
                $rawRequestCustomer['middle_name'],
                $rawRequestCustomer['email'],
            );
            $items = array_map(fn(array $cartItem): CartItem => new CartItem(
                $cartItem['uuid'],
                $this->productRepository->getByUuid($cartItem['product_uuid']),
                $cartItem['quantity'],
            ), $rawRequest['items']);

            $cart = new Cart(
                $rawRequest['uuid'],
                $customer,
                $rawRequest['payment_method'],
                $items
            );

            $this->cartManager->saveCart($cart, $sessionId);

            $response
                ->withStatus(201)
                ->setBody(['status' => 'success']);
        } catch (Throwable) {
            $response
                ->withStatus(400)
                ->setBody(['status' => 'error', 'message' => 'Malformed request']);
        }


        return $response;
    }
}