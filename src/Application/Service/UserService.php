<?php

namespace Things\Application\Service;

use DateTime;
use Things\Domain\Model\User\UserNotFoundException;
use Things\Domain\Model\User\UserId;
use Things\Domain\Model\User\User;
use Things\Domain\Model\User\UserAlreadyExistsException;
use Things\Domain\Model\User\UserRepository;
use Things\Domain\Service\PasswordHashing;

/**
 * Class UserService
 * @package Things\Application\Service
 */
class UserService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var PasswordHashing
     */
    protected $hasher;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param PasswordHashing $hasher
     */
    public function __construct(
        UserRepository $userRepository,
        PasswordHashing $hasher
    ) {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $name
     * @return array
     * @throws UserAlreadyExistsException
     * @throws \Assert\AssertionFailedException
     */
    public function createUser(string $email, string $password, string $name = ''): array
    {
        // checks if user already exists
        $user = $this->userRepository->findByEmail($email);
        if (null !== $user) {
            throw new UserAlreadyExistsException;
        }

        $user = $this->buildUser(
            $this->userRepository->nextId(),
            $email,
            $name,
            $this->hasher->calculateHash($password),
            new DateTime,
            new DateTime
        );

        $this->userRepository->save($user);

        return [
            'id' => $user->getUserId()->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ];
    }

    /**
     * @param UserId $userId
     * @return array
     * @throws UserNotFoundException
     */
    public function getUserById(UserId $userId)
    {
        $user = $this->userRepository->findByUserId($userId);
        if (null === $user) {
            throw new UserNotFoundException;
        }

        return [
            'id' => $user->getUserId()->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ];
    }

    /**
     * @param UserId $userId
     * @param string $email
     * @param string $name
     * @param string $password
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     * @return User
     * @throws \Assert\AssertionFailedException
     */
    public static function buildUser(
        UserId $userId,
        string $email,
        string $name,
        string $password,
        DateTime $createdAt,
        DateTime $updatedAt
    ): User {
        return new User($userId, $email, $name, $password, $createdAt, $updatedAt);
    }
}
