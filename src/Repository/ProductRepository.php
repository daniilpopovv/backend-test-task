<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\Connection;
use Exception;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Enum\ProductCategoryType;
use Raketa\BackendTestTask\Repository\Entity\Product;

readonly class ProductRepository
{
    public function __construct(private Connection $connection, private LoggerInterface $logger)
    {
    }


    public function getByUuid(string $uuid): ?Product
    {
        try {
            $row = $this->connection->fetchAssociative(
                "SELECT * FROM products WHERE uuid = ?",
                [$uuid]
            );
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTrace(),
            ]);

            return null;
        }

        if (!$row) {
            return null;
        }

        return $this->make($row);
    }

    public function getByCategory(ProductCategoryType $category): array
    {
        try {
            return array_map(
                fn(array $row): Product => $this->make($row),
                $this->connection->fetchAllAssociative(
                    "SELECT id FROM products WHERE is_active = 1 AND category = ?",
                    [$category->value]
                )
            );
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTrace(),
            ]);

            return [];
        }
    }

    public function make(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uuid'],
            $row['is_active'],
            ProductCategoryType::from($row['category']),
            $row['name'],
            $row['description'],
            $row['thumbnail'],
            $row['price'],
        );
    }
}
