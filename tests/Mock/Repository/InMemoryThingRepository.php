<?php

namespace Things\Mock\Repository;

use Things\Domain\Model\Thing\Thing;
use Things\Domain\Model\Thing\ThingId;
use Things\Domain\Model\Thing\ThingRepository;
use Things\Domain\Model\User\UserId;

/**
 * Class InMemoryThingRepository
 * @package Things\Mock\Repository
 */
class InMemoryThingRepository implements ThingRepository
{
    /**
     * @var Thing[]
     */
    protected $things = [];

    /**
     * @param UserId $userId
     * @return Thing[]
     */
    public function findAllByUserId(UserId $userId)
    {
        return array_filter(
            $this->things,
            function (Thing $thing) use ($userId) {
                return $thing->getUserId()->equals($userId);
            }
        );
    }

    /**
     * @param ThingId $thingId
     * @return Thing|null
     */
    public function findById(ThingId $thingId)
    {
        if (isset($this->things[$thingId->getId()])) {
            return $this->things[$thingId->getId()];
        }

        return null;
    }

    /**
     * @param Thing $thing
     * @return bool
     */
    public function update(Thing $thing): bool
    {
        $thingId = $thing->getThingId()->getId();

        if (isset($this->things[$thingId])) {
            $this->things[$thingId] = $thing;
            return true;
        }

        return false;
    }

    /**
     * @param Thing $thing
     * @return bool
     */
    public function save(Thing $thing): bool
    {
        $this->things[$thing->getThingId()->getId()] = $thing;
        return true;
    }

    /**
     * @param Thing $thing
     * @return bool
     */
    public function delete(Thing $thing): bool
    {
        $thingId = $thing->getThingId()->getId();

        if (isset($this->things[$thingId])) {
            unset($this->things[$thingId]);
            return true;
        }

        return false;
    }

    /**
     * @return ThingId
     */
    public function nextId(): ThingId
    {
        return new ThingId;
    }
}
