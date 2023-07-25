<?php

namespace DbalObjects\Tests\Integration;

use DbalObjects\Lib\Service\DbalObjectConverter;
use DbalObjects\Tests\Integration\Fixture\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

class DbalObjectConverterDatabaseTest extends TestCase
{
    private Connection $connection;
    private DbalObjectConverter $serviceUnderTest;

    protected function tearDown(): void
    {
        $this->connection->executeStatement('DELETE FROM users');
    }

    protected function setUp(): void
    {
        $this->connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/db.sqlite',
        ]);

        $this->serviceUnderTest = new DbalObjectConverter();
    }

    public function testFetchSingleObject(): void
    {
        /** @Given */
        $this->loadRecordToDatabase(1, 'John', 'example token');

        $result = $this->connection->executeQuery('SELECT * FROM users');

        $expectedObject = new User();
        $expectedObject->id = 1;
        $expectedObject->name = 'John';
        $expectedObject->token = 'example token';

        /** @When */
        $actual = $this->serviceUnderTest->fetchObject($result, User::class);

        /** @Then */
        $this->assertEquals($expectedObject, $actual);
    }

    public function testFetchSingleObjectsOneByOne(): void
    {
        /** @Given */
        $this->loadRecordToDatabase(1, 'John', 'example token');
        $this->loadRecordToDatabase(2, 'Alice', null);
        $this->loadRecordToDatabase(3, 'Bob',  '');

        $result = $this->connection->executeQuery('SELECT * FROM users');

        $firstExpected = new User();
        $firstExpected->id = 1;
        $firstExpected->name = 'John';
        $firstExpected->token = 'example token';

        $secondExpected = new User();
        $secondExpected->id = 2;
        $secondExpected->name = 'Alice';
        $secondExpected->token = null;

        $thirdExpected = new User();
        $thirdExpected->id = 3;
        $thirdExpected->name = 'Bob';
        $thirdExpected->token = '';

        /** @When */
        $firstActual = $this->serviceUnderTest->fetchObject($result, User::class);
        $secondActual = $this->serviceUnderTest->fetchObject($result, User::class);
        $thirdActual = $this->serviceUnderTest->fetchObject($result, User::class);

        /** @Then */
        $this->assertEquals($firstExpected, $firstActual);
        $this->assertEquals($secondExpected, $secondActual);
        $this->assertEquals($thirdExpected, $thirdActual);
    }

    /**
     * @description Loads record to database as a test fixture
     */
    private function loadRecordToDatabase(int $id, string $name, ?string $token): void
    {
        $this->connection->insert('users', [
            'id' => $id,
            'name' => $name,
            'token' => $token
        ]);
    }
}