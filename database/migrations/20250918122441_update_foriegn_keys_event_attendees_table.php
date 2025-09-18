<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdateForiegnKeysEventAttendeesTable extends AbstractMigration
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
        $table = $this->table('events_attendees');

        if ($this->table('events_attendees')->hasForeignKey('attendee_id')) {
            $table->dropForeignKey('attendee_id');
        }
        if ($this->table('events_attendees')->hasForeignKey('event_id')) {
            $table->dropForeignKey('event_id');
        }
        $table->update();


        $table->addForeignKey('attendeeId', 'attendees', 'id', [
            'delete' => 'CASCADE',
            'update' => 'CASCADE'
        ])
            ->addForeignKey('eventId', 'events', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])->update();
    }
}
