<?php

declare(strict_types=1);

namespace Db\Fixture;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DependentFixtureFactory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): DependentFixture {
        return new DependentFixture;
    }
}
