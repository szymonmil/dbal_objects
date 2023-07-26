# Doctrine (DBAL) Mapper
Doctrine Mapper is package used to map results of native SQL queries made via Doctrine DBAL, into custom DTOs.

## Installation

### 1. Using Composer

run `composer require szymonmil/doctrine-mapper`

## Usage

### Mapping single result into DTO with public properties

#### 1. Create DTO class which will represent result of DBAL query result
All properties must have same name (or be a camel case version of snake case name) as fields returned from DBAL query! 
```php
namespace Some\Namespace;

class Person
{
    public int $id;
    
    public string $name;
    
    public bool $isMarried;
    
    public ?int $age = null;
}
```

#### 2. Write your SQL query, get result from DBAL and pass it to DoctrineObjectMapper
```php
$sql = <<<SQL
    SELECT
        person.id AS id,
        person.name AS name,
        person.is_married AS isMarried,
        person.age AS age
    FROM person
SQL;

$result = $connection->executeQuery($sql);

$doctrineMapper = new \DoctrineMapper\Lib\Service\DoctrineObjectMapper() // You can also inject this service in Symfony DI

$firstPerson = $doctrineMapper->fetchObject($result, Some\Namespace\Person::class);
```

#### 3. Now you have User object in $firstPerson variable, mapped from query.