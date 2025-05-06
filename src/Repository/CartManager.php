<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Infrastructure\RedisConnectorFacade;
use Throwable;

readonly class CartManager extends RedisConnectorFacade
{
    private const DB_INDEX = 1;

    public function __construct(string $host, int $port, ?string $password, LoggerInterface $logger)
    {
        parent::__construct($host, $port, $password, self::DB_INDEX, $logger);
    }

    /**
     * @throws Throwable
     */
    public function saveCart(Cart $cart, string $sessionId): void
    {
        $this->redisConnector->set($sessionId, $cart);
    }

    public function getCart(string $sessionId): ?Cart
    {
        try {
            return $this->redisConnector->get($sessionId);
        } catch (Throwable) {
            return null;
        }
    }
}
