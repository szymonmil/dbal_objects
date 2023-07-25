<?php

namespace DbalObjects\Lib\Service;

use DbalObjects\Lib\DbalObjectConverterInterface;
use DbalObjects\Lib\Exception\NotTypedClassProperty;
use DbalObjects\Lib\Exception\RequiredParamNotProvided;
use Doctrine\DBAL\Result;
use ReflectionClass;

class DbalObjectConverter implements DbalObjectConverterInterface
{
    /**
     * @template Class
     *
     * @param class-string<Class> $className
     *
     * @return Class
     */
    public function fetchObject(Result $result, string $className): object
    {
        $arrayValue = $result->fetchAssociative();

        $objectToReturn = new $className;

        if (in_array('__construct', get_class_methods($className))) {
            return new $className;
        }

        $objectProperties = array_keys(get_class_vars($className));
        $classReflection = new ReflectionClass($className);

        foreach ($objectProperties as $property) {
            $propertyType = $classReflection->getProperty($property)->getType();

            if ($propertyType === null) {
                throw new NotTypedClassProperty(sprintf(
                    'Object to convert must have all properties typed and property `%s` is not typed',
                    $property
                ));
            }

            if (array_key_exists($property, $arrayValue)) {
                $objectToReturn->$property = $arrayValue[$property];

                continue;
            }

            if (!$propertyType->allowsNull()) {
                throw new RequiredParamNotProvided(sprintf(
                    'Parameter `%s` which is required, is not provided',
                    $property
                ));
            }

            $objectToReturn->$property = null;
        }

        return $objectToReturn;
    }
}