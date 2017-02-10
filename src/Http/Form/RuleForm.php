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

namespace Antares\BanManagement\Http\Form;

use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Contracts\Html\Form\Grid as FormGrid;
use Antares\Contracts\Html\Form\Presenter;
use Antares\BanManagement\Model\Rule;
use Antares\BanManagement\Validation\RuleValidation;

class RuleForm
{

    /**
     * Form factory instance.
     *
     * @var FormFactory
     */
    protected $form;

    /**
     * Rule validation instance.
     *
     * @var RuleValidation
     */
    protected $validation;

    /**
     * If true then client can create rule which restrict access of himself.
     *
     * @var boolean
     */
    protected $canBanSelf;

    /**
     * RuleForm constructor.
     * @param FormFactory $form
     * @param RuleValidation $validation
     */
    public function __construct(FormFactory $form, RuleValidation $validation)
    {
        $this->form       = $form;
        $this->validation = $validation;
    }

    /**
     *
     * 
     * @param boolean $canBanSelf
     * @return \Antares\BanManagement\Http\Form\RuleForm
     */
    public function canBanSelf($canBanSelf)
    {
        $this->canBanSelf = $canBanSelf;
        return $this;
    }

    /**
     * Builds the form.
     *
     * @param Presenter $listener
     * @param Rule $rule
     * @return \Antares\Contracts\Html\Builder
     */
    public function build(Presenter $listener, Rule $rule)
    {
//        vdump(handles('antares::ban_management/rules/update/'));
//        exit;
        publish('ban_management', 'assets.scripts');
        return $this->form->of('antares.ban_management.rules', function(FormGrid $form) use($listener, $rule) {
                    $url  = $rule->exists ? handles('ban_management.rules.update', compact('rule')) : handles('ban_management.rules.store');
                    $attr = ['method' => $rule->exists ? 'PUT' : 'POST'];

                    $form->setup($listener, $url, $rule, $attr);
                    $form->name('Ban rule form');
                    $form->hidden('id');
                    $form->layout('antares/foundation::components.form');

                    $form->fieldset($rule->exists ? 'Edit IP Rule' : 'New IP Rule', function (Fieldset $fieldset) use($rule) {
                        $this->setupFieldset($fieldset, $rule);
                    });

                    $scenario   = $rule->exists ? 'update' : 'create';
                    $validation = $this->validation->on($scenario);

                    if (method_exists($validation, 'on' . ucfirst($scenario))) {
                        $validation->{'on' . ucfirst($scenario)}();
                    }

                    if ($this->canBanSelf) {
                        $validation->on('banSelf');
                        $validation->onBanSelf();
                    }

                    $form->ajaxable()->rules($validation->getValidationRules())->phrases($validation->getValidationPhrases());
                });
    }

    /**
     * Setups fieldsets of the form.
     *
     * @param Fieldset $fieldset
     * @param Rule $rule
     */
    protected function setupFieldset(Fieldset $fieldset, Rule $rule)
    {
        $fieldset->control('input:text', 'value')
                ->value($rule->getValue())
                ->label(trans('antares/ban_management::label.rule.value'))
                ->help(trans('antares/ban_management::label.rule.wildcard_tip'));

        $fieldset->control('input:text', 'expired_at')
                ->value($rule->getExpirationDate() ? $rule->getExpirationDate()->toDateString() : '')
                ->attributes([
                    'class' => 'datepicker-expired_at',
                ])
                ->label(trans('antares/ban_management::label.rule.expiration_date'));

        $fieldset->control('input:hidden', 'enabled')->value(0);
        $fieldset->control('input:hidden', 'trusted')->value(0);

        $fieldset->control('input:checkbox', 'enabled')
                ->attributes($rule->isEnabled() ? ['checked'] : [])
                ->value(1)
                ->label(trans('antares/ban_management::label.rule.enabled'));

        $fieldset->control('input:checkbox', 'trusted')
                ->attributes($rule->isTrusted() ? ['checked'] : [])
                ->value(1)
                ->label(trans('antares/ban_management::label.rule.trusted'))
                ->help(trans('antares/ban_management::label.rule.trusted_tip'));

        $fieldset->control('input:text', 'internal_reason')
                ->value($rule->getInternalReason())
                ->label(trans('antares/ban_management::label.rule.note'));


        $fieldset->control('input:textarea', 'reason')
                ->value($rule->getReason())
                ->label(trans('antares/ban_management::label.rule.reason'));


        $fieldset->control('button', 'button')
                ->attributes(['type' => 'submit', 'class' => 'btn btn-primary'])
                ->value(trans('antares/foundation::label.save_changes'));

        $fieldset->control('button', 'cancel')
                ->field(function() {
                    return app('html')->link(handles('antares::ban_management/rules'), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                });
    }

}
