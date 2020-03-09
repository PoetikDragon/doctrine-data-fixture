<?php

declare(strict_types=1);

namespace ApiSkeletons\Doctrine\DataFixture;

class ConfigProvider
{
    /**
     * Return the full configuration
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Get the dependency configuration
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            'aliases'   => [
                'doctrine.data-fixture.import' => Commands\ImportCommand::class,
                'doctrine.data-fixture.list'   => Commands\ListCommand::class,
            ],
            'factories' => [
                Commands\ImportCommand::class => Commands\CommandFactory::class,
                Commands\ListCommand::class   => Commands\CommandFactory::class,
                DataFixtureManager::class     => DataFixtureManagerFactory::class,
            ],
        ];
    }
}
