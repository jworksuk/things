<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitMigration extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('users', ['id' => false, ['primary_key' => 'id']]);
        $table->addColumn('id', 'uuid')
            ->addColumn('email', 'string')
            ->addColumn('name', 'string')
            ->addColumn('password', 'string')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();

        $table = $this->table('things', ['id' => false, ['primary_key' => 'id']]);
        $table->addColumn('id', 'uuid')
            ->addColumn('user_id', 'uuid')
            ->addColumn('name', 'string')
            ->addColumn('description', 'string')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
//            ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->create();
    }

    public function down(): void
    {
        $this->table('users')->drop()->save();
        $this->table('things')->drop()->save();
    }
}
