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

namespace Antares\Modules\BanManagement\Model;

use Antares\Testing\ApplicationTestCase;
use Antares\Modules\BanManagement\Contracts\RuleContract;
use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Carbon\Carbon;

class RuleTest extends ApplicationTestCase
{

    use EloquentConnectionTrait;

    public function testContract()
    {
        $rule = new Rule;

        $this->assertInstanceOf(RuleContract::class, $rule);
    }

    public function testDefaultAttributes()
    {
        $rule = new Rule;

        $this->assertTrue($rule->isEnabled());
        $this->assertFalse($rule->isTrusted());
        $this->assertEquals('', $rule->getValue());
        $this->assertEquals(null, $rule->getExpirationDate());
    }

    public function testChangedAttributes()
    {
        $date             = Carbon::now();
        $rule             = new Rule;
        $rule->enabled    = 0;
        $rule->trusted    = 1;
        $rule->value      = 'localhost';
        $rule->expired_at = $date;

        $this->assertFalse($rule->isEnabled());
        $this->assertTrue($rule->isTrusted());
        $this->assertEquals('localhost', $rule->getValue());
        $this->assertEquals($date->format('Y-m-d'), $rule->getExpirationDate()->format('Y-m-d'));
    }

}
