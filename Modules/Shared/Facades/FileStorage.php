<?php

namespace Modules\Shared\Facades;

use Illuminate\Support\Facades\Facade;

class FileStorage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'file-storage';
    }
}
