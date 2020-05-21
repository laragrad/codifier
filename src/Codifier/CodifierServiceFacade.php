<?php
namespace Laragrad\Codifier;

use Illuminate\Support\Facades\Facade;
use Laragrad\Codifier\CodifierService;

class CodifierServiceFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return CodifierService::class;
    }
}