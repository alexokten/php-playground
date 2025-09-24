<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venues extends Model
{
    protected $table = 'venues';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = ['id', 'createdAt', 'updatedAt'];

    protected function casts()
    {
        return [
            'name' => 'string',
            'location' => 'string',
            'maxCapacity' => 'number',
            'isActive' => 'bool',
        ];
    }
}
