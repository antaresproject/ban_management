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

namespace Antares\Modules\BanManagement\Http\Middleware;

use Illuminate\Events\Dispatcher;
use Antares\Modules\BanManagement\Http\Controllers\BannedController;
use Antares\Modules\BanManagement\Services\FirewallService;
use Antares\Modules\BanManagement\Events\Banned as BannedEvent;
use Antares\Modules\BanManagement\Contracts\BanReasonContract;
use Antares\Modules\BanManagement\Services\RouteService;
use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use Closure;

class FirewallMiddleware
{

    /**
     * Event dispatcher.
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Application container.
     *
     * @var Container
     */
    protected $container;

    /**
     * Firewall Service.
     *
     * @var FirewallService
     */
    protected $firewall;

    /**
     * Route service.
     *
     * @var RouteService
     */
    protected $routeService;

    /**
     * FirewallMiddleware constructor.
     * @param Dispatcher $dispatcher
     * @param Container $container
     * @param FirewallService $firewall
     * @param RouteService $routeService
     */
    public function __construct(Dispatcher $dispatcher, Container $container, FirewallService $firewall, RouteService $routeService)
    {
        $this->dispatcher   = $dispatcher;
        $this->container    = $container;
        $this->firewall     = $firewall;
        $this->routeService = $routeService;
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

        if ($this->routeService->canSkip($request) OR $this->firewall->isRequestAllowed($request)) {
            return $next($request);
        }

        $rule = $this->firewall->getMatchingRule();
        $this->dispatcher->fire(new BannedEvent($rule, $request));

        if ($rule instanceof BanReasonContract) {
            return $this->container->make(BannedController::class)->index($rule->getReason());
        }

        return $next($request);
    }

}
