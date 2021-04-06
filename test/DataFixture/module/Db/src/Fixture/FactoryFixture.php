<?php

declare(strict_types=1);

namespace Db\Fixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FactoryFixture implements FixtureInterface
{

    /**
     * Constructor
     *
     * @param bool $requiredArgument
     */
    public function __construct(bool $requiredArgument)
    {
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $objectManager): bool
    {
        return true;
    }
}
