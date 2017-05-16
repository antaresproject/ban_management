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

use Antares\Modules\BanManagement\Model\BannedEmail;
use Exception;

interface BannedEmailsRepositoryContract
{

    /**
     * Returns all banned emails.
     *
     * @return BannedEmail[]
     */
    public function all();

    /**
     * Returns the Query Builder for Datatable.
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function datatable(array $columns = ['*']);

    /**
     * Returns the model by the given id.
     *
     * @param int $id
     * @return BannedEmail
     */
    public function findById($id);

    /**
     * Returns the model by the given email address.
     *
     * @param string $email
     * @return BannedEmail
     */
    public function findByEmail($email);

    /**
     * Store the given model in the database.
     *
     * @param BannedEmail $bannedEmail
     * @throws Exception
     */
    public function store(BannedEmail $bannedEmail);

    /**
     * Update the given model in the database.
     *
     * @param BannedEmail $bannedEmail
     * @throws Exception
     */
    public function update(BannedEmail $bannedEmail);

    /**
     * Delete the given model from the database.
     *
     * @param BannedEmail $bannedEmail
     * @throws Exception
     */
    public function delete(BannedEmail $bannedEmail);
}
