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
    \M6Web\Component\Firewall\Entry\IPV4::class,
    \M6Web\Component\Firewall\Entry\IPV4CIDR::class,
    \M6Web\Component\Firewall\Entry\IPV4Mask::class,
    \M6Web\Component\Firewall\Entry\IPV4Range::class,
    \M6Web\Component\Firewall\Entry\IPV4Wildcard::class,
    \M6Web\Component\Firewall\Entry\IPV6::class,
    \M6Web\Component\Firewall\Entry\IPV6CIDR::class,
    \M6Web\Component\Firewall\Entry\IPV6Mask::class,
    \M6Web\Component\Firewall\Entry\IPV6Range::class,
    \M6Web\Component\Firewall\Entry\IPV6Wildcard::class,
    \Antares\Modules\BanManagement\Rules\Hostname::class,
];
