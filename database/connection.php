<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Dotenv\Dotenv;

/** 1 - Load environment variables (prefer local if exists) */
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
if (file_exists(__DIR__ . '/../.env.local')) {
    $dotenv->load('.env.local');
} else {
    $dotenv->load();
}

/** 2 - Create Capsule instance */
$capsule = new Capsule;

/** 3 - Add database connection */
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'port' => $_ENV['DB_PORT'] ?? 3306,
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

/** 4 - Set the event dispatcher used by Eloquent models */
$capsule->setEventDispatcher(new Dispatcher(new Container));

/** 5 - Make this Capsule instance available globally via static methods */
$capsule->setAsGlobal();

/** 6 - Setup the Eloquent ORM */
$capsule->bootEloquent();
