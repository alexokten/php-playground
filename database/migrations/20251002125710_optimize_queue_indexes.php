<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class OptimizeQueueIndexes extends AbstractMigration
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

        // Remove old index that doesn't include status
        $table->removeIndex(['queue', 'reservedAt', 'availableAt'], ['name' => 'idx_queue_processing'])
              ->update();

        // Add optimized index for pop() query: queue + status + availableAt
        $table->addIndex(['queue', 'status', 'availableAt'], [
            'name' => 'idx_queue_pop'
        ])->update();

        // Add index on status for count/filter queries
        $table->addIndex(['status'], [
            'name' => 'idx_status'
        ])->update();
    }
}
