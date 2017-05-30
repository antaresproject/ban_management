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

namespace Antares\Modules\BanManagement\Repositories;

use Antares\Modules\BanManagement\Model\BannedEmail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Antares\Testing\TestCase;
use Carbon\Carbon;
use Faker\Factory;

class BannedEmailsRepositoryTest extends TestCase
{

    /**
     * @var BannedEmailsRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();
        $this->repository = new BannedEmailsRepository(new BannedEmail);
    }

    public function testAllMethod()
    {
        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            BannedEmail::create([
                'email' => $faker->email,
            ]);
        }

        $models = $this->repository->all();

        $this->assertCount(BannedEmail::count(), $models);
        $this->assertInstanceOf(Collection::class, $models);
    }

    public function testDatatableMethod()
    {
        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            BannedEmail::create([
                'email' => $faker->email,
            ]);
        }

        $models = $this->repository->datatable();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $models);
    }

    public function testFindByIdMethod()
    {
        BannedEmail::query()->delete();
        DB::beginTransaction();

        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            BannedEmail::create([
                'email'      => $faker->email,
                'expired_at' => Carbon::today()->addDays(1)
            ]);
        }
        $model = $this->repository->findById(BannedEmail::first()->id);
        $empty = $this->repository->findById(999);

        $this->assertInstanceOf(BannedEmail::class, $model);
        $this->assertNull($empty);
        DB::rollback();
    }

    public function testFindByEmailMethod()
    {
        BannedEmail::query()->delete();
        DB::beginTransaction();
        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            BannedEmail::create([
                'email'      => $faker->email,
                'expired_at' => Carbon::today()->addDays(1)
            ]);
        }

        $email = BannedEmail::find(BannedEmail::first()->id)->email;

        $model = $this->repository->findByEmail($email);
        $empty = $this->repository->findByEmail($faker->email);

        $this->assertInstanceOf(BannedEmail::class, $model);
        $this->assertNull($empty);
        DB::rollback();
    }

    public function testStoreMethod()
    {
        DB::beginTransaction();
        $this->assertCount(0, $this->repository->all());

        $faker = Factory::create();

        $model = new BannedEmail;
        $model->fill([
            'email'      => $faker->email,
            'reason'     => $faker->paragraph,
            'expired_at' => Carbon::today()->addDays(1)
        ]);

        $response = $this->repository->store($model);

        $this->assertCount(1, $this->repository->all());
        $this->assertNull($response);
        DB::rollback();
    }

    public function testUpdateMethod()
    {
        DB::beginTransaction();
        $faker = Factory::create();

        $model = new BannedEmail;
        $model->fill([
            'email'      => $faker->email,
            'reason'     => 'old reason',
            'expired_at' => Carbon::today()->addDays(1)
        ]);

        $this->repository->store($model);
        $model         = $this->repository->findById(BannedEmail::first()->id);
        $model->reason = 'new reason';

        $response = $this->repository->update($model);
        $model    = $this->repository->findById(BannedEmail::first()->id);

        $this->assertEquals('new reason', $model->getReason());
        $this->assertNull($response);
        DB::rollback();
    }

    public function testDeleteMethod()
    {
        DB::beginTransaction();
        $faker = Factory::create();

        $model = new BannedEmail;
        $model->fill([
            'email'      => $faker->email,
            'expired_at' => Carbon::today()->addDays(1)
        ]);

        $this->repository->store($model);

        $this->assertCount(1, $this->repository->all());

        $response = $this->repository->delete($model);

        $this->assertCount(0, $this->repository->all());
        $this->assertNull($response);
        DB::rollback();
    }

}
