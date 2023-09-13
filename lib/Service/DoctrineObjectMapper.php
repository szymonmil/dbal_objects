<?php

namespace DoctrineMapper\Lib\Service;

use DoctrineMapper\Lib\DbalObjectConverterInterface;
use DoctrineMapper\Lib\Exception\NotTypedClassProperty;
use DoctrineMapper\Lib\Exception\RequiredParamNotProvided;
use Doctrine\DBAL\Result;
use ReflectionClass;

class DoctrineObjectMapper implements DbalObjectConverterInterface
{
    public function fetchObject(Result $result, string $className): ?object
    {
        $arrayValue = $result->fetchAssociative();

        if ($arrayValue === false) {
            return null;
        }

        if (in_array('__construct', get_class_methods($className))) {
            return $this->createObjectByConstructor($arrayValue, $className);
        }

        return $this->createObjectByProperties($className, $arrayValue);
    }

    /**
     * @param array<string, mixed> $arrayValue
     *
     * @template TClass of object
     * @param class-string<TClass> $className
     *
     * @return TClass
     */
    private function createObjectByProperties(string $className, array $arrayValue): object
    {
        $objectProperties = array_keys(get_class_vars($className));
        $classReflection = new ReflectionClass($className);

        $objectToReturn = new $className;

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

    /**
     * @param array<string, mixed> $arrayValue
     *
     * @template TClass of object
     * @param class-string<TClass> $className
     *
     * @return TClass
     */
    private function createObjectByConstructor(array $arrayValue, string $className): object
    {
        $classReflection = new ReflectionClass($className);
        $classConstructorParams = $classReflection->getConstructor()->getParameters();

        $parametersToPass = [];

        foreach ($classConstructorParams as $constructorParam) {
            $parametersToPass[] = $arrayValue[$constructorParam->getName()];
        }

        return $classReflection->newInstance(...$parametersToPass);
    }
}