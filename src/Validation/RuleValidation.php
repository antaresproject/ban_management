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

namespace Antares\BanManagement\Validation;

use Antares\Support\Validator;

class RuleValidation extends Validator
{

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'value'      => ['required', 'max:255', 'notSubmitterIpHostname'],
        'expired_at' => ['date'],
    ];

    /**
     * {@inheritdoc}
     */
    protected $events = [
        'antares.validate: rule',
    ];

    /**
     * {@inheritdoc}
     */
    protected $phrases = [
        'value.unique'                    => 'A rule for IP/Value is already exist.',
        'reason.required'                 => 'This field is required and cannot be empty.',
        'value.not_submitter_ip_hostname' => 'Cannot put this IP/Hostname pattern because you are using it right now.',
    ];

    /**
     * Scenario for creating.
     *
     * @return void
     */
    public function onCreate()
    {
        $this->rules['value'][] = 'unique:tbl_ban_management_rules,value';
    }

    /**
     * Remove rules which restrict submitter.
     *
     * @return void
     */
    public function onBanSelf()
    {
        $rulesToRemove = ['notSubmitterIpHostname'];
        $rules         = (array) array_get($this->rules, 'value', []);

        foreach ($rules as $index => $rule) {
            if (in_array($rule, $rulesToRemove, true)) {
                unset($this->rules['value'][$index]);
            }
        }
    }

}
