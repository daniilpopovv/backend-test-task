<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Domain\CartItem;
use Raketa\BackendTestTask\Repository\CartManager;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Raketa\BackendTestTask\View\CartView;
use Ramsey\Uuid\Uuid;

readonly class AddItemToCartController
{
    public function __construct(
        private ProductRepository $productRepository,
        private CartView $cartView,
        private CartManager $cartManager,
    ) {
    }

    public function addItem(RequestInterface $request): ResponseInterface
    {
        $rawRequest = json_decode($request->getBody()->getContents(), true);
        $product = $this->productRepository->getByUuid($rawRequest['productUuid']);
        $sessionId = session_id();

        $response = new JsonResponse();

        switch (true) {
            case !$sessionId:
                $response
                    ->withStatus(403)
                    ->setBody(['status' => 'error', 'message' => 'Authorization error']);

                break;
            case !$product:
                $response
                    ->withStatus(404)
                    ->setBody(['status' => 'error', 'message' => 'Product not found']);

                break;
            default:
                $cart = $this->cartManager->getCart($sessionId);
                $cart->addItem(
                    new CartItem(
                        Uuid::uuid4()->toString(),
                        $product,
                        $rawRequest['quantity'],
                    )
                );

                $response
                    ->withStatus(200)
                    ->setBody([
                        'status' => 'success',
                        'cart' => $this->cartView->toArray($cart)
                    ]);

                break;
        }

        return $response;
    }
}
