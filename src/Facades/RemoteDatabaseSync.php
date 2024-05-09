<?php

namespace DoeAnderson\RemoteDatabaseSync\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DoeAnderson\RemoteDatabaseSync\RemoteDatabaseSync
 */
class RemoteDatabaseSync extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \DoeAnderson\RemoteDatabaseSync\RemoteDatabaseSync::class;
    }
}
