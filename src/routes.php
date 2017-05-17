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
use Antares\Routing\Router;

//use Illuminate\Routing\Router;

/* @var $router Router */


$router->group(['prefix' => 'ban_management'], function (Router $router) {

    $router->match(['GET', 'POST', 'PUT'], 'rules/datatable', 'RulesController@datatable');
    $router->resource('rules', 'RulesController');
    $router->match(['GET', 'POST', 'PUT'], 'bannedemails/datatable', 'BannedEmailsController@datatable');
    $router->resource('bannedemails', 'BannedEmailsController');
    $router->get('config', 'ConfigController@edit')->name('ban_management.config.edit');
    $router->put('config', 'ConfigController@update')->name('ban_management.config.update');
});
