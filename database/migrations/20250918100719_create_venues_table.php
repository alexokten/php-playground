<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateVenuesTable extends AbstractMigration
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
        $exists = $this->hasTable('venues');
        if ($exists) {
            $this->table('venues')->drop()->save();
        }

        $table = $this->table('venues');
        $table
            ->addColumn('name', 'string')
            ->addColumn('postcode', 'text')
            ->addColumn('maxCapacity', 'integer')
            ->addColumn('location', 'string')
            ->addColumn('isActive', 'boolean', ['default' => true])
            ->addTimestamps('createdAt', 'updatedAt')
            ->create();
    }
}
