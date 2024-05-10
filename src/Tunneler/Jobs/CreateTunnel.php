<?php

namespace DoeAnderson\RemoteDatabaseSync\Tunneler\Jobs;

class CreateTunnel extends \STS\Tunneler\Jobs\CreateTunnel
{
    public function __construct(string $environment)
    {
        $config = config('remote-database-sync.environments.' . $environment);
        if ($config === null) {
            throw new \ErrorException('No config found for environment: ' . $enviroment);
        }

        $this->ncCommand = sprintf('%s -vz %s %d  > /dev/null 2>&1',
            config('tunneler.nc_path'),
            $config['tunneler']['local_address'] ?? config('tunneler.local_address'),
            $config['tunneler']['local_port'] ?? config('tunneler.local_port'),
        );

        $this->bashCommand = sprintf('timeout 1 %s -c \'cat < /dev/null > /dev/tcp/%s/%d\' > /dev/null 2>&1',
            config('tunneler.bash_path'),
            $config['tunneler']['local_address'] ?? config('tunneler.local_address'),
            $config['tunneler']['local_port'] ?? config('tunneler.local_port'),
        );

        $this->sshCommand = sprintf('%s %s %s -N -i %s -L %d:%s:%d -p %d %s@%s',
            config('tunneler.ssh_path'),
            config('tunneler.ssh_options'),
            config('tunneler.ssh_verbosity'),
            config('tunneler.identity_file'),
            $config['tunneler']['local_port'] ?? config('tunneler.local_port'),
            $config['tunneler']['bind_address'] ?? config('tunneler.bind_address'),
            $config['tunneler']['bind_port'] ?? config('tunneler.bind_port'),
            $config['tunneler']['port'] ?? config('tunneler.port'),
            $config['tunneler']['user'] ?? config('tunneler.user'),
            $config['tunneler']['hostname'] ?? config('tunneler.hostname'),
        );
    }
}
