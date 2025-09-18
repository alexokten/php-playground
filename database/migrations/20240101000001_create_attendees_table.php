<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAttendeesTable extends AbstractMigration
{
    public function change()
    {
        $exists = $this->hasTable('attendees');
        if ($exists) {
            $this->table('attendees')->drop()->save();
        }

        $table = $this->table('attendees');
        $table
            ->addColumn('firstName', 'string')
            ->addColumn('lastName', 'string')
            ->addColumn('dateOfBirth', 'date')
            ->addColumn('city', 'string')
            ->addColumn('isActive', 'boolean', ['default' => true])
            ->addTimestamps('createdAt', 'updatedAt')
            ->create();
    }
}
