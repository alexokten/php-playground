<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddEmailQueue extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('queue');
        $table
            ->addColumn('queue', 'string', [
                'default' => 'default',
                'limit' => 100,
                'null' => false
            ])
            ->addColumn(
                'payload',
                'text',
                ['null' => false]
            )
            ->addColumn(
                'attempts',
                'integer',
                ['default' => 0, 'signed' => false, 'null' => false]
            )
            ->addColumn(
                'reservedAt',
                'timestamp',
                ['null' => true, 'default' => null]
            )
            ->addColumn(
                'availableAt',
                'timestamp',
                ['null' => false]
            )
            ->addColumn(
                'completedAt',
                'timestamp',
                ['null' => true, 'default' => null]
            )
            ->addIndex(['queue', 'reservedAt', 'availableAt'], [
                'name' => 'idx_queue_processing'
            ])
            ->addTimestamps('createdAt', 'updatedAt')
            ->create();
    }
}
