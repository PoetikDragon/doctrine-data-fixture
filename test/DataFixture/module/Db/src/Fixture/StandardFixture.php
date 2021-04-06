<?php

declare(strict_types=1);

namespace Db\Fixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StandardFixture implements FixtureInterface
{

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $objectManager): bool
    {
        return true;
    }
}
