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

namespace Antares\Modules\BanManagement\Http\Middleware;

use Mockery as m;
use Antares\Testbench\TestCase;

class CookieBanMiddlewareTest extends TestCase
{

    /**
     * @var Mockery
     */
    protected $kernel;

    /**
     * @var Mockery
     */
    protected $cookieBanService;

    /**
     * @var Mockery
     */
    protected $request;

    public function setUp()
    {
        parent::setUp();

        $this->kernel           = m::mock('\Illuminate\Contracts\Console\Kernel');
        $this->cookieBanService = m::mock('\Antares\Modules\BanManagement\Services\CookieBanService');
        $this->request          = m::mock('\Illuminate\Http\Request');

        $this->app->instance('\Illuminate\Contracts\Console\Kernel', $this->kernel);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @return \Antares\Modules\BanManagement\Http\Middleware\CookieBanMiddleware
     */
    protected function getCookieBanMiddleware()
    {
        return new CookieBanMiddleware($this->kernel, $this->cookieBanService);
    }

    public function testWithoutCookie()
    {
        $this->cookieBanService
                ->shouldReceive('hasDifferentIp')
                ->once()
                ->andReturn(false);

        $next = function() {
            return 'next-dump-request';
        };

        $this->assertEquals($next(), $this->getCookieBanMiddleware()->handle($this->request, $next));
    }

    public function testWithCookie()
    {
        $ip = '192.168.56.101';

        $this->cookieBanService
                ->shouldReceive('hasDifferentIp')
                ->once()
                ->andReturn(true)
                ->getMock();

        $this->request
                ->shouldReceive('getClientIp')
                ->once()
                ->andReturn($ip)
                ->getMock();

        $this->kernel
                ->shouldReceive('call')
                ->with('ban-management:add-rule', compact('ip'))
                ->once()
                ->andReturn(m::type('Integer'))
                ->getMock();

        $next = function() {
            return 'next-dump-request';
        };

        $this->assertEquals($next(), $this->getCookieBanMiddleware()->handle($this->request, $next));
    }

}
