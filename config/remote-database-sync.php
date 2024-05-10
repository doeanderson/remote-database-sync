<?php

return [
    'backup_storage_path' => env('DATABASE_BACKUP_STORAGE_PATH', 'remote-database-sync/local-backups'),
    'mysql_path' => env('MYSQL_PATH', 'mysql'),
    'mysqldump_path' => env('MYSQLDUMP_PATH', 'mysqldump'),
    'mysqldump_options' => env('MYSQLDUMP_OPTIONS', '--no-tablespaces'),
    'environments' => [
        'staging' => [
            'db_connection_name' => 'mysql_remote_staging',
            'tunneler' => [
                'local_address' => env('TUNNELER_STAGING_LOCAL_ADDRESS', config('tunneler.local_address')),
                'local_port' => env('TUNNELER_STAGING_LOCAL_PORT', config('tunneler.local_port')),
                'ssh_options' => env('TUNNELER_STAGING_SSH_OPTIONS', config('tunneler.ssh_options')),
                'ssh_verbosity' => env('TUNNELER_STAGING_SSH_VERBOSITY', config('tunneler.ssh_verbosity')),
                'identity_file' => env('TUNNELER_STAGING_IDENTITY_FILE', config('tunneler.identity_file')),
                'bind_address' => env('TUNNELER_STAGING_BIND_ADDRESS', config('tunneler.bind_address')),
                'bind_port' => env('TUNNELER_STAGING_BIND_PORT', config('tunneler.bind_port')),
                'port' => env('TUNNELER_STAGING_PORT', config('tunneler.port')),
                'user' => env('TUNNELER_STAGING_USER', config('tunneler.user')),
                'hostname' => env('TUNNELER_STAGING_HOSTNAME', config('tunneler.hostname')),
            ],
        ],
        'production' => [
            'db_connection_name' => 'mysql_remote_production',
            'tunneler' => [
                'local_address' => env('TUNNELER_PRODUCTION_LOCAL_ADDRESS', config('tunneler.local_address')),
                'local_port' => env('TUNNELER_PRODUCTION_LOCAL_PORT', config('tunneler.local_port')),
                'ssh_options' => env('TUNNELER_PRODUCTION_SSH_OPTIONS', config('tunneler.ssh_options')),
                'ssh_verbosity' => env('TUNNELER_PRODUCTION_SSH_VERBOSITY', config('tunneler.ssh_verbosity')),
                'identity_file' => env('TUNNELER_PRODUCTION_IDENTITY_FILE', config('tunneler.identity_file')),
                'bind_address' => env('TUNNELER_PRODUCTION_BIND_ADDRESS', config('tunneler.bind_address')),
                'bind_port' => env('TUNNELER_PRODUCTION_BIND_PORT', config('tunneler.bind_port')),
                'port' => env('TUNNELER_PRODUCTION_PORT', config('tunneler.port')),
                'user' => env('TUNNELER_PRODUCTION_USER', config('tunneler.user')),
                'hostname' => env('TUNNELER_PRODUCTION_HOSTNAME', config('tunneler.hostname')),
            ],
        ],
    ],
];
