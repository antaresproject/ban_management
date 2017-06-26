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

namespace Antares\Modules\BanManagement\Services;

use Mockery as m;
use Antares\Testbench\TestCase;
use Antares\Modules\BanManagement\Model\PlainRule;
use Antares\Modules\BanManagement\Collections\Rules;
use M6Web\Component\Firewall\Entry\EntryFactory;
use M6Web\Component\Firewall\Lists\ListMerger;
use Antares\Modules\BanManagement\Contracts\RulesRepositoryContract;

class FirewallServiceTest extends TestCase
{

    /**
     * @var Rules
     */
    protected $rules;

    /**
     * @var EntryFactory
     */
    protected $entryFactory;

    /**
     * @var ListMerger
     */
    protected $listMerger;

    public function setUp()
    {
        parent::setUp();

        $entries = (array) include(__DIR__ . '/../../resources/config/entries.php');

        $this->rules        = $this->getPopulatedRules();
        $this->entryFactory = new EntryFactory($entries);
        $this->listMerger   = new ListMerger;
    }

    /**
     * @return Rules
     */
    protected function getPopulatedRules()
    {
        $repository = m::mock(RulesRepositoryContract::class)
                ->shouldReceive('enabledUntilDate')
                ->once()
                ->andReturn([])
                ->getMock();

        $rules = new Rules($repository);

        $rules->add(new PlainRule('10.10.6.100'));
        $rules->add(new PlainRule('10.10.6.20'));
        $rules->add(new PlainRule('10.10.6.30', true));
        $rules->add(new PlainRule('10.10.6.40'));
        $rules->add(new PlainRule('10.10.6.40', true));
        $rules->add(new PlainRule('blocked.com'));

        return $rules;
    }

    /**
     * @return FirewallService
     */
    protected function getFirewall()
    {
        return new FirewallService($this->rules, $this->entryFactory, $this->listMerger);
    }

    public function testRequestAllowed()
    {
        $request = m::mock('Symfony\Component\HttpFoundation\Request')
                ->shouldReceive('getClientIp')
                ->once()
                ->andReturn('10.10.6.100')
                ->getMock();

        $firewall = $this->getFirewall();

        $this->assertFalse($firewall->isRequestAllowed($request));
    }

    public function testNotAllowedIps()
    {
        $firewall = $this->getFirewall();

        $this->assertFalse($firewall->isIpAllowed('10.10.6.100'));
        $this->assertFalse($firewall->isIpAllowed('10.10.6.20'));
    }

    public function testAllowedIps()
    {
        $firewall = $this->getFirewall();

        $this->assertTrue($firewall->isIpAllowed('10.10.6.30'));
    }

    public function testAllowedHostname()
    {
        $firewall = $this->getFirewall();

        $this->assertTrue($firewall->isIpAllowed('domain.com'));
        $this->assertFalse($firewall->isIpAllowed('blocked.com'));
    }

}
