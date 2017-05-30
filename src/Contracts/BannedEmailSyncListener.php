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

namespace Antares\Modules\BanManagement\Contracts;

interface BannedEmailSyncListener
{

    /**
     * Handles the successfully synchronized banned emails.
     *
     * @param string $msg
     */
    public function syncBannedEmailSuccess($msg);

    /**
     * Handles the failed synchronized banned emails.
     *
     * @param string $msg
     */
    public function syncBannedEmailFailed($msg);
}
