<?php

namespace Things\Application\Service;

use Things\Domain\Model\Thing\Thing;
use Things\Domain\Model\Thing\ThingCannotBeAccessedByUserException;
use Things\Domain\Model\Thing\ThingId;
use Things\Domain\Model\Thing\ThingNotFoundException;
use Things\Domain\Model\Thing\ThingCannotBeSavedException;
use Things\Domain\Model\Thing\ThingRepository;
use Things\Domain\Model\Thing\ThingFactory;
use Things\Domain\Model\User\UserId;

/**
 * Class ThingService
 * @package Things\Application\Service
 */
class ThingService implements ThingFactory
{
    /**
     * @var ThingRepository
     */
    protected $thingRepository;

    /**
     * ThingService constructor.
     * @param ThingRepository $thingRepository
     */
    public function __construct(ThingRepository $thingRepository)
    {
        $this->thingRepository = $thingRepository;
    }

    /**
     * @param UserId $userId
     * @return array
     */
    public function getThingsByUserId(UserId $userId)
    {
        return array_map(
            function (Thing $thing) {
                return $this->transformThing($thing);
            },
            $this->thingRepository->findAllByUserId($userId)
        );
    }

    /**
     * @param string $name
     * @param string $description
     * @param UserId $userId
     * @return array
     * @throws ThingCannotBeSavedException
     */
    public function createThing(string $name, string $description, UserId $userId)
    {
        $thing = $this->buildThing($this->thingRepository->nextId(), $userId, $name, $description);

        if (!$this->thingRepository->save($thing)) {
            throw new ThingCannotBeSavedException;
        }

        return $this->transformThing($thing);
    }

    /**
     * @param ThingId $thingId
     * @param UserId $userId
     * @return array
     * @throws ThingCannotBeAccessedByUserException
     * @throws ThingNotFoundException
     */
    public function getThingById(ThingId $thingId, UserId $userId)
    {
        $thing = $this->thingRepository->findById($thingId);

        $this->checkThingFound($thing);

        $this->checkUserAccess($userId, $thing);

        return $this->transformThing($thing);
    }

    /**
     * @param ThingId $thingId
     * @param UserId $userId
     * @param string|null $name
     * @param string|null $description
     * @return array
     * @throws ThingCannotBeAccessedByUserException
     * @throws ThingNotFoundException
     */
    public function updateThing(ThingId $thingId, UserId $userId, string $name = null, string $description = null)
    {
        $thing = $this->thingRepository->findById($thingId);

        $this->checkThingFound($thing);

        $this->checkUserAccess($userId, $thing);

        if ($name !== null) {
            $thing->setName($name);
        }

        if ($description !== null) {
            $thing->setDescription($description);
        }

        return [
            'success' => $this->thingRepository->update($thing)
        ];
    }

    /**
     * @param ThingId $thingId
     * @param UserId $userId
     * @return array
     * @throws ThingCannotBeAccessedByUserException
     * @throws ThingNotFoundException
     */
    public function deleteThingById(ThingId $thingId, UserId $userId)
    {
        $thing = $this->thingRepository->findById($thingId);

        $this->checkThingFound($thing);

        $this->checkUserAccess($userId, $thing);

        return [
            'success' => $this->thingRepository->delete($thing)
        ];
    }

    /**
     * @param Thing $thing
     * @return array
     */
    protected function transformThing(Thing $thing)
    {
        return [
            'id' => $thing->getThingId()->getId(),
            'name' => $thing->getName(),
            'description' => $thing->getDescription()
        ];
    }

    /**
     * @param ThingId $thingId
     * @param UserId $userId
     * @param string $name
     * @param string $description
     * @return Thing
     */
    public static function buildThing(ThingId $thingId, UserId $userId, string $name, string $description): Thing
    {
        return new Thing($thingId, $userId, $name, $description);
    }

    /**
     * @param UserId $userId
     * @param $thing
     * @throws ThingCannotBeAccessedByUserException
     */
    public function checkUserAccess(UserId $userId, Thing $thing)
    {
        if (!$thing->getUserId()->equals($userId)) {
            throw new ThingCannotBeAccessedByUserException;
        }
    }

    /**
     * @param Thing|null $thing
     * @throws ThingNotFoundException
     */
    public function checkThingFound($thing)
    {
        if ($thing === null) {
            throw new ThingNotFoundException;
        }
    }
}
