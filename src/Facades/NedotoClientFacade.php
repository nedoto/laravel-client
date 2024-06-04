<?php

declare(strict_types=1);

namespace Nedoto\Facades;

use Illuminate\Support\Facades\Facade;
use Nedoto\Client\NedotoClient;

class NedotoClientFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NedotoClient::class;
    }
}
