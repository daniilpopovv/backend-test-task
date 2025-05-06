<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Repository\CartManager;
use Raketa\BackendTestTask\View\CartView;

readonly class GetCartController
{
    public function __construct(
        private CartView $cartView,
        private CartManager $cartManager
    ) {
    }

    public function get(): ResponseInterface
    {
        $response = new JsonResponse();
        $sessionId = session_id();

        if (!$sessionId) {
            $response
                ->withStatus(403)
                ->setBody(['message' => 'Authorization error']);

            return $response;
        }

        $cart = $this->cartManager->getCart($sessionId);

        if (!$cart) {
            $response
                ->withStatus(404)
                ->setBody(['status' => 'error', 'message' => 'Cart not found']);
        } else {
            $response
                ->withStatus(200)
                ->setBody([
                    'status' => 'success',
                    'cart' => $this->cartView->toArray($cart)
                ]);
        }

        return $response;
    }
}
