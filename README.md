# Remote Database Sync

This package allows you to sync a remote database to your local environment.

## Installation

You can install the package via composer:

Add the following to your `composer.json` file:
```json
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/doeanderson/remote-database-sync"
  }
]
```

```bash
composer require --dev doeanderson/remote-database-sync
```
## Config
You can publish the config file with:

```bash
php artisan vendor:publish --tag="remote-database-sync-config"
```

:exclamation: This package uses the [prodigyphp/laravel-ssh-tunnel](https://github.com/prodigyphp/laravel-ssh-tunnel) package to create an SSH tunnel to the remote server. 
You will need to configure the tunneler package before using this package.
Afterward you can configure per-environment tunnels in your `config/remote-database-sync.php` file. The published config file has an example of how to do this.

## Usage
```bash
php artisan db:download-remote
```

You will be prompted to save an optiona local database backup, select which remote environment to download from and which individual database tables to download.
