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

namespace Antares\BanManagement\Console\Commands\BannedEmails;

use Illuminate\Console\Command;
use Antares\BanManagement\Processor\BannedEmailsProcessor;
use Antares\BanManagement\Contracts\BannedEmailSyncListener;

class SyncCommand extends Command implements BannedEmailSyncListener
{

    /**
     * {@inheritdoc}
     */
    protected $signature = 'ban-management:sync-emails';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Synchronize of banned emails for the plain file.';

    /**
     * Banned emails processor instance.
     *
     * @var BannedEmailsProcessor
     */
    protected $bannedEmailsProcessor;

    /**
     * SyncCommand constructor.
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
        $this->bannedEmailsProcessor->sync($this);
    }

    /**
     * Success message for the sync action.
     *
     * @param string $msg
     */
    public function syncBannedEmailSuccess($msg)
    {
        $this->info($msg);
    }

    /**
     * Error message for the sync action.
     *
     * @param string $msg
     */
    public function syncBannedEmailFailed($msg)
    {
        $this->error($msg);
    }

}
