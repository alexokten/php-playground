<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $table = 'attendees';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = ['id', 'createdAt', 'updatedAt'];

    protected function casts()
    {
        return [
            'queue' => 'string',
            'payload' => 'string',
            'attempts' => 'integer',
            'reservedAt' => 'datetime',
            'availableAt' => 'datetime',
            'completedAt' => 'datetime',
        ];
    }
}
