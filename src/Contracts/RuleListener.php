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

interface RuleListener extends RuleStoreListener, RuleUpdateListener
{

    /**
     * Returns the view for creating rule.
     *
     * @param array $data
     */
    public function showCreateForm(array $data);

    /**
     * Returns the view for editing rule.
     *
     * @param array $data
     */
    public function showEditForm(array $data);

    /**
     * Handles the successfully deleted rule.
     */
    public function deleteRuleSuccess();

    /**
     * Handles the failed deleted rule.
     *
     * @param array $errors
     */
    public function deleteRuleFailed(array $errors);
}
