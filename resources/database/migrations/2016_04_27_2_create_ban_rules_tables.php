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
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBanRulesTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            $this->down();

            Schema::create('tbl_ban_management_rules', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->nullable()->index('user_id');
                $table->string('value');
                $table->boolean('enabled')->default(1)->index();
                $table->boolean('trusted')->default(0);
                $table->text('internal_reason')->nullable();
                $table->text('reason')->nullable();
                $table->datetime('expired_at')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
            Schema::table('tbl_ban_management_rules', function(Blueprint $table) {
                $table->foreign('user_id', 'tbl_ban_management_rules_fk1')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        } catch (Exception $e) {
            Log::emergency($e);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tbl_ban_management_rules');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
