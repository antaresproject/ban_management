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

use Mockery as m;
use Antares\Testbench\TestCase;
use Illuminate\Http\Response;
use Antares\BanManagement\Http\Controllers\BannedController;

class FirewallMiddlewareTest extends TestCase
{

    /**
     * @var Mockery
     */
    protected $dispatcher;

    /**
     * @var Mockery
     */
    protected $redirector;

    /**
     * @var Mockery
     */
    protected $firewall;

    /**
     * @var Mockery
     */
    protected $route;

    /**
     * @var Mockery
     */
    protected $request;

    /**
     * @var Mockery
     */
    protected $routeService;

    public function setUp()
    {
        parent::setUp();

        $this->dispatcher   = m::mock('\Illuminate\Events\Dispatcher');
        $this->firewall     = m::mock('\Antares\BanManagement\Services\FirewallService');
        $this->container    = m::mock('\Illuminate\Contracts\Container\Container');
        $this->route        = m::mock('\Illuminate\Routing\Route');
        $this->routeService = m::mock('\Antares\BanManagement\Services\RouteService');
        $this->request      = m::mock('\Illuminate\Http\Request');
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @return \Antares\BanManagement\Http\Middleware\FirewallMiddleware
     */
    protected function getFirewallMiddleware()
    {
        return new FirewallMiddleware($this->dispatcher, $this->container, $this->firewall, $this->routeService);
    }

    public function testHandleWItchSkippedAction()
    {
        $this->routeService
                ->shouldReceive('canSkip')
                ->once()
                ->with($this->request)
                ->andReturn(true)
                ->getMock();

        $next = function() {
            return 'next-dump-request';
        };

        $result = $this->getFirewallMiddleware()->handle($this->request, $next);

        $this->assertEquals($next(), $result);
    }

    public function testHandleWithoutBanAction()
    {
        $this->routeService
                ->shouldReceive('canSkip')
                ->once()
                ->with($this->request)
                ->andReturn(false)
                ->getMock();

        $this->firewall
                ->shouldReceive('isRequestAllowed')
                ->with($this->request)
                ->once()
                ->andReturn(true)
                ->getMock();

        $next = function() {
            return 'next-dump-request';
        };

        $result = $this->getFirewallMiddleware()->handle($this->request, $next);

        $this->assertEquals($next(), $result);
    }

    public function testHandleWitBanAction()
    {
        $rule = m::mock(\Antares\BanManagement\Contracts\RuleContract::class)
                ->shouldReceive('getReason')
                ->once()
                ->andReturn('reason')
                ->getMock();

        $this->routeService
                ->shouldReceive('canSkip')
                ->once()
                ->with($this->request)
                ->andReturn(false)
                ->getMock();

        $this->firewall
                ->shouldReceive('isRequestAllowed')
                ->with($this->request)
                ->once()
                ->andReturn(false)
                ->shouldReceive('getMatchingRule')
                ->once()
                ->andReturn($rule)
                ->getMock();

        $this->dispatcher
                ->shouldReceive('fire')
                ->with(m::type('\Antares\BanManagement\Events\Banned'))
                ->once()
                ->andReturnNull()
                ->getMock();

        $bannedController = m::mock(BannedController::class)
                ->shouldReceive('index')
                ->once()
                ->with(m::type('String'))
                ->andReturn(m::mock(Response::class))
                ->getMock();

        $this->container
                ->shouldReceive('make')
                ->with(BannedController::class)
                ->andReturn($bannedController)
                ->getMock();

        $next = function() {
            return 'next-dump-request';
        };

        $result = $this->getFirewallMiddleware()->handle($this->request, $next);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function testHandleWitBanActionOfModelRule()
    {
        $rule = m::mock('\Antares\BanManagement\Model\Rule')
                ->shouldReceive('getReason')
                ->once()
                ->andReturn('test reason')
                ->getMock();

        $this->routeService
                ->shouldReceive('canSkip')
                ->once()
                ->with($this->request)
                ->andReturn(false)
                ->getMock();

        $this->firewall
                ->shouldReceive('isRequestAllowed')
                ->with($this->request)
                ->once()
                ->andReturn(false)
                ->shouldReceive('getMatchingRule')
                ->once()
                ->andReturn($rule)
                ->getMock();

        $this->dispatcher
                ->shouldReceive('fire')
                ->with(m::type('\Antares\BanManagement\Events\Banned'))
                ->once()
                ->andReturnNull()
                ->getMock();

        $bannedController = m::mock(BannedController::class)
                ->shouldReceive('index')
                ->once()
                ->with(m::type('String'))
                ->andReturn(m::mock(Response::class))
                ->getMock();

        $this->container
                ->shouldReceive('make')
                ->with(BannedController::class)
                ->andReturn($bannedController)
                ->getMock();

        $next = function() {
            return 'next-dump-request';
        };

        $result = $this->getFirewallMiddleware()->handle($this->request, $next);

        $this->assertInstanceOf(Response::class, $result);
    }

}
