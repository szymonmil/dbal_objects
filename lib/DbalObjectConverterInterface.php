<?php

namespace DbalObjects\Lib;

use Doctrine\DBAL\Result;

interface DbalObjectConverterInterface
{
    /**
     * Fetch single object as an object. You can run it in iterate way with same $result, and you will get next rows
     *
     * @template TClass of object
     * @param class-string<TClass> $className
     *
     * @return TClass | null Returns null if there is no results
     */
    public function fetchObject(Result $result, string $className): ?object;
}