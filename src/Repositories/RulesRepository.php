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

use Antares\BanManagement\Contracts\RulesRepositoryContract;
use Antares\BanManagement\Model\Rule;
use Carbon\Carbon;

class RulesRepository implements RulesRepositoryContract
{

    /**
     * The Rule model instance.
     *
     * @var Rule
     */
    protected $model;

    /**
     * RulesRepository constructor.
     * @param Rule $model
     */
    public function __construct(Rule $model)
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
    public function getEnabledWhitelist()
    {
        return $this->model->newQuery()->where('enabled', 1)->where('trusted', 1)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function enabled()
    {
        return $this->model->newQuery()->where('enabled', 1)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function enabledUntilDate(Carbon $date)
    {
        return $this->model->newQuery()->where('enabled', 1)->where('expired_at', '>', $date->toDateTimeString())->get();
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
    public function store(Rule $rule)
    {
        if (auth()->check()) {
            $rule->user_id = auth()->user()->id;
        }

        $rule->save();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Rule $rule)
    {
        $rule->save();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Rule $rule)
    {
        $rule->delete();
    }

}
