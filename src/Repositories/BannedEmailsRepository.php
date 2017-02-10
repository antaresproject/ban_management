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

namespace Antares\BanManagement\Repositories;

use Antares\BanManagement\Contracts\BannedEmailsRepositoryContract;
use Antares\BanManagement\Model\BannedEmail;

class BannedEmailsRepository implements BannedEmailsRepositoryContract
{

    /**
     * The Banned email model instance.
     *
     * @var BannedEmail
     */
    protected $model;

    /**
     * BannedEmailsRepository constructor.
     * @param BannedEmail $model
     */
    public function __construct(BannedEmail $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->model->newQuery()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function datatable(array $columns = ['*'])
    {
        return $this->model->newQuery()->select($columns)->with('user')->orderBy('id', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail($email)
    {
        return $this->model->newQuery()->where('email', $email)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function store(BannedEmail $bannedEmail)
    {
        if (auth()->check()) {
            $bannedEmail->user_id = auth()->user()->id;
        }

        $bannedEmail->save();
    }

    /**
     * {@inheritdoc}
     */
    public function update(BannedEmail $bannedEmail)
    {
        $bannedEmail->save();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(BannedEmail $bannedEmail)
    {
        $bannedEmail->delete();
    }

}
