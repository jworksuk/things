<?php

namespace Things\Domain\Model\User;

use Ramsey\Uuid\Uuid;

class UserId
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
     * @param UserId $userId
     *
     * @return bool
     */
    public function equals(UserId $userId)
    {
        return $this->getId() === $userId->getId();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getId();
    }
}
