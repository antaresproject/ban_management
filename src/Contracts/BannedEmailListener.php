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

namespace Antares\Modules\BanManagement\Contracts;

interface BannedEmailListener extends BannedEmailStoreListener, BannedEmailUpdateListener
{

    /**
     * Returns the view for creating banned email.
     *
     * @param array $data
     */
    public function showCreateForm(array $data);

    /**
     * Returns the view for editing banned email.
     *
     * @param array $data
     */
    public function showEditForm(array $data);

    /**
     * Handles the successfully deleted banned email.
     */
    public function deleteBannedEmailSuccess();

    /**
     * Handles the failed deleted banned email.
     *
     * @param array $errors
     */
    public function deleteBannedEmailFailed(array $errors);
}
