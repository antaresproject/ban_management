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
return [
    'rules'        => [
        'create'    => array(
            'success'   => 'Ban Rule has been added successfully',
            'db-failed' => 'An error occurs while creating ban rule.'
        ),
        'update'    => array(
            'success'   => 'Ban Rule has been updated.',
            'db-failed' => 'An error occurs while updating ban rule.'
        ),
        'delete'    => array(
            'success'   => 'Ban Rule has been deleted',
            'db-failed' => 'An error occurs while deleting ban rule.'
        ),
        'notexists' => 'The requested ban rule does not exist.',
    ],
    'bannedemails' => [
        'create'    => array(
            'success'   => 'Banned Email has been added successfully',
            'db-failed' => 'An error occurs while creating banned email.'
        ),
        'update'    => array(
            'success'   => 'Banned Email has been updated.',
            'db-failed' => 'An error occurs while updating banned email.',
        ),
        'delete'    => array(
            'success'   => 'Banned Email has been deleted',
            'db-failed' => 'An error occurs while deleting banned email.',
        ),
        'sync'      => array(
            'success' => 'Synchronization of banned emails has been successfully completed.',
        ),
        'notexists' => 'The requested banned email does not exist.',
    ],
    'banned'       => [
        'info'        => 'You have been banned',
        'reasonLabel' => 'Reason',
    ],
    'config'       => [
        'success' => 'Ban Management configuration has been successfully saved.',
    ],
];
