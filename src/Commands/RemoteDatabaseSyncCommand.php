<?php

namespace DoeAnderson\RemoteDatabaseSync\Commands;

use DoeAnderson\RemoteDatabaseSync\Tunneler\Jobs\CreateTunnel;
use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use SplFileObject;
use Symfony\Component\Process\Process;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class RemoteDatabaseSyncCommand extends Command
{
    protected $signature = 'db:download-remote
     {remote-environment? : Remote environment}
     ';

    protected $description = 'Download remote database to current environment.';

    /**
     * @throws ErrorException
     */
    public function handle(): int
    {
        // Backup local database.
        if (confirm('Do you want to backup the local database before downloading the remote database?')) {
            $defaultFileName = DB::connection()->getDatabaseName() . '-' . now()->format('Y-m-d-H-i-s') . '.sql';
            $backupDatabaseFileName = text(
                label: 'Backup file name',
                default: $defaultFileName,
                hint: 'Saved in: ' . storage_path(config('remote-database-sync.backup_storage_path'))
            );

            $this->backupLocalDatabase($backupDatabaseFileName);
        }

        // Select remote environment.
        $environmentOptions = array_keys(config('remote-database-sync.environments'));
        $remoteEnvironment = $this->argument('remote-environment') ?: select(
            label: 'Select remote environment',
            options: $environmentOptions
        );

        $config = config('remote-database-sync.environments.' . $remoteEnvironment);

        if (empty($config)) {
            $this->error('No config found for environment: ' . $remoteEnvironment);
            return static::FAILURE;
        }

        // Create SSH tunnel.
        $tunnel = new CreateTunnel($remoteEnvironment);
        dispatch_sync($tunnel);

        $remoteDbConnection = DB::connection($config['db_connection_name']);

        // Dump remote database to temp file.
        $tempDumpFile = $this->downloadRemoteDatabase($remoteDbConnection, $config);

        if (!$tempDumpFile) {
            return static::FAILURE;
        }

        // Import remote database to local.
        $this->importRemoteDatabase($tempDumpFile);

        @unlink($tempDumpFile->getPathname());

        return static::SUCCESS;
    }

    protected function backupLocalDatabase($fileName): bool
    {
        Storage::createDirectory(config('remote-database-sync.backup_storage_path'));

        $localDbConnection = DB::connection();
        $process = new Process([
            config('remote-database-sync.mysqldump_path'),
            '--host=' . $localDbConnection->getConfig('hostname'),
            '--port=' . $localDbConnection->getConfig('port'),
            '--user=' . $localDbConnection->getConfig('username'),
            '--password=' . $localDbConnection->getConfig('password'),
            config('remote-database-sync.mysqldump_options'),
            $localDbConnection->getDatabaseName(),
        ]);

        $this->output->info('Backing up local database ');

        $process->start();
        foreach ($process as $type => $data) {
            if ($type !== $process::OUT) {
                continue;
            }
            Storage::append(config('remote-database-sync.backup_storage_path') . '/' . $fileName, $data);
            $this->output->write('.');
        }
        $process->stop();

        if (!$process->isSuccessful()) {
            $this->output->error('Failed to backup database.');
            $this->output->writeln($process->getErrorOutput());
            return false;
        }

        $this->output->writeln('<info>Done!</info>');
        return true;
    }

    protected function downloadRemoteDatabase(MySqlConnection $remoteDbConnection, array $config): SplFileObject|false
    {
        $showTablesResult = $remoteDbConnection->select('SHOW TABLES');

        $tableOptions = [];
        foreach ($showTablesResult as $tableRow) {
            $tableOptions[] = $tableRow->{'Tables_in_' . $remoteDbConnection->getDatabaseName()};
        }

        $this->output->warning('Tables in your local database will be dropped and replaced with the remote database tables.');

        $tablesToSync = multiselect(
            label: 'Select individual tables to sync.',
            options: $tableOptions,
            scroll: 15,
            hint: 'If no tables are selected the entire database will be synced.'
        );

        $mysqldumpProcess = new Process([
            config('remote-database-sync.mysqldump_path'),
            '--host=' . $config['tunneler']['local_address'],
            '--port=' . $config['tunneler']['local_port'],
            '--user=' . $remoteDbConnection->getConfig('username'),
            '--password=' . $remoteDbConnection->getConfig('password'),
            config('remote-database-sync.mysqldump_options'),
            $remoteDbConnection->getDatabaseName(),
            ...$tablesToSync,
        ]);

        $tempDir = (new TemporaryDirectory())
            ->name('remote-database-sync')
            ->force()
            ->create();

        $tempDumpFilePath = $tempDir->path(Str::slug(config('app.name')) . '-' . uniqid() . '.sql');
        $tempDumpFile = new SplFileObject($tempDumpFilePath, 'w');

        $this->output->info('Downloading database ');

        $mysqldumpProcess->start();
        foreach ($mysqldumpProcess as $type => $data) {
            if ($type !== $mysqldumpProcess::OUT) {
                continue;
            }
            $tempDumpFile->fwrite($data);
            $this->output->write('.');
        }

        $mysqldumpProcess->stop();

        if (!$mysqldumpProcess->isSuccessful()) {
            $this->output->error('Failed to download database.');
            $this->output->writeln($mysqldumpProcess->getErrorOutput());
            return false;
        }

        $this->output->writeln('<info>Done!</info>');

        return $tempDumpFile;
    }

    protected function importRemoteDatabase(SplFileObject $tempDumpFile): bool
    {
        $this->output->info('Importing database ');

        $localDbConnection = DB::connection();
        $mysqlImportProcess = new Process(
            command: [
                config('remote-database-sync.mysql_path'),
                '--host=' . $localDbConnection->getConfig('hostname'),
                '--port=' . $localDbConnection->getConfig('port'),
                '--user=' . $localDbConnection->getConfig('username'),
                '--password=' . $localDbConnection->getConfig('password'),
                $localDbConnection->getDatabaseName(),
            ],
            input: 'source ' . $tempDumpFile->getPathname(),
        );

        $mysqlImportProcess->start();

        foreach ($mysqlImportProcess as $type => $data) {
            if ($type !== $mysqlImportProcess::OUT) {
                continue;
            }
            $this->output->write('.');
        }

        if (!$mysqlImportProcess->isSuccessful()) {
            $this->output->error('Failed to import database.');
            $this->output->writeln($mysqlImportProcess->getErrorOutput());
            return false;
        }

        $this->output->writeln('<info>Done!</info>');

        return true;
    }

}
