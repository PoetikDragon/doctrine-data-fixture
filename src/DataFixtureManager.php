<?php

declare(strict_types=1);

namespace ApiSkeletons\Doctrine\DataFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;

class DataFixtureManager extends AbstractPluginManager implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @inheritdoc
     */
    protected $instanceOf = FixtureInterface::class;

    /**
     * @var string
     */
    protected $objectManagerAlias;

    /**
     * @var array
     */
    private $options = [];

    public function __construct(ContainerInterface $container, array $config, $options = [])
    {
        parent::__construct($container, $config);

        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get all data fixtures
     *
     * @return array
     */
    public function getAll(): array
    {
        $fixtures = [];

        foreach ($this->factories as $name => $squishedName) {
            $fixtures[] = $this->get($name);
        }

        return $fixtures;
    }

    /**
     * Get the object manager alias
     *
     * @return string
     */
    public function getObjectManagerAlias(): string
    {
        return $this->objectManagerAlias;
    }

    /**
     * Set the object manager alias
     *
     * @param string $alias
     *
     * @return void
     */
    public function setObjectManagerAlias(string $alias): void
    {
        $this->objectManagerAlias = $alias;
    }
}
