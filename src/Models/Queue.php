<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\QueueStatus;
use Carbon\Carbon;

class Queue extends Model
{
    protected $table = 'queue';

    const string CREATED_AT = 'createdAt';
    const string UPDATED_AT = 'updatedAt';

    protected $guarded = ['id', 'createdAt', 'updatedAt'];

    #[\Override]
    protected function casts()
    {
        return [
            'queue' => 'string',
            'payload' => 'string',
            'status' => QueueStatus::class,
            'attempts' => 'integer',
            'reservedAt' => 'datetime',
            'availableAt' => 'datetime',
            'completedAt' => 'datetime',
        ];
    }
}
