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

use Illuminate\Support\MessageBag;

interface RuleStoreListener
{

    /**
     * Handles the validation error while creating rule.
     *
     * @param MessageBag $errors
     */
    public function createRuleFailedValidation(MessageBag $errors);

    /**
     * Handles the successfully stored rule.
     *
     * @param RuleContract $rule
     */
    public function storeRuleSuccess(RuleContract $rule);

    /**
     * Handles the failed stored rule.
     * 
     * @param array $errors
     */
    public function storeRuleFailed(array $errors);
}
