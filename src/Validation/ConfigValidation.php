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

class ConfigValidation extends Validator
{

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'options.max_failed_attempts' => ['required', 'numeric', 'min:0'],
    ];

    /**
     * {@inheritdoc}
     */
    protected $phrases = [
        'options.max_failed_attempts.required' => 'The max failed attempts must be specified.',
        'options.max_failed_attempts.numeric'  => 'The max failed attempts must be numeric.',
        'options.max_failed_attempts.min'      => 'The max failed attempts must have non-negative digits.',
    ];

}
