<?php

declare(strict_types=1);

class UserRepository
{
    public function findAll()
    {
        return User::orderBy('createdAt', 'desc')->get();
    }
}
