<?php

namespace DoeAnderson\RemoteDatabaseSync;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use DoeAnderson\RemoteDatabaseSync\Commands\RemoteDatabaseSyncCommand;

class RemoteDatabaseSyncServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('remote-database-sync')
            ->hasConfigFile()
            ->hasCommand(RemoteDatabaseSyncCommand::class);
    }
}
