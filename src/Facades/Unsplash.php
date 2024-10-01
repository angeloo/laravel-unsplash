<?php

namespace Xchimx\UnsplashApi\Facades;

use Illuminate\Support\Facades\Facade;

class Unsplash extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'unsplash';
    }
}
