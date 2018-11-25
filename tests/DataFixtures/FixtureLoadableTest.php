<?php

namespace Test\DataFixtures;

use App\DataFixtures\Exception\ClassNotExistException;
use App\DataFixtures\Exception\InvalidFixtureClassException;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class FixtureLoadableTest extends TestCase
{
    /**
     * @var DataFixtureLoaderMock
     */
    private $fixtureLoader;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function setUp()
    {
        parent::setUp();

        $this->fixtureLoader = new DataFixtureLoaderMock();
        $this->entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function it_should_throw_exception_on_class_not_exist()
    {
        $this->expectException(ClassNotExistException::class);
        $this->fixtureLoader->load($this->entityManager, ['asd']);
    }

    /**
     * @test
     */
    public function it_should_throw_exception_on_invalid_class()
    {
        $this->expectException(InvalidFixtureClassException::class);
        $this->fixtureLoader->load($this->entityManager, [self::class]);
    }
}
