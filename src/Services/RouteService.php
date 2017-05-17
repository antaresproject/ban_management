<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Ban Management
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Modules\BanManagement\Services;

use Illuminate\Http\Request;

class RouteService
{

    /**
     * Array of route names which should be prevented from banned.
     *
     * @var array
     */
    protected $preventRouteNames;

    /**
     * RouteService constructor.
     * @param array $preventRouteNames
     */
    public function __construct(array $preventRouteNames)
    {
        $this->preventRouteNames = $preventRouteNames;
    }

    /**
     * Check if the given request can be skipped.
     *
     * @param Request $request
     * @return bool
     */
    public function canSkip(Request $request)
    {
        $routeName = $request->route()->getName();

        return in_array($routeName, $this->preventRouteNames, true);
    }

}
