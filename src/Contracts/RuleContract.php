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

interface RuleContract extends BanReasonContract, ExpirableContract
{

    /**
     * Check if a rule is trusted.
     *
     * @return boolean
     */
    public function isTrusted();

    /**
     * Check if a rule is enabled.
     *
     * @return boolean
     */
    public function isEnabled();

    /**
     * Returns a value of the rule.
     *
     * @return string
     */
    public function getValue();
}
