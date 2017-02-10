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

use Antares\BanManagement\Model\Rule;
use Exception;
use Carbon\Carbon;

interface RulesRepositoryContract
{

    /**
     * Returns all rules.
     *
     * @return RuleContract[]
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
     * Returns collection of enabled white listed rules.
     *
     * @return \Illuminate\Database\Eloquent\Collection|RuleContract[]
     */
    public function getEnabledWhitelist();

    /**
     * Returns all rules which are enabled.
     *
     * @return RuleContract[]
     */
    public function enabled();

    /**
     * Returns all rules which are enabled and not expired.
     *
     * @param Carbon $date
     * @return RuleContract[]
     */
    public function enabledUntilDate(Carbon $date);

    /**
     * Returns the model by the given id.
     *
     * @param int $id
     * @return RuleContract
     */
    public function findById($id);

    /**
     * Store the given model in the database.
     *
     * @param Rule $rule
     * @throws Exception
     */
    public function store(Rule $rule);

    /**
     * Update the given model in the database.
     *
     * @param Rule $rule
     * @throws Exception
     */
    public function update(Rule $rule);

    /**
     * Delete the given model from the database.
     *
     * @param Rule $rule
     * @throws Exception
     */
    public function delete(Rule $rule);
}
