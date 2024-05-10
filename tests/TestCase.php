<?php

namespace DoeAnderson\RemoteDatabaseSync\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use DoeAnderson\RemoteDatabaseSync\RemoteDatabaseSyncServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'DoeAnderson\\RemoteDatabaseSync\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            RemoteDatabaseSyncServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_remote-database-sync_table.php.stub';
        $migration->up();
        */
    }
}
