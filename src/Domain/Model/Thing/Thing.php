<?php

namespace Things\Domain\Model\Thing;

use Things\Domain\Model\User\UserId;

/**
 * Class Thing
 * @package Things\Domain\Model\Thing
 */
class Thing
{
    /**
     * @var ThingId
     */
    protected $thingId;

    /**
     * @var UserId
     */
    protected $userId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * Thing constructor.
     * @param ThingId $thingId
     * @param UserId $userId
     * @param string $name
     * @param string $description
     */
    public function __construct(
        ThingId $thingId,
        UserId $userId,
        string $name,
        string $description
    ) {
        $this->thingId = $thingId;
        $this->userId = $userId;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return ThingId
     */
    public function getThingId(): ThingId
    {
        return $this->thingId;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = trim($name);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = trim($description);
    }
}
