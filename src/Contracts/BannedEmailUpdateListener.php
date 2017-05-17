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

use Antares\Modules\BanManagement\Model\BannedEmail;

interface BannedEmailUpdateListener
{

    /**
     * Handles the not found banned email.
     *
     * @param int $id
     */
    public function notFound($id);

    /**
     * Handles the validation error while updating banned email.
     *
     * @param $errors
     * @param int $id
     */
    public function updateBannedEmailFailedValidation($errors, $id);

    /**
     * Handles the successfully updated banned email.
     *
     * @param BannedEmail $bannedEmail
     */
    public function updateBannedEmailSuccess(BannedEmail $bannedEmail);

    /**
     * Handles the failed updated banned email.
     *
     * @param array $errors
     */
    public function updateBannedEmailFailed(array $errors);
}
