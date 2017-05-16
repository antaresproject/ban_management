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

namespace Antares\Modules\BanManagement\Rules;

use Mockery as m;
use Antares\Testbench\TestCase;
use M6Web\Component\Firewall\Entry\AbstractEntry;

class HostnameTest extends TestCase
{

    public function testInstance()
    {
        $rule = new Hostname('domain.com');
        $this->assertInstanceOf(AbstractEntry::class, $rule);
    }

    public function testValidMatchMethod()
    {
        $hostnames = [
            'domain.com',
            '1domain.com',
            'domain',
            'aaa.ssss.vvvv.bbbb.rdddd',
        ];

        foreach ($hostnames as $hostname) {
            $this->assertTrue(Hostname::match($hostname));
        }
    }

    public function testMatchingEntries()
    {
        $template = 'domain.*';
        $expected = [$template];
        $rule     = new Hostname($template);

        return $this->assertEquals($expected, $rule->getMatchingEntries());
    }

    public function testInvalidMatchMethod()
    {
        $hostnames = [
            '',
            '!sdsd',
            '@sdasd.com',
            '//dfdasads',
            '1dsfjdsj@aasdasdsad.com',
        ];

        foreach ($hostnames as $hostname) {
            $this->assertFalse(Hostname::match($hostname));
        }
    }

    public function testValidCheckMethod()
    {
        $map = [
            'domain.com'   => 'domain.com',
            'domain.*'     => 'domain.com',
            '*.domain.com' => 'aa.domain.com',
            'domain*.com'  => 'domainaaa.com',
        ];

        foreach ($map as $template => $entry) {
            $rule = new Hostname($template);

            $this->assertTrue($rule->check($entry));
        }
    }

    public function testInvalidCheckMethod()
    {
        $map = [
            'domain.com'   => 'domain.com22',
            'domain.*'     => 'domain33.com',
            '*.domain.com' => 'domain.com',
        ];

        foreach ($map as $template => $entry) {
            $rule = new Hostname($template);

            $this->assertFalse($rule->check($entry));
        }
    }

}
