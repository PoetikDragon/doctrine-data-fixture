<?php

declare(strict_types=1);

namespace Db\Fixture;

use Db\Entity\Artist;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ApiSkeletons\Doctrine\DataFixture\Module;

class DependencyFixture extends AbstractFixture implements FixtureInterface
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
        $this->addReference(Module::class, new Artist);
        return true;
    }
}
