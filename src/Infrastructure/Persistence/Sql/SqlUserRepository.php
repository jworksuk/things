<?php

namespace Things\Infrastructure\Persistence\Sql;

use Things\Domain\Model\User\User;
use Things\Domain\Model\User\UserId;
use Things\Domain\Model\User\UserRepository;

/**
 * Class SqlUserRepository
 * @package Things\Infrastructure\Persistence\Sql
 */
class SqlUserRepository extends SqlRepository implements UserRepository
{
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @param UserId $userId
     * @return User|null
     * @throws \Assert\AssertionFailedException
     */
    public function findByUserId(UserId $userId)
    {
        $st = $this->execute('SELECT * FROM users WHERE id = :id', [
            'id' => $userId->getId()
        ]);

        if ($row = $st->fetch()) {
            return $this->buildUser($row);
        }

        return null;
    }

    /**
     * @param string $email
     * @return User|null
     * @throws \Assert\AssertionFailedException
     */
    public function findByEmail(string $email)
    {
        $st = $this->execute('SELECT * FROM users WHERE email = :email', [
            'email' => $email,
        ]);

        if ($row = $st->fetch()) {
            return $this->buildUser($row);
        }

        return null;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function save(User $user): bool
    {
        $sql = 'INSERT INTO users
            (id, email, password, activated_at, updated_at, created_at)
            VALUES
            (:id, :email, :password, :activated_at, :updated_at, :created_at)';
        $this->execute($sql, [
            'id' => $user->getUserId()->getId(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'activated_at' => null,
            'updated_at' => $user->getUpdatedAt()->format(self::DATE_FORMAT),
            'created_at' => $user->getCreatedAt()->format(self::DATE_FORMAT),
        ]);

        return true;
    }

    /**
     * @return UserId
     */
    public function nextId(): UserId
    {
        return new UserId;
    }

    /**
     * @param array $row
     * @return User
     * @throws \Assert\AssertionFailedException
     */
    private function buildUser(array $row)
    {
        return new User(
            new UserId($row['id']),
            $row['email'],
            '',
            $row['password'],
            new \DateTime($row['created_at']),
            new \DateTime($row['updated_at'])
        );
    }
}
