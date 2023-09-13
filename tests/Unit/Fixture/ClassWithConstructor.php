<?php

namespace DoctrineMapper\Tests\Unit\Fixture;

class ClassWithConstructor
{
    private int $id;
    private string $name;
    private ?float $age = null;

    public function __construct(int $id, string $name, ?float $age = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->age = $age;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): ?float
    {
        return $this->age;
    }
}