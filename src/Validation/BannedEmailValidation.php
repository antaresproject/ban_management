<?php

/**
 * Part of the Antares package.
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
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Modules\BanManagement\Validation;

use Antares\Support\Validator;

class BannedEmailValidation extends Validator
{

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'email'      => ['required', 'email', 'max:255', 'notSubmitterEmail'],
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
        'email.unique'              => 'The e-mail address is already exist.',
        'email.email'               => 'The email is not valid e-mail address.',
        'email.required'            => 'The email is required and cannot be empty.',
        'email.not_submitter_email' => 'Cannot put this email address pattern because you are using it right now.',
    ];

    /**
     * Scenario for creating.
     *
     * @return void
     */
    public function onCreate()
    {
        $this->rules['email'][] = 'unique:tbl_ban_management_banned_emails';
    }

}
