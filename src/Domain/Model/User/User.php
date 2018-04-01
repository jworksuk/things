<?php

namespace Things\Domain\Model\User;

use DateTime;
use Assert\Assertion;

/**
 * Class User
 * @package Things\Domain\Model\User
 */
class User
{
    /**
     * @var UserId
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * User constructor.
     * @param UserId $id
     * @param string $email
     * @param string $name
     * @param string $password
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(
        UserId $id,
        string $email,
        string $name,
        string $password,
        DateTime $createdAt,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->setEmail($email)
            ->setPassword($password);
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return static
     * @throws \Assert\AssertionFailedException
     */
    public function setEmail(string $email)
    {
        $email = trim($email);
        if (!$email) {
            throw new \InvalidArgumentException('email');
        }

        Assertion::email($email);

        $this->email = strtolower($email);

        return $this;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password)
    {
        $password = trim($password);
        if (!$password) {
            throw new \InvalidArgumentException('password');
        }

        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
