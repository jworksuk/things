<?php

namespace Things\Infrastructure\Persistence\Sql;

use Things\Domain\Model\Thing\Thing;
use Things\Domain\Model\Thing\ThingFactory;
use Things\Domain\Model\Thing\ThingId;
use Things\Domain\Model\Thing\ThingRepository;
use Things\Domain\Model\User\UserId;

/**
 * Class SqlThingRepository
 * @package Things\Infrastructure\Persistence\Sql
 */
class SqlThingRepository extends SqlRepository implements ThingRepository, ThingFactory
{
    /**
     * @param UserId $userId
     * @return Thing[]
     */
    public function findAllByUserId(UserId $userId)
    {
        $st = $this->execute('SELECT * FROM things WHERE userId = :userId', [
            'userId' => $userId->getId(),
        ]);

        return array_map(
            function (array $row) {
                return $this->buildThing(
                    new ThingID($row['id']),
                    new UserId($row['userId']),
                    $row['name'],
                    $row['description']
                );
            },
            $st->fetchAll()
        );
    }

    /**
     * @param ThingId $thingId
     * @return Thing|null
     */
    public function findById(ThingId $thingId)
    {
        $st = $this->execute('SELECT * FROM things WHERE id = :id', [
            'id' => $thingId->getId(),
        ]);

        if ($row = $st->fetch()) {
            return $this->buildThing(
                new ThingID($row['id']),
                new UserId($row['userId']),
                $row['name'],
                $row['description']
            );
        }

        return null;
    }

    /**
     * @param Thing $thing
     * @return bool
     */
    public function update(Thing $thing): bool
    {
        $sql = 'UPDATE things
            SET name = :name, description = :description
            WHERE id = :id';
        $this->execute($sql, [
            'id' => $thing->getThingId()->getId(),
            'name' => $thing->getName(),
            'description' => $thing->getDescription(),
        ]);

        return true;
    }

    /**
     * @param Thing $thing
     * @return bool
     */
    public function save(Thing $thing): bool
    {
        $sql = 'INSERT INTO things
            (id, userId, name, description)
            VALUES
            (:id, :userId, :name, :description)';
        $this->execute($sql, [
            'id' => $thing->getThingId()->getId(),
            'userId' => $thing->getUserId()->getId(),
            'name' => $thing->getName(),
            'description' => $thing->getDescription(),
        ]);

        return true;
    }

    /**
     * @param Thing $thing
     * @return bool
     */
    public function delete(Thing $thing): bool
    {
        $this->execute('DELETE FROM things WHERE id = :id', [
            'id' => $thing->getThingId()->getId(),
        ]);

        return true;
    }

    /**
     * @return ThingId
     */
    public function nextId(): ThingId
    {
        return new ThingId;
    }

    /**
     * @param ThingId $thingId
     * @param UserId $userId
     * @param string $name
     * @param string $description
     * @return Thing
     */
    public static function buildThing(ThingId $thingId, UserId $userId, string $name, string $description): Thing
    {
        return new Thing($thingId, $userId, $name, $description);
    }
}
