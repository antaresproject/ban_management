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

namespace Antares\BanManagement\Contracts;

use Carbon\Carbon;

interface ExpirableContract
{

    /**
     * Checks if the rule is expired based on the given date.
     *
     * @param Carbon $date
     * @return bool
     */
    public function isExpired(Carbon $date);

    /**
     * Returns the date when the rule will expire, otherwise returns null.
     *
     * @return Carbon|null
     */
    public function getExpirationDate();
}
