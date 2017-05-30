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

use Antares\Html\Form\FormBuilder;
use Antares\Support\Validator;
use Antares\Messages\MessageBag;
use Illuminate\Foundation\Application;

class ValidationHelper
{

    /**
     * Form builder instance.
     *
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * Dedicated validator instance.
     *
     * @var Validator
     */
    protected $validator;

    /**
     * array of inputs.
     *
     * @var array
     */
    protected $inputs;

    /**
     * Message bag instance.
     *
     * @var MessageBag
     */
    protected $messageBag;

    /**
     * ValidationHelper constructor.
     * @param FormBuilder $formBuilder
     * @param Validator $validator
     * @param array $inputs
     */
    public function __construct(FormBuilder $formBuilder, Validator $validator, array $inputs = [])
    {
        $this->formBuilder = $formBuilder;
        $this->validator   = $validator;
        $this->inputs      = $inputs;
    }

    /**
     * Checks if the the given inputs data are valid.
     *
     * @param bool $sendHeaders
     * @return bool
     */
    public function isValid($sendHeaders = true)
    {
        if (Application::getInstance()->runningInConsole()) {
            return !$this->validator->with($this->inputs)->fails();
        }

        return $this->formBuilder->isValid($sendHeaders);
    }

    /**
     * Returns the validation messages.
     *
     * @return MessageBag
     */
    public function getMessageBag()
    {
        if (Application::getInstance()->runningInConsole()) {
            return $this->validator->with($this->inputs)->getMessageBag();
        }

        return $this->formBuilder->getMessageBag();
    }

}
