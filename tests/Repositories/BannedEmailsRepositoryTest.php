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
use Antares\BanManagement\Model\BannedEmail;
use CreateBanEmailsTables;
use Illuminate\Support\Collection;
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

        $this->app->make(CreateBanEmailsTables::class)->up();

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
        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            BannedEmail::create([
                'email' => $faker->email,
            ]);
        }

        $model = $this->repository->findById(1);
        $empty = $this->repository->findById(999);

        $this->assertInstanceOf(BannedEmail::class, $model);
        $this->assertNull($empty);
    }

    public function testFindByEmailMethod()
    {
        $faker = Factory::create();

        foreach (range(0, 20) as $index) {
            BannedEmail::create([
                'email' => $faker->email,
            ]);
        }

        $email = BannedEmail::find(1)->email;

        $model = $this->repository->findByEmail($email);
        $empty = $this->repository->findByEmail($faker->email);

        $this->assertInstanceOf(BannedEmail::class, $model);
        $this->assertNull($empty);
    }

    public function testStoreMethod()
    {
        $this->assertCount(0, $this->repository->all());

        $faker = Factory::create();

        $model = new BannedEmail;
        $model->fill([
            'email'  => $faker->email,
            'reason' => $faker->paragraph,
        ]);

        $response = $this->repository->store($model);

        $this->assertCount(1, $this->repository->all());
        $this->assertNull($response);
    }

    public function testUpdateMethod()
    {
        $faker = Factory::create();

        $model = new BannedEmail;
        $model->fill([
            'id'     => 1,
            'email'  => $faker->email,
            'reason' => 'old reason',
        ]);

        $this->repository->store($model);
        $model         = $this->repository->findById(1);
        $model->reason = 'new reason';

        $response = $this->repository->update($model);
        $model    = $this->repository->findById(1);

        $this->assertEquals('new reason', $model->getReason());
        $this->assertNull($response);
    }

    public function testDeleteMethod()
    {
        $faker = Factory::create();

        $model = new BannedEmail;
        $model->fill([
            'email' => $faker->email,
        ]);

        $this->repository->store($model);

        $this->assertCount(1, $this->repository->all());

        $response = $this->repository->delete($model);

        $this->assertCount(0, $this->repository->all());
        $this->assertNull($response);
    }

}
