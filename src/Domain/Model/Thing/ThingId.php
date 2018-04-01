<?php

namespace Things\Domain\Model\Thing;

use Ramsey\Uuid\Uuid;

/**
 * Class ThingId
 * @package Things\Domain\Model\Thing
 */
class ThingId
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @param string $id
     */
    public function __construct(string $id = null)
    {
        $this->id = null === $id ? Uuid::uuid4()->toString() : $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param ThingId $thingId
     *
     * @return bool
     */
    public function equals(ThingId $thingId)
    {
        return $this->getId() === $thingId->getId();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getId();
    }
}
