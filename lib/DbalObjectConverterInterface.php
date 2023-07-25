<?php

namespace DbalObjects\Lib;

use Doctrine\DBAL\Result;

interface DbalObjectConverterInterface
{
    /**
     * @template Class
     *
     * @param class-string<Class> $className
     *
     * @return Class
     */
    public function fetchObject(Result $result, string $className): object;
}