<?php

declare(strict_types=1);

namespace Db\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ApiSkeletons\Doctrine\DataFixture\Module;

class DependentFixture extends AbstractFixture implements DependentFixtureInterface
{

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $objectManager): bool
    {
        $this->getReference(Module::class);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getDependencies(): array
    {
        return [
            DependencyFixture::class,
        ];
    }
}
