<?php

namespace DoeAnderson\RemoteDatabaseSync;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use DoeAnderson\RemoteDatabaseSync\Commands\RemoteDatabaseSyncCommand;

class RemoteDatabaseSyncServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('remote-database-sync')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_remote-database-sync_table')
            ->hasCommand(RemoteDatabaseSyncCommand::class);
    }
}
