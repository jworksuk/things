<?php

namespace Things\Domain\Model\Thing;

use Things\Domain\Model\User\UserId;

/**
 * Interface ThingRepository
 * @package Things\Domain\Model\Thing
 */
interface ThingRepository
{
    /**
     * @param UserId $userId
     * @return Thing[]
     */
    public function findAllByUserId(UserId $userId);

    /**
     * @param ThingId $thingId
     * @return Thing|null
     */
    public function findById(ThingId $thingId);

    /**
     * @param Thing $thing
     * @return bool
     */
    public function update(Thing $thing): bool;

    /**
     * @param Thing $thing
     * @return bool
     */
    public function save(Thing $thing): bool;

    /**
     * @param Thing $thing
     * @return bool
     */
    public function delete(Thing $thing): bool;

    /**
     * @return ThingId
     */
    public function nextId(): ThingId;
}
