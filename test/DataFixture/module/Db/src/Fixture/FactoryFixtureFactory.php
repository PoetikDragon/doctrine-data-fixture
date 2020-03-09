<?php

declare(strict_types=1);

namespace Db\Fixture;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FactoryFixtureFactory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): FactoryFixture {
        return new FactoryFixture(true);
    }
}
