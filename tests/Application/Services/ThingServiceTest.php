<?php

namespace Things\Application\Service;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Assert;
use Things\Domain\Model\Thing\Thing;
use Things\Domain\Model\Thing\ThingId;
use Things\Domain\Model\Thing\ThingRepository;
use Things\Domain\Model\User\UserId;

class ThingServiceTest extends TestCase
{
    /**
     * @var ThingService
     */
    protected $thingService;

    public function setUp()
    {
        parent::setUp();

        $this->thingService = new class(
            self::createMock(ThingRepository::class)
        ) extends ThingService{
            public function setThingRepository($thingRepository)
            {
                $this->thingRepository = $thingRepository;
            }
        };
    }

    public function testGetThingsByUserId()
    {
        $userId = new UserId;
        $thingRepository = self::createMock(ThingRepository::class);
        $thingRepository->method('findAllByUserId')
            ->willReturn([
                new Thing(
                    new ThingId,
                    $userId,
                    'Thing #1',
                    'This is thing #1'
                ),
                new Thing(
                    new ThingId,
                    $userId,
                    'Thing #2',
                    'This is thing #2'
                )
            ]);
        $this->thingService->setThingRepository($thingRepository);
        $response = $this->thingService->getThingsByUserId($userId);
        Assert::assertCount(2, $response);
        Assert::assertEquals('Thing #1', $response[0]['name']);
        Assert::assertEquals('This is thing #1', $response[0]['description']);
        Assert::assertEquals('Thing #2', $response[1]['name']);
        Assert::assertEquals('This is thing #2', $response[1]['description']);
    }

    public function testCreateThing()
    {
        $userId = new UserId;
        $thingRepository = self::createMock(ThingRepository::class);
        $thingRepository->method('save')
            ->willReturn(true);
        $this->thingService->setThingRepository($thingRepository);
        $response = $this->thingService->createThing('Thing #1', 'This is thing #1', $userId);
        Assert::assertEquals('Thing #1', $response['name']);
        Assert::assertEquals('This is thing #1', $response['description']);
    }

    /**
     * @expectedException \Things\Domain\Model\Thing\ThingCannotBeSavedException
     */
    public function testCreateThingThingCannotBeSavedException()
    {
        $userId = new UserId;
        $thingRepository = self::createMock(ThingRepository::class);
        $thingRepository->method('save')
            ->willReturn(false);
        $this->thingService->setThingRepository($thingRepository);
        $this->thingService->createThing('Thing #1', 'This is thing #1', $userId);
    }

    public function testGetThingById()
    {
        $thingId = new ThingId;
        $userId = new UserId;
        $thingRepository = self::createMock(ThingRepository::class);
        $thingRepository->method('findById')
            ->willReturn(new Thing(
                $thingId,
                $userId,
                'Thing #1',
                'This is thing #1'
            ));
        $this->thingService->setThingRepository($thingRepository);
        $response = $this->thingService->getThingById($thingId, $userId);
    }

    public function testUpdateThing()
    {
        $userId = new UserId;
        $thingId = new ThingId;
        $thingRepository = self::createMock(ThingRepository::class);
        $thingRepository->method('findById')->willReturn(new Thing(
            $thingId,
            $userId,
            'Thing #1',
            'This is thing #1'
        ));
        $thingRepository->method('delete')->willReturn(true);
        $this->thingService->setThingRepository($thingRepository);
        $response = $this->thingService->updateThing($thingId, $userId, 'Thing #1', 'This is thing #1');
        Assert::assertTrue(isset($response['success']));
    }

    public function testDeleteThingById()
    {
        $userId = new UserId;
        $thingId = new ThingId;
        $thingRepository = self::createMock(ThingRepository::class);
        $thingRepository->method('findById')->willReturn(new Thing(
            $thingId,
            $userId,
            'Thing #1',
            'This is thing #1'
        ));
        $thingRepository->method('delete')->willReturn(true);
        $this->thingService->setThingRepository($thingRepository);
        $response = $this->thingService->deleteThingById(new ThingId, $userId);
        Assert::assertTrue(isset($response['success']));
    }

    public function testCheckUserAccess()
    {
        $userId = new UserId;
        $thing = new Thing(
            new ThingId,
            $userId,
            'Thing #1',
            'This is thing #1'
        );

        $this->thingService->checkUserAccess($userId, $thing);
    }

    /**
     * @expectedException \Things\Domain\Model\Thing\ThingCannotBeAccessedByUserException
     */
    public function testCheckUserAccessThingCannotBeAccessedByUserException()
    {
        $thing = new Thing(
            new ThingId,
            new UserId,
            'Thing #1',
            'This is thing #1'
        );

        $this->thingService->checkUserAccess(new UserId, $thing);
    }

    public function testCheckThingFound()
    {
        $this->thingService->checkThingFound(new Thing(new ThingId, new UserId, 'Thing #1', 'This is thing #1'));
    }

    /**
     * @expectedException \Things\Domain\Model\Thing\ThingNotFoundException
     */
    public function testCheckThingFoundThingNotFoundException()
    {
        $this->thingService->checkThingFound(null);
    }
}