<?php

namespace Asanak\WebCall\Facade;

use Illuminate\Support\Facades\Facade;

class AsanakWebCallFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Asanak\WebCall\WebCallClient::class;
    }
}
