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

use Illuminate\Cache\RateLimiter;
use Mockery as m;
use Antares\Testing\ApplicationTestCase;

class ThrottleRequestsMiddlewareTest extends ApplicationTestCase
{

    /**
     * @var Mockery
     */
    protected $kernel;

    /**
     * @var RateLimiter
     */
    protected $rateLimiter;

    public function setUp()
    {
//        $this->addProvider(\Antares\Widget\WidgetServiceProvider::class);
//        $this->addProvider(\Antares\Widgets\WidgetsServiceProvider::class);
        $this->addProvider(\Antares\BanManagement\BanManagementServiceProvider::class);
        parent::setUp();



        $this->kernel      = m::mock('\Illuminate\Contracts\Console\Kernel');
        $this->rateLimiter = $this->app->make(RateLimiter::class);
        $this->app->instance('\Illuminate\Contracts\Console\Kernel', $this->kernel);
    }

    protected function hit($times)
    {
        for ($i = 0; $i < $times; ++$i) {
            $this->call('GET', 'throttle-test-route');
        }
    }

    public function testNotBannedRequest()
    {
        $this->app->router->get('throttle-test-route', ['middleware' => [ThrottleRequestsMiddleware::class . ':10'], function () {
                return 'next request';
            }]);
        $this->hit(1);
        $this->assertResponseOk();
        $this->hit(10);
        $this->assertResponseStatus(500);
    }

    public function testBannedRequest()
    {
        $this->kernel
                ->shouldReceive('call')
                ->with('ban-management:add-rule', m::type('Array'))
                ->once()
                ->andReturnNull()
                ->getMock();

        $this->app->router->get('throttle-test-route', ['middleware' => [ThrottleRequestsMiddleware::class . ':10'], function () {
                return 'next request';
            }]);
        $this->hit(1);
        $this->assertResponseOk();
        $this->hit(11);
        $this->assertResponseStatus(500);
    }

}
