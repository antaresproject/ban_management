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

namespace Antares\Modules\BanManagement\Console\Commands\Rules;

use Antares\Modules\BanManagement\Contracts\RuleContract;
use Illuminate\Console\Command;
use Antares\Modules\BanManagement\Processor\RulesProcessor;
use Antares\Modules\BanManagement\Contracts\RuleStoreListener;
use Illuminate\Support\MessageBag;

class AddCommand extends Command implements RuleStoreListener
{

    /**
     * {@inheritdoc}
     */
    protected $signature = 'ban-management:add-rule 
    {ip : IP rule} 
    {--disabled : If the rule should be disabled} 
    {--trusted : If the rule should be trusted}
    {--canBanSelf : If true then the submitter can be banned himself}
    {--reason= : Reason of the rule}
    {--expire= : Expiration date}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Add a new Ban Rule.';

    /**
     * Rules processor instance.
     *
     * @var RulesProcessor
     */
    protected $rulesProcessor;

    /**
     * AddCommand constructor.
     * @param RulesProcessor $rulesProcessor
     */
    public function __construct(RulesProcessor $rulesProcessor)
    {
        parent::__construct();

        $this->rulesProcessor = $rulesProcessor;
    }

    /**
     * Handles the command.
     */
    public function handle()
    {
        $data = [
            'value'      => $this->argument('ip'),
            'enabled'    => !$this->option('disabled'),
            'trusted'    => $this->option('trusted'),
            'reason'     => $this->option('reason'),
            'expired_at' => $this->option('expire'),
        ];

        $this->rulesProcessor->store($this, $data, $this->option('canBanSelf'));
    }

    /**
     * Validation error message for the storing rule.
     * 
     * @param $errors
     */
    public function createRuleFailedValidation(MessageBag $errors)
    {
        $message = implode("\n\r", $errors->all());
        $this->error($message);
    }

    /**
     * Success message for the stored rule.
     *
     * @param RuleContract $rule
     */
    public function storeRuleSuccess(RuleContract $rule)
    {
        $this->info('Ban rule has been added.');
    }

    /**
     * Error message for the storing rule.
     *
     * @param array $errors
     */
    public function storeRuleFailed(array $errors)
    {
        $message = trans('antares/ban_management::response.rules.create.db-failed', $errors);

        $this->error($message);
    }

}
