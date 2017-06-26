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

namespace Antares\Modules\BanManagement\Model;

use Antares\Modules\BanManagement\Contracts\RuleContract;
use Antares\Testbench\TestCase;
use Carbon\Carbon;

class PlainRuleTest extends TestCase
{

    public function testContract()
    {
        $rule = new PlainRule('localhost');

        $this->assertInstanceOf(RuleContract::class, $rule);
    }

    public function testWithDefaultConstructor()
    {
        $rule = new PlainRule('localhost');

        $this->assertTrue($rule->isEnabled());
        $this->assertFalse($rule->isTrusted());
        $this->assertEquals('localhost', $rule->getValue());
        $this->assertEquals(null, $rule->getExpirationDate());
    }

    public function testWithWhitelisted()
    {
        $rule = new PlainRule('localhost', true);

        $this->assertTrue($rule->isEnabled());
        $this->assertTrue($rule->isTrusted());
        $this->assertEquals('localhost', $rule->getValue());
        $this->assertEquals(null, $rule->getExpirationDate());
    }

    public function testWithExpirationDate()
    {
        $date = Carbon::now();
        $rule = new PlainRule('localhost', true, $date);

        $this->assertTrue($rule->isEnabled());
        $this->assertTrue($rule->isTrusted());
        $this->assertEquals('localhost', $rule->getValue());
        $this->assertEquals($date, $rule->getExpirationDate());
    }

}
