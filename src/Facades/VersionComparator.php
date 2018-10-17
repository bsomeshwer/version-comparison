<?php namespace Someshwer\VersionComparison\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * VersionComparator facade
 *
 * @author Someshwer <bsomeshwer89@gmail.com>
 * Date: 26-09-2018
 */
class VersionComparator extends Facade
{

    /**
     * Get facade accessor
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'VersionComparator';
    }

}
