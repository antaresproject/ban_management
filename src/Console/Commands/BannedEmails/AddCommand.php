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

namespace Antares\Modules\BanManagement\Console\Commands\BannedEmails;

use Antares\Modules\BanManagement\Model\BannedEmail;
use Illuminate\Console\Command;
use Antares\Modules\BanManagement\Processor\BannedEmailsProcessor;
use Antares\Modules\BanManagement\Contracts\BannedEmailStoreListener;
use Illuminate\Support\MessageBag;

class AddCommand extends Command implements BannedEmailStoreListener
{

    /**
     * {@inheritdoc}
     */
    protected $signature = 'ban-management:add-email 
    {email : Banned e-mail} 
    {--reason= : Reason of the banned email}
    {--expire= : Expiration date}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Add a new Banned Email.';

    /**
     * Banned emails processor instance.
     *
     * @var BannedEmailsProcessor
     */
    protected $bannedEmailsProcessor;

    /**
     * AddCommand constructor.
     * @param BannedEmailsProcessor $bannedEmailsProcessor
     */
    public function __construct(BannedEmailsProcessor $bannedEmailsProcessor)
    {
        parent::__construct();

        $this->bannedEmailsProcessor = $bannedEmailsProcessor;
    }

    /**
     * Handles the command.
     */
    public function handle()
    {
        $data = [
            'email'      => $this->argument('email'),
            'reason'     => $this->option('reason'),
            'expired_at' => $this->option('expire'),
        ];

        $this->bannedEmailsProcessor->store($this, $data);
    }

    /**
     * Validation error message for the storing rule.
     *
     * @param MessageBag $errors
     */
    public function createBannedEmailFailedValidation(MessageBag $errors)
    {
        $message = implode("\n\r", $errors->all());
        $this->error($message);
    }

    /**
     * Success message for the stored banned email.
     *
     * @param BannedEmail $bannedEmail
     */
    public function storeBannedEmailSuccess(BannedEmail $bannedEmail)
    {
        $this->info('Banned email has been added.');
    }

    /**
     * Error message for the storing banned email.
     *
     * @param array $errors
     */
    public function storeBannedEmailFailed(array $errors)
    {
        $message = trans('antares/ban_management::response.bannedemails.create.db-failed', $errors);

        $this->error($message);
    }

}
