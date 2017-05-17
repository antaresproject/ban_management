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

namespace Antares\Modules\BanManagement\Http\Form;

use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Contracts\Html\Form\Grid as FormGrid;
use Antares\Contracts\Html\Form\Presenter;
use Antares\Modules\BanManagement\Model\BannedEmail;
use Antares\Modules\BanManagement\Validation\BannedEmailValidation;

class BannedEmailForm
{

    /**
     * Form factory instance.
     *
     * @var FormFactory
     */
    protected $form;

    /**
     * Banned emails validation instance.
     *
     * @var BannedEmailValidation
     */
    protected $validation;

    /**
     * RuleForm constructor.
     * @param FormFactory $form
     * @param BannedEmailValidation $validation
     */
    public function __construct(FormFactory $form, BannedEmailValidation $validation)
    {
        $this->form       = $form;
        $this->validation = $validation;
    }

    /**
     * Builds the form.
     *
     * @param Presenter $listener
     * @param BannedEmail $bannedEmail
     * @return \Antares\Contracts\Html\Builder
     */
    public function build(Presenter $listener, BannedEmail $bannedEmail)
    {
        return $this->form->of('antares.ban_management.banned-emails', function(FormGrid $form) use($listener, $bannedEmail) {
                    $url = $bannedEmail->exists ? route('bannedemails.update', compact('bannedEmail')) : route('bannedemails.store');

                    $attr = ['method' => $bannedEmail->exists ? 'PUT' : 'POST'];

                    $form->setup($listener, $url, $bannedEmail, $attr);
                    $form->name('Banned Emails Form');
                    $form->hidden('id');
                    $form->layout('antares/foundation::components.form');

                    $form->fieldset($bannedEmail->exists ? 'Edit Banned Email' : 'New Banned Email', function (Fieldset $fieldset) use($bannedEmail) {
                        $this->setupFieldset($fieldset, $bannedEmail);
                    });

                    $scenario   = $bannedEmail->exists ? 'update' : 'create';
                    $validation = $this->validation->on($scenario);

                    if (method_exists($validation, 'on' . ucfirst($scenario))) {
                        $validation->{'on' . ucfirst($scenario)}();
                    }

                    $form->ajaxable()->rules($validation->getValidationRules())->phrases($validation->getValidationPhrases());
                });
    }

    /**
     * Setups fieldsets of the form.
     *
     * @param Fieldset $fieldset
     * @param BannedEmail $bannedEmail
     */
    protected function setupFieldset(Fieldset $fieldset, BannedEmail $bannedEmail)
    {
        $fieldset->control('input:text', 'email')
                ->value($bannedEmail->getEmail())
                ->label(trans('antares/ban_management::label.rule.email'));

        $fieldset->control('input:text', 'expired_at')
                ->value($bannedEmail->getExpirationDate() ? $bannedEmail->getExpirationDate()->toDateString() : '')
                ->attributes([
                    'class' => 'datepicker-expired_at',
                ])
                ->label(trans('antares/ban_management::label.rule.expiration_date'));

        $fieldset->control('input:text', 'internal_reason')
                ->value($bannedEmail->getInternalReason())
                ->label(trans('antares/ban_management::label.rule.note'));


        $fieldset->control('input:textarea', 'reason')
                ->value($bannedEmail->getReason())
                ->label(trans('antares/ban_management::label.rule.reason'));


        $fieldset->control('button', 'button')
                ->attributes(['type' => 'submit', 'class' => 'btn btn-primary'])
                ->value(trans('antares/foundation::label.save_changes'));

        $fieldset->control('button', 'cancel')
                ->field(function() {
                    return app('html')->link(handles('antares::ban_management/bannedemails'), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                });
    }

}
