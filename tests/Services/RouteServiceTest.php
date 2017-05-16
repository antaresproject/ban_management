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

namespace Antares\Modules\BanManagement\Services;

use Mockery as m;
use Antares\Testbench\TestCase;

class RouteServiceTest extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @param string $name
     * @return Mockery
     */
    protected function getRouterMockupWithRouteName($name)
    {
        $request = m::mock('\Illuminate\Http\Request');

        $request
                ->shouldReceive('route->getName')
                ->andReturn($name)
                ->getMock();

        return $request;
    }

    public function testCanSkipRoute()
    {
        $routeNames = ['route.should.be.skipped'];
        $service    = new RouteService($routeNames);
        $request    = $this->getRouterMockupWithRouteName($routeNames[0]);

        $this->assertTrue($service->canSkip($request));
    }

    public function testCannotSkipRoute()
    {
        $routeNames = ['route.should.be.skipped'];
        $service    = new RouteService($routeNames);
        $request    = $this->getRouterMockupWithRouteName('other.route');

        $this->assertFalse($service->canSkip($request));
    }

    public function testDeclaredRoutesInConfigFile()
    {
        $routeNames = (array) include(__DIR__ . '/../../resources/config/routes.php');
        $service    = new RouteService($routeNames);

        $expectedRouteNames = [
        ];

        foreach ($expectedRouteNames as $expectedRouteName) {
            $request = $this->getRouterMockupWithRouteName($expectedRouteName);

            $this->assertTrue($service->canSkip($request));
        }
    }

}
