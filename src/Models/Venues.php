<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venues extends Model
{
    protected $table = 'venues';

    const string CREATED_AT = 'createdAt';
    const string UPDATED_AT = 'updatedAt';

    protected $guarded = ['id', 'createdAt', 'updatedAt'];

    #[\Override]
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
