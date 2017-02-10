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

namespace Antares\BanManagement\Repositories;

use Antares\Testing\TestCase;
use Antares\BanManagement\Model\Rule;
use CreateBanRulesTables;
use Illuminate\Support\Collection;
use Faker\Factory;

class RulesRepositoryTest extends TestCase
{

    /**
     * @var RulesRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->app->make(CreateBanRulesTables::class)->up();

        $this->repository = new RulesRepository(new Rule);
    }

    public function testAllMethod()
    {
        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            Rule::create([
                'value'  => $faker->ipv4,
                'reason' => $faker->paragraph,
            ]);
        }

        $models = $this->repository->all();

        $this->assertCount(Rule::count(), $models);
        $this->assertInstanceOf(Collection::class, $models);
    }

    public function testDatatableMethod()
    {
        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            Rule::create([
                'value'  => $faker->ipv4,
                'reason' => $faker->paragraph,
            ]);
        }

        $models = $this->repository->datatable();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $models);
    }

    public function testEnabledMethod()
    {
        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            Rule::create([
                'value'   => $faker->ipv4,
                'reason'  => $faker->paragraph,
                'enabled' => $faker->boolean(50),
            ]);
        }

        $models = $this->repository->enabled();

        $this->assertInstanceOf(Collection::class, $models);
        $this->assertCount(Rule::where('enabled', '1')->count(), $models);
    }

    public function testFindByIdMethod()
    {
        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            Rule::create([
                'value'   => $faker->ipv4,
                'reason'  => $faker->paragraph,
                'enabled' => $faker->boolean(50),
            ]);
        }

        $model = $this->repository->findById(1);
        $empty = $this->repository->findById(999);

        $this->assertInstanceOf(Rule::class, $model);
        $this->assertNull($empty);
    }

    public function testStoreMethod()
    {
        $this->assertCount(0, $this->repository->all());

        $faker = Factory::create();

        $rule = new Rule;
        $rule->fill([
            'value'   => $faker->ipv4,
            'reason'  => $faker->paragraph,
            'enabled' => $faker->boolean(50),
        ]);

        $response = $this->repository->store($rule);

        $this->assertCount(1, $this->repository->all());
        $this->assertNull($response);
    }

    public function testUpdateMethod()
    {
        $faker = Factory::create();

        $rule = new Rule;
        $rule->fill([
            'id'      => 1,
            'value'   => $faker->ipv4,
            'reason'  => 'old reason',
            'enabled' => $faker->boolean(50),
        ]);

        $this->repository->store($rule);
        $rule         = $this->repository->findById(1);
        $rule->reason = 'new reason';

        $response = $this->repository->update($rule);
        $rule     = $this->repository->findById(1);

        $this->assertEquals('new reason', $rule->getReason());
        $this->assertNull($response);
    }

    public function testDeleteMethod()
    {
        $faker = Factory::create();

        $rule = new Rule;
        $rule->fill([
            'value'   => $faker->ipv4,
            'reason'  => $faker->paragraph,
            'enabled' => $faker->boolean(50),
        ]);

        $this->repository->store($rule);

        $this->assertCount(1, $this->repository->all());

        $response = $this->repository->delete($rule);

        $this->assertCount(0, $this->repository->all());
        $this->assertNull($response);
    }

}
