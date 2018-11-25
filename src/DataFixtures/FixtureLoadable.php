<?php

namespace App\DataFixtures;

use App\DataFixtures\Exception\ClassNotExistException;
use App\DataFixtures\Exception\InvalidFixtureClassException;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Test\Api\Exception\UnableSchemaCreateException;

trait FixtureLoadable
{
    protected function loadFixtures(EntityManager $entityManager, array $fixtures)
    {
        $this->purgeDatabase($entityManager);

        foreach ($fixtures as $fixtureClass) {
            $fixture = $this->createFixture($fixtureClass);
            $fixture->load($entityManager);
        }
    }

    protected function purgeDatabase(EntityManager $entityManager): void
    {
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($entityManager);
        $tool->dropSchema($metaData);
        try {
            $tool->createSchema($metaData);
        } catch (ToolsException $e) {
            throw new UnableSchemaCreateException();
        }
    }

    private function createFixture(string $fixtureClass): Fixture
    {
        if (!class_exists($fixtureClass)) {
            throw new ClassNotExistException($fixtureClass);
        }

        $fixture = new $fixtureClass;

        if (!$fixture instanceof Fixture) {
            throw new InvalidFixtureClassException($fixtureClass);
        }

        return $fixture;
    }
}
