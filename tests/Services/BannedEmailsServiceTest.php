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

use Antares\Modules\BanManagement\Repositories\BannedEmailsRepository;
use Antares\Modules\BanManagement\Model\BannedEmail;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Antares\Testing\TestCase;
use CreateBanEmailsTables;
use Faker\Factory;
use Carbon\Carbon;
use Mockery as m;

class BannedEmailsServiceTest extends TestCase
{

    /**
     * @var Mockery
     */
    protected $repository;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function setUp()
    {
        parent::setUp();

        $this->app->make(CreateBanEmailsTables::class)->up();

        $this->repository = $this->app->make(BannedEmailsRepository::class);
        $this->filesystem = $this->app->make(Filesystem::class);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @return BannedEmailService
     */
    protected function getBannedEmailsService()
    {
        return new BannedEmailService($this->repository, $this->filesystem);
    }

    public function testSaveToFile()
    {
        BannedEmail::query()->delete();
        DB::beginTransaction();
        $faker   = Factory::create();
        $service = $this->getBannedEmailsService();
        $service->saveToFile();

        $this->assertCount(0, $service->getEmailTemplates());
        $this->assertInternalType('array', $service->getEmailTemplates());

        foreach (range(0, 40) as $index) {
            BannedEmail::create([
                'email'      => $faker->email,
                'expired_at' => Carbon::today()->addDays(1)
            ]);
        }

        $service->saveToFile();

        $this->assertCount(BannedEmail::count(), $service->getEmailTemplates());
        $this->assertInternalType('array', $service->getEmailTemplates());
        DB::rollBack();
    }

    public function testGetEmailTemplates()
    {
        BannedEmail::query()->delete();
        DB::beginTransaction();
        $faker   = Factory::create();
        $service = $this->getBannedEmailsService();

        foreach (range(0, 40) as $index) {
            BannedEmail::create([
                'email'      => $faker->email,
                'expired_at' => Carbon::today()->addDays(1)
            ]);
        }

        $service->saveToFile();
        $emails = $service->getEmailTemplates();
        foreach (BannedEmail::get() as $model) {
            $this->assertTrue(in_array($model->getEmail(), $emails));
        }
        DB::rollback();
    }

    public function testIsEmailBanned()
    {
        BannedEmail::query()->delete();
        DB::beginTransaction();
        $faker   = Factory::create();
        $service = $this->getBannedEmailsService();

        foreach (range(0, 40) as $index) {
            BannedEmail::create([
                'email'      => $faker->email,
                'expired_at' => Carbon::today()->addDays(1)
            ]);
        }

        $service->saveToFile();

        foreach (BannedEmail::get() as $model) {
            $this->assertTrue($service->isEmailBanned($model->getEmail()));
        }

        DB::rollback();
    }

}
