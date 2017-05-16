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

interface RuleUpdateListener
{

    /**
     * Redirects to index page for not found rule.
     *
     * @param $id
     */
    public function notFound($id);

    /**
     * Handles the validation error while updating rule.
     *
     * @param $errors
     * @param int $id
     */
    public function updateRuleFailedValidation($errors, $id);

    /**
     * Handles the successfully updated rule.
     *
     * @param RuleContract $rule
     */
    public function updateRuleSuccess(RuleContract $rule);

    /**
     * Handles the failed updated rule.
     *
     * @param array $errors
     */
    public function updateRuleFailed(array $errors);
}
