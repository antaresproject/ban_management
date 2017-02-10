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

use Carbon\Carbon;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Contracts\Console\Kernel;
use Antares\BanManagement\Config;
use Closure;

class ThrottleRequestsMiddleware
{

    /**
     * The rate limiter instance.
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * Application kernel.
     *
     * @var Kernel
     */
    protected $kernel;

    /**
     * Config instance.
     *
     * @var Config
     */
    protected $config;

    /**
     * ThrottleRequestsMiddleware constructor.
     * @param RateLimiter $limiter
     * @param Kernel $kernel
     * @param Config $config
     */
    public function __construct(RateLimiter $limiter, Kernel $kernel, Config $config)
    {
        $this->limiter = $limiter;
        $this->kernel  = $kernel;
        $this->config  = $config;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {


        $key          = $request->fingerprint();
        $maxAttempts  = $this->config->getMaxFailedAttempts();
        $decayMinutes = $this->config->getAttemptsDecay();

        if (auth()->guest() && $this->limiter->tooManyAttempts($key, $maxAttempts, $decayMinutes)) {
            $expirationDate = Carbon::today()->addMinutes($decayMinutes);
            $this->ban($request, $expirationDate);
        } else {
            $this->limiter->hit($key, $decayMinutes);
        }

        return $next($request);
    }

    /**
     * Ban the given request.
     *
     * @param Request $request
     * @param Carbon $expirationDate
     */
    protected function ban(Request $request, Carbon $expirationDate)
    {
        $params = [
            'ip'           => $request->getClientIp(),
            '--reason'     => 'Too many attempts.',
            '--canBanSelf' => true,
            '--expire'     => $expirationDate->toDateString(),
        ];

        $this->kernel->call('ban-management:add-rule', $params);
    }

}
