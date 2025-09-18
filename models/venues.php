<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

class Venues extends Model
{
    protected $table = 'venues';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'name',
        'postCode',
        'capacity',
        'isActive',
    ];

    protected function casts()
    {
        return [
            'name' => 'string',
            'postCode' => 'string',
            'capacity' => 'number',
            'isActive' => 'bool',
        ];
    }
}
