<?php

namespace DoeAnderson\RemoteDatabaseSync\Commands;

use Illuminate\Console\Command;

class RemoteDatabaseSyncCommand extends Command
{
    public $signature = 'remote-database-sync';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
