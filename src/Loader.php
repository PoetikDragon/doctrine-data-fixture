<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace ApiSkeletons\Doctrine\DataFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader as DoctrineLoader;

class Loader extends DoctrineLoader
{

    /**
     * @var DataFixtureManager
     */
    protected $dataFixtureManager;

    /**
     * Constructor
     *
     * @param DataFixtureManager $dataFixtureManager
     */
    public function __construct(DataFixtureManager $dataFixtureManager)
    {
        $this->dataFixtureManager = $dataFixtureManager;
    }

    /**
     * @inheritdoc
     */
    protected function createFixture($class): FixtureInterface
    {
        if ($this->dataFixtureManager->has($class)) {
            return $this->dataFixtureManager->get($class);
        }

        return parent::createFixture($class);
    }
}
