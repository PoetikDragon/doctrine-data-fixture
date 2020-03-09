<?php

declare(strict_types=1);

namespace ApiSkeletons\Doctrine\DataFixture;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DataFixtureManagerFactory implements FactoryInterface
{

    const OPTION_GROUP = 'group';

    /**
     * @inheritdoc
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): DataFixtureManager {

        // Load the fixture group
        $config             = $container->get('config');
        $fixtureGroup       = $this->getFixtureGroup($options);
        $groupConfig        = $this->getGroupConfig($config, $fixtureGroup);
        $objectManagerAlias = $this->getObjectManagerAlias($groupConfig, $fixtureGroup);

        /**
         * @var DataFixtureManager $instance
         */
        $instance = new $requestedName($container, $groupConfig);
        $instance->setObjectManagerAlias($objectManagerAlias);
        $instance->setObjectManager($container->get($objectManagerAlias));

        return $instance;
    }

    /**
     * Get the fixture group name
     *
     * @param array|null $options
     *
     * @return string
     */
    protected function getFixtureGroup(array $options = null): string
    {
        $fixtureGroup = (string)$options[self::OPTION_GROUP];
        if (! $fixtureGroup) {
            throw new \RuntimeException('No fixture group given.');
        }

        return $fixtureGroup;
    }

    /**
     * Get the fixture group configuration
     *
     * @param array  $config
     * @param string $fixtureGroup
     *
     * @return array
     */
    protected function getGroupConfig(array $config, string $fixtureGroup): array
    {
        $groupConfig = null;
        if (isset($config['doctrine']['fixture'][$fixtureGroup])) {
            $groupConfig = (array)$config['doctrine']['fixture'][$fixtureGroup];
        }

        if (! $groupConfig) {
            throw new \RuntimeException(sprintf(
                'Fixture group not found: %s',
                $fixtureGroup
            ));
        }

        return $groupConfig;
    }

    /**
     * Get the object manager alias
     *
     * @param array  $groupConfig
     * @param string $fixtureGroup
     *
     * @return string
     */
    protected function getObjectManagerAlias(array $groupConfig, string $fixtureGroup): string
    {
        $objectManagerAlias = (string)$groupConfig['object_manager'];
        if (! $objectManagerAlias) {
            throw new \RuntimeException(sprintf(
                'Object manager not specified for fixture group %s',
                $fixtureGroup
            ));
        }

        return $objectManagerAlias;
    }
}
