<?php

namespace DependencyInjection;

use DoctrineMapper\Lib\DbalObjectConverterInterface;
use DoctrineMapper\Lib\DependencyInjection\DbalObjectsExtension;
use DoctrineMapper\Lib\Service\DbalObjectConverter;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ServiceDependencyInjectionTest extends WebTestCase
{
    public function testFetchingInterfaceFromDI(): void
    {
        /** @Given */
        $container = $this->getContainer();

        /** @When */
        $serviceUnderTest = $container->get(DbalObjectConverterInterface::class);

        /** @Then */
        $this->assertInstanceOf(DbalObjectConverter::class, $serviceUnderTest);
    }

    public function testFetchingByTagFromDI(): void
    {
        /** @Given */
        $container = $this->getContainer();

        /** @When */
        $serviceUnderTest = $container->get('doctrine_mapper.dbal_object_converter');

        /** @Then */
        $this->assertInstanceOf(DbalObjectConverter::class, $serviceUnderTest);
    }

    private function getContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $extension = new DbalObjectsExtension();
        $extension->load([], $container);

        $container->compile();

        return $container;
    }
}