<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'firstName',
        'lastName',
        'dateOfBirth',
        'city',
        'isActive',
    ];

    protected $casts = [
        'firstName' => 'string',
        'lastName' => 'string',
        'dateOfBirth' => 'datetime',
        'city' => 'string',
        'isActive' => 'bool',
    ];
}
