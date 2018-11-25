<?php

namespace Test\DataFixtures;

use App\DataFixtures\FixtureLoadable;
use Doctrine\ORM\EntityManager;

class DataFixtureLoaderMock
{
    use FixtureLoadable;

    public function load(EntityManager $entityManager, array $fixtures)
    {
        $this->loadFixtures($entityManager, $fixtures);
    }

    protected function purgeDatabase(): void
    {
    }
}
