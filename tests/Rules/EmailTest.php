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

namespace Antares\Modules\BanManagement\Rules;

use Mockery as m;
use Antares\Testbench\TestCase;
use M6Web\Component\Firewall\Entry\AbstractEntry;

class EmailTest extends TestCase
{

    public function testInstance()
    {
        $rule = new Email('some@email.com');
        $this->assertInstanceOf(AbstractEntry::class, $rule);
    }

    public function testValidMatchMethod()
    {
        $emails = [
            'a+b@domain.com',
            'a-b@aa.com',
            'a.b@domain.pl',
            'a%b@dom.com',
            'a_b@dom.com',
            'A.B@DOM.cOm',
            'a@domain.abcdef',
            'a@d-o.m0123456789.com'
        ];

        foreach ($emails as $email) {
            $this->assertTrue(Email::match($email));
        }
    }

    public function testMatchingEntries()
    {
        $template = 'domain.*';
        $expected = [$template];
        $rule     = new Email($template);

        return $this->assertEquals($expected, $rule->getMatchingEntries());
    }

    public function testInvalidMatchMethod()
    {
        $emails = [
            '',
            '!sdsd',
            '@sdasd.com',
            '//dfdasads',
            '@.com',
            'aa@d.abcdefg'
        ];

        foreach ($emails as $email) {
            $this->assertFalse(Email::match($email));
        }
    }

    public function testValidCheckMethod()
    {
        $map = [
            '*@domain.com' => 'aaa@domain.com',
            '*@domain.com' => 'aaa.bbb@domain.com',
            '*@do*.com'    => 'aaaa@domain.com',
            'aaaa@*.*'     => 'aaaa@domain.com',
        ];

        foreach ($map as $template => $entry) {
            $rule = new Email($template);
            $this->assertTrue($rule->check($entry), 'Template rule: ' . $template . ' to ' . $entry);
        }
    }

    public function testInvalidCheckMethod()
    {
        $map = [
            'a@domain.com' => 'a@domain.com22',
            'a@domain.*'   => 'a@domain33.com',
            '*@domain.com' => 'domain.com',
        ];

        foreach ($map as $template => $entry) {
            $rule = new Email($template);

            $this->assertFalse($rule->check($entry));
        }
    }

}
