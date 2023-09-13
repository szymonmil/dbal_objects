<?php

namespace DoctrineMapper\Lib;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DbalObjectsBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}