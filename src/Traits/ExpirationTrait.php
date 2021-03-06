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

namespace Antares\Modules\BanManagement\Traits;

use Carbon\Carbon;

trait ExpirationTrait
{

    /**
     * Checks if the rule is expired based on the given date.
     *
     * @param Carbon $date
     * @return bool
     */
    public function isExpired(Carbon $date)
    {
        if ($this->getExpirationDate() instanceof Carbon) {
            return $this->getExpirationDate()->lte($date);
        }

        return false;
    }

    /**
     * Returns the date when the rule will expire, otherwise returns null.
     *
     * @return Carbon|null
     */
    public abstract function getExpirationDate();
}
