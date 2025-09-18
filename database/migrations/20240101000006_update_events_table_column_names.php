<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdateEventsTableColumnNames extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('events');
        if ($table->hasColumn('promoter_id')) {
            $table->renameColumn('promoter_id', 'promoterId')
                ->update();
        }
    }
}
