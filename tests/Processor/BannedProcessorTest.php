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

namespace Antares\BanManagement\Processor;

use Mockery as m;
use Antares\BanManagement\BanManagementServiceProvider;
use Antares\Testing\ApplicationTestCase;
use Antares\Widget\WidgetServiceProvider;
use Antares\BanManagement\Contracts\BannedListener;

class BannedProcessorTest extends ApplicationTestCase
{

    public function testHandleMethod()
    {
        $listener = m::mock(BannedListener::class)
                ->shouldReceive('showInfoPage')
                ->once()
                ->with(m::type('Array'))
                ->andReturn()
                ->getMock();

        $processor = new BannedProcessor;
        $this->assertNull($processor->handle($listener, m::type('String')));
    }

}
