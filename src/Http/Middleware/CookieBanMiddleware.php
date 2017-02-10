<?php

/**
 * Part of the Antares Project package.
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
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\BanManagement\Http\Middleware;

use Illuminate\Contracts\Console\Kernel;
use Antares\BanManagement\Services\CookieBanService;
use Illuminate\Http\Request;
use Closure;

class CookieBanMiddleware
{

    /**
     * Application kernel.
     *
     * @var Kernel
     */
    protected $kernel;

    /**
     * Cookie Ban Service.
     *
     * @var CookieBanService
     */
    protected $cookieBanService;

    /**
     * CookieBanMiddleware constructor.
     * @param Kernel $kernel
     * @param CookieBanService $cookieBanService
     */
    public function __construct(Kernel $kernel, CookieBanService $cookieBanService)
    {
        $this->kernel           = $kernel;
        $this->cookieBanService = $cookieBanService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->cookieBanService->hasDifferentIp()) {
            $ip = $request->getClientIp();

            $this->kernel->call('ban-management:add-rule', compact('ip'));
        }

        return $next($request);
    }

}
