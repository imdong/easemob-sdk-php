<?php

namespace ImDong\Easemob\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Easemob
 *
 * @package ImDong\Easemob\Facades
 * @author  ImDong (www@qs5.org)
 * @created 2021-01-06 10:39
 */
class Easemob extends Facade
{
    /**
     *
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Easemob';
    }
}
