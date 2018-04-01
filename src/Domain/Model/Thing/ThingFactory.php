<?php

namespace Things\Domain\Model\Thing;

use Things\Domain\Model\User\UserId;

/**
 * Interface ThingFactory
 * @package Things\Domain\Model\Thing
 */
interface ThingFactory
{
    /**
     * @param ThingId $thingId
     * @param UserId $userId
     * @param string $name
     * @param string $description
     * @return Thing
     */
    public static function buildThing(ThingId $thingId, UserId $userId, string $name, string $description): Thing;
}
