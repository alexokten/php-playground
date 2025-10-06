<?php

declare(strict_types=1);

namespace App\Controllers;

use Spatie\Ray\Payloads\PhpInfoPayload;

class ConfigController
{
    public function getConfig(): bool
    {
        return phpinfo();
    }
}
