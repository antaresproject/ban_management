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

namespace Antares\Modules\BanManagement\Collections;

use Antares\Modules\BanManagement\Contracts\RulesRepositoryContract;
use Antares\Modules\BanManagement\Contracts\RuleContract;
use Antares\Testbench\TestCase;
use Mockery as m;

class RuleTest extends TestCase
{

    /**
     * @var Mockery
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->repository = m::mock(RulesRepositoryContract::class);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testEmptyListsAndInternalFetchMethod()
    {
        $this->repository
                ->shouldReceive('enabledUntilDate')
                ->once()
                ->andReturn([])
                ->getMock();

        $rules = new Rules($this->repository);

        $this->assertCount(0, $rules->getBlackList());
        $this->assertCount(0, $rules->getWhiteList());
    }

    public function testAddingWhitelistedRule()
    {
        $rule = m::mock(RuleContract::class)
                ->shouldReceive('isTrusted')
                ->once()
                ->andReturn(true)
                ->getMock();

        $this->repository
                ->shouldReceive('enabledUntilDate')
                ->once()
                ->andReturn([$rule])
                ->getMock();

        $rules = new Rules($this->repository);

        $this->assertCount(0, $rules->getBlackList());
        $this->assertCount(1, $rules->getWhiteList());
    }

    public function testAddingNotWhitelistedRule()
    {
        $rule = m::mock(RuleContract::class)
                ->shouldReceive('isTrusted')
                ->once()
                ->andReturn(false)
                ->getMock();

        $this->repository
                ->shouldReceive('enabledUntilDate')
                ->once()
                ->andReturn([$rule])
                ->getMock();

        $rules = new Rules($this->repository);

        $this->assertCount(1, $rules->getBlackList());
        $this->assertCount(0, $rules->getWhiteList());
    }

}
