<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPromoterIdToEvent extends AbstractMigration
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
        $table = $this->table('events');
        $table->addColumn('promoter_id', 'integer', ['signed' => false])
            ->addForeignKey('promoter_id', 'promoters', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->update();
    }
}
