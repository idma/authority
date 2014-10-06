<?php

namespace Idma\Authority;

use Illuminate\Support\Facades\Facade;

class AuthorityFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'idma.authority';
    }
}
