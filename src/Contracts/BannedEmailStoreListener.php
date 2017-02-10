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

use Antares\BanManagement\Model\BannedEmail;
use Illuminate\Support\MessageBag;

interface BannedEmailStoreListener
{

    /**
     * Handles the validation error while creating banned email.
     *
     * @param MessageBag $errors
     */
    public function createBannedEmailFailedValidation(MessageBag $errors);

    /**
     * Handles the successfully stored banned email.
     *
     * @param BannedEmail $bannedEmail
     */
    public function storeBannedEmailSuccess(BannedEmail $bannedEmail);

    /**
     * Handles the failed stored banned email.
     *
     * @param array $errors
     */
    public function storeBannedEmailFailed(array $errors);
}
