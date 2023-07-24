<?php

namespace DbalObjects\Tests\Functional;

use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

class DbalObjectConverterDatabaseTest extends TestCase
{
    public function testX(): void
    {
        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/db.sqlite',
        ]);

        $result = $connection->executeQuery('SELECT * FROM users');

        var_dump($result->fetchAllAssociative());
    }
}