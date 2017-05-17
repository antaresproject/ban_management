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

namespace Antares\Modules\BanManagement\Listeners;

use Mockery as m;
use Antares\Testbench\TestCase;

class CookieBanListenerTest extends TestCase
{

    /**
     * @var Mockery
     */
    protected $cookieBanService;

    public function setUp()
    {
        parent::setUp();

        $this->cookieBanService = m::mock('\Antares\Modules\BanManagement\Services\CookieBanService');
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testHandleMethod()
    {
        $this->cookieBanService
                ->shouldReceive('add')
                ->once()
                ->andReturnNull()
                ->getMock();

        $bannedEvent = m::mock('Antares\Modules\BanManagement\Events\Banned');

        $listener = new CookieBanListener($this->cookieBanService);
        $listener->handle($bannedEvent);
    }

}
