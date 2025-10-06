<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private static array $state = ['migrationsRun' => false];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!self::$state['migrationsRun']) {
            self::runMigrations();
            self::$state['migrationsRun'] = true;
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        DB::connection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::connection()->rollBack();
        parent::tearDown();
    }

    protected static function runMigrations(): void
    {
        $output = shell_exec('cd ' . __DIR__ . '/.. && vendor/bin/phinx migrate -e testing 2>&1');
        if ($output && strpos($output, 'All Done') === false && strpos($output, 'already migrated') === false) {
            throw new \RuntimeException("Migration failed: {$output}");
        }
    }
}
