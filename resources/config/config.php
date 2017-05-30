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
return [
    'assets' => [
        'scripts' => [
            'datatable-js' => 'js/datatable.js',
        ],
    ],
    'ddos'   => [
        'enabled'      => true,
        'redirect'     => '/302.html',
        'interval'     => 35,
        'enable_after' => 60
    ]
];
