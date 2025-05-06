<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure;

use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Redis;
use RedisException;

readonly class RedisConnector
{
    private const DEFAULT_CART_EXPIRE_SECONDS = 24 * 60 * 60;

    public function __construct(private Redis $redis, private LoggerInterface $logger)
    {
    }

    /**
     * @throws ConnectorException
     */
    public function get(string $key): mixed
    {
        try {
            if ($this->redis->isConnected() && $this->has($key)) {
                return unserialize($this->redis->get($key));
            } else {
                return null;
            }
        } catch (RedisException $e) {
            $this->logger->error($e->getMessage(), [
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
            ]);

            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }

    /**
     * @throws ConnectorException
     */
    public function set(string $key, Cart $value): void
    {
        try {
            if ($this->redis->isConnected()) {
                $this->redis->setex($key, self::DEFAULT_CART_EXPIRE_SECONDS, serialize($value));
            } else {
                throw new RedisException('Redis is not connected');
            }
        } catch (RedisException $e) {
            $this->logger->error($e->getMessage(), [
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
            ]);

            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }

    /**
     * @throws ConnectorException
     */
    public function has($key): bool
    {
        try {
            if ($this->redis->isConnected()) {
                return $this->redis->exists($key);
            } else {
                throw new RedisException('Redis is not connected');
            }
        } catch (RedisException $e) {
            $this->logger->error($e->getMessage(), [
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
            ]);

            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }
}
