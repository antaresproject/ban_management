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

namespace Antares\BanManagement\Http\Handlers;

use Antares\BanManagement\Processor\RulesProcessor;
use Antares\BanManagement\Contracts\RuleContract;
use Antares\Html\Form\Grid as FormGrid;
use Antares\BanManagement\Config;
use Antares\Html\Form\FormBuilder;
use Antares\Html\Form\Fieldset;
use Illuminate\Http\Request;

class SimpleConfig
{

    /**
     * Config instance.
     *
     * @var Config
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Rules Processor instance.
     *
     * @var RulesProcessor
     */
    protected $rulesProcessor;

    /**
     * SimpleConfig constructor.
     * @param Config $config
     * @param Request $request
     * @param RulesProcessor $rulesProcessor
     */
    public function __construct(Config $config, Request $request, RulesProcessor $rulesProcessor)
    {
        $this->config         = $config;
        $this->request        = $request;
        $this->rulesProcessor = $rulesProcessor;
    }

    /**
     * Handles the simple config of the module.
     *
     * @param array $options
     * @param FormBuilder $form
     */
    public function handle(array $options, $form)
    {
        $this->extendForm($form);
    }

    /**
     * Extends the form instance.
     *
     * @param FormBuilder $form
     */
    protected function extendForm(FormBuilder $form)
    {
        $form->extend(function(FormGrid $form) {
            $fieldsetName = trans('antares/ban_management::title.config.title');

            $form->findFieldsetOrCreateNew($fieldsetName, function(Fieldset $fieldset) use($fieldsetName) {
                $fieldset->legend($fieldsetName);
                $rules = $this->rulesProcessor->getWhitelist()->map(function(RuleContract $rule) {
                            return $rule->getValue();
                        })->toArray();

                $fieldset->control('input:text', 'ban-management[options][max_failed_attempts]')
                        ->value($this->config->getMaxFailedAttempts())
                        ->label(trans('antares/ban_management::label.config.max-attempts'));

                $fieldset->control('input:textarea', 'ban-management[rules][ip-whitelist]')
                        ->value(implode("\r\n", $rules))
                        ->label(trans('antares/ban_management::label.config.ip-whitelist'))
                        ->attributes(['rows' => 5, 'cols' => 50])
                        ->help(trans('antares/ban_management::label.config.ip_whitelist_help'));

                $fieldset->control('input:text', 'cancel')
                        ->label('')
                        ->field(function() {
                            return app('html')->link(handles("antares::ban_management/rules/datatable"), trans('antares/ban_management::title.go_to_ban_management'));
                        });
            });
        });
    }

}
