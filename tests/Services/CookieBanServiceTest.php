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

namespace Antares\BanManagement\Services;

use Mockery as m;
use Antares\Testbench\TestCase;

class CookieBanServiceTest extends TestCase
{

    /**
     * @var Mockery
     */
    protected $request;

    /**
     * @var Mockery
     */
    protected $cookieJar;

    /**
     * @var Mockery
     */
    protected $cookie;

    public function setUp()
    {
        parent::setUp();

        $this->request   = m::mock('\Illuminate\Http\Request');
        $this->cookieJar = m::mock('\Illuminate\Cookie\CookieJar');
        $this->cookie    = m::mock('\Symfony\Component\HttpFoundation\Cookie');
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @return \Antares\BanManagement\Services\CookieBanService
     */
    protected function getCookieBanService()
    {
        return new CookieBanService($this->cookieJar, $this->request);
    }

    public function testAddMethod()
    {
        $ip = '192.168.56.101';

        $this->request
                ->shouldReceive('getClientIp')
                ->once()
                ->andReturn($ip);

        $this->cookieJar
                ->shouldReceive('forever')
                ->once()
                ->with('ban', $ip)
                ->andReturn($this->cookie);

        $this->getCookieBanService()->add();
    }

    public function testRemoveMethod()
    {
        $this->cookieJar
                ->shouldReceive('forget')
                ->once()
                ->with('ban')
                ->andReturn($this->cookie);

        $this->getCookieBanService()->remove();
    }

    public function testHasMethodWithCookie()
    {
        $this->request
                ->shouldReceive('cookie')
                ->once()
                ->with('ban')
                ->andReturn(m::type('String'));

        $this->assertTrue($this->getCookieBanService()->has());
    }

    public function testHasMethodWithoutCookie()
    {
        $this->request
                ->shouldReceive('cookie')
                ->once()
                ->with('ban')
                ->andReturnNull();

        $this->assertFalse($this->getCookieBanService()->has());
    }

    public function testHasDifferentIpWithoutCookie()
    {
        $this->request
                ->shouldReceive('cookie')
                ->once()
                ->with('ban')
                ->andReturnNull();

        $this->assertFalse($this->getCookieBanService()->hasDifferentIp());
    }

    public function testHasDifferentIpWithValidCookieIp()
    {
        $this->request
                ->shouldReceive('cookie')
                ->twice()
                ->with('ban')
                ->andReturn('192.168.56.101')
                ->shouldReceive('getClientIp')
                ->once()
                ->andReturn('192.168.1.101');

        $this->assertTrue($this->getCookieBanService()->hasDifferentIp());
    }

    public function testHasDifferentIpWithInvalidCookieIp()
    {
        $this->request
                ->shouldReceive('cookie')
                ->twice()
                ->with('ban')
                ->andReturn('192.168.56.101')
                ->shouldReceive('getClientIp')
                ->once()
                ->andReturn('192.168.56.101');

        $this->assertFalse($this->getCookieBanService()->hasDifferentIp());
    }

}
