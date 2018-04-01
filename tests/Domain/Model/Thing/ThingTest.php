<?php

namespace Things\Domain\Model\Thing;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * Class ThingTest
 * @package Things\Domain\Model\Thing
 */
class ThingTest extends TestCase
{
    public function testThingId()
    {
        $primitiveId = 'TEST';
        $thingId = new ThingId($primitiveId);
        Assert::assertEquals($primitiveId, $thingId->getId());
        Assert::assertTrue($thingId->equals($thingId));
        Assert::assertFalse($thingId->equals(new ThingId));
        Assert::assertFalse((new ThingId)->equals(new ThingId));
        Assert::assertEquals($primitiveId, strval($thingId->__toString()));
        Assert::assertTrue(is_string($thingId->__toString()));
    }
}
