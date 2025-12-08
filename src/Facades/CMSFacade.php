<?php

namespace Amplify\System\Cms\Facades;

use Illuminate\Support\Facades\Facade;

class CMSFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'CMS';
    }
}
