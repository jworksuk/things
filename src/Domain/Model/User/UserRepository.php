<?php

namespace Things\Domain\Model\User;

interface UserRepository
{
    /**
     * @param UserId $userId
     * @return User|null
     */
    public function findByUserId(UserId $userId);

    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email);

    /**
     * @param User $user
     * @return bool
     */
    public function save(User $user): bool;

    /**
     * @return UserId
     */
    public function nextId(): UserId;
}
