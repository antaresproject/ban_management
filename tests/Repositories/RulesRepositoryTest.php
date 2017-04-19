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

use Antares\BanManagement\Model\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Antares\Testing\TestCase;
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
        $this->repository = new RulesRepository(new Rule);
    }

    public function testAllMethod()
    {
        DB::beginTransaction();
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
        DB::rollback();
    }

    public function testDatatableMethod()
    {
        DB::beginTransaction();
        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            Rule::create([
                'value'  => $faker->ipv4,
                'reason' => $faker->paragraph,
            ]);
        }
        $models = $this->repository->datatable();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $models);
        DB::rollback();
    }

    public function testEnabledMethod()
    {
        DB::beginTransaction();
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
        DB::rollback();
    }

    public function testFindByIdMethod()
    {
        Rule::query()->delete();
        DB::beginTransaction();
        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            Rule::create([
                'value'   => $faker->ipv4,
                'reason'  => $faker->paragraph,
                'enabled' => $faker->boolean(50),
            ]);
        }

        $model = $this->repository->findById(Rule::first()->id);
        $empty = $this->repository->findById(999);
        $this->assertInstanceOf(Rule::class, $model);
        $this->assertNull($empty);
        DB::rollback();
    }

    public function testStoreMethod()
    {
        Rule::query()->delete();
        DB::beginTransaction();
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
        DB::rollback();
    }

    public function testUpdateMethod()
    {
        Rule::query()->delete();
        DB::beginTransaction();
        $faker = Factory::create();

        $rule = new Rule;
        $rule->fill([
            'id'      => 1,
            'value'   => $faker->ipv4,
            'reason'  => 'old reason',
            'enabled' => $faker->boolean(50),
        ]);

        $this->repository->store($rule);
        $rule         = $this->repository->findById(Rule::first()->id);
        $rule->reason = 'new reason';

        $response = $this->repository->update($rule);
        $rule     = $this->repository->findById(Rule::first()->id);

        $this->assertEquals('new reason', $rule->getReason());
        $this->assertNull($response);
        DB::rollback();
    }

    public function testDeleteMethod()
    {
        Rule::query()->delete();
        DB::beginTransaction();
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
        DB::rollback();
    }

}
