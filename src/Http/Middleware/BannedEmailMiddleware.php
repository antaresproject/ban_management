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

use Antares\Modules\BanManagement\Http\Controllers\BannedController;
use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use Antares\Modules\BanManagement\Services\BannedEmailService;
use Antares\Modules\BanManagement\Contracts\BanReasonContract;
use Antares\Modules\BanManagement\Services\RouteService;
use Antares\Contracts\Auth\Guard;
use Closure;

class BannedEmailMiddleware
{

    /**
     * Application container.
     *
     * @var Container
     */
    protected $container;

    /**
     * Service for banned emails.
     *
     * @var BannedEmailService
     */
    protected $bannedEmailService;

    /**
     * Route service.
     *
     * @var RouteService
     */
    protected $routeService;

    /**
     * Guard instance.
     *
     * @var Guard
     */
    protected $guard;

    /**
     * BannedEmailMiddleware constructor.
     * @param Container $container
     * @param BannedEmailService $bannedEmailService
     * @param RouteService $routeService
     * @param Guard $guard
     */
    public function __construct(Container $container, BannedEmailService $bannedEmailService, RouteService $routeService, Guard $guard)
    {
        $this->container          = $container;
        $this->bannedEmailService = $bannedEmailService;
        $this->routeService       = $routeService;
        $this->guard              = $guard;
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
        if ($this->routeService->canSkip($request) OR $this->guard->guest()) {
            return $next($request);
        }

        $email = $this->guard->user()->email;

        if ($this->bannedEmailService->isEmailBanned($email)) {
            $model = $this->bannedEmailService->getModelForEmail($email);

            if ($model instanceof BanReasonContract) {
                $this->guard->logout();
                return $this->container->make(BannedController::class)->index($model->getReason());
            }
        }

        return $next($request);
    }

}
