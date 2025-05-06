<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure;

use Psr\Log\LoggerInterface;
use Redis;
use RedisException;

readonly class RedisConnectorFacade
{
    protected RedisConnector $redisConnector;

    public function __construct(
        private string $host,
        private int $port = 6379,
        private ?string $password = null,
        private ?int $dbindex = null,
        protected LoggerInterface $logger
    ) {
        try {
            $redis = new Redis();

            $isConnected = $redis->isConnected();
            if (!$isConnected && $redis->ping('Ping')) {
                $isConnected = $redis->connect(
                    $this->host,
                    $this->port,
                );
            }

            if ($isConnected) {
                $redis->auth($this->password);
                $redis->select($this->dbindex);
                $this->redisConnector = new RedisConnector($redis, $logger);
            }
        } catch (RedisException $exception) {
            $this->logger->error($exception->getMessage(), [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTrace(),
            ]);
        }
    }
}
