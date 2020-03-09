<?php

declare(strict_types=1);

namespace Db\Fixture;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DependencyFixtureFactory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): DependencyFixture {
        return new DependencyFixture(true);
    }
}
