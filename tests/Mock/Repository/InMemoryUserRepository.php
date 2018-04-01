<?php

namespace Things\Mock\Repository;

use DateTime;
use Things\Domain\Model\User\User;
use Things\Domain\Model\User\UserId;
use Things\Domain\Model\User\UserRepository;

/**
 * Class InMemoryUserRepository
 * @package Things\Mock\Repository
 */
class InMemoryUserRepository implements UserRepository
{

    /**
     * @var User[]
     */
    protected $users = [];

    public $oldUserId;

    public function __construct()
    {
        $this->users = [];
        $user = new User(
            new UserId,
            'found@found.com',
            '',
            '12345678',
            new DateTime,
            new DateTime
        );
        $this->users[$user->getUserId()->getId()] = $user;
        $this->oldUserId = $user->getUserId();
    }

    /**
     * @param UserId $userId
     * @return User|null
     */
    public function findByUserId(UserId $userId)
    {
        $users = array_filter(
            $this->users,
            function (User $user) use ($userId) {
                return $user->getUserId() == $userId->getId();
            }
        );

        if (reset($users)) {
            return reset($users);
        }

        return null;
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email)
    {
        $email = trim($email);
        $users = array_filter(
            $this->users,
            function (User $user) use ($email) {
                return $user->getEmail() == $email;
            }
        );

        if (reset($users)) {
            return reset($users);
        }

        return null;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function save(User $user): bool
    {
        $this->users[strval($user->getUserId())] = $user;
        return true;
    }

    /**
     * @return UserId
     */
    public function nextId(): UserId
    {
        return new UserId;
    }
}
