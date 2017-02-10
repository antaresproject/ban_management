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
    'rule'   => [
        'value'           => 'IP Address or Hostname',
        'email'           => 'E-mail Address',
        'enabled'         => 'Enabled',
        'trusted'         => 'Trusted',
        'edit'            => 'Edit',
        'delete'          => 'Delete',
        'reason'          => 'Reason',
        'internal_reason' => 'Internal Reason',
        'note'            => 'Note',
        'expiration_date' => 'Expiration Date',
        'wildcard_tip'    => 'Field can be filled as wildcard mask. <br/>E.g. 192.168.0.* - IPs starting with 192.168.0, same as IP range 192.168.0.0-192.168.0.255.',
        'trusted_tip'     => 'Rule defined as trusted will be added to the whitelist. Useful when ban subject is a wide range.',
    ],
    'config' => [
        'max-attempts' => 'Automatically ban IP on X failed login attempts',
        'ip-whitelist' => 'IP Whitelist',
    ],
];
