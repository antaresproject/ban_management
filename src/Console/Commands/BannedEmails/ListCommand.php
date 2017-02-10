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

class ListCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected $signature = 'ban-management:emails';

    /**
     * {@inheritdoc}
     */
    protected $description = 'List all banned emails.';

    /**
     * Banned emails processor instance.
     *
     * @var BannedEmailsProcessor
     */
    protected $bannedEmailsProcessor;

    /**
     * ListCommand constructor.
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
        $headers = ['id', 'email', 'internal_reason', 'reason', 'created_at', 'updated_at', 'expired_at'];
        $rules   = $this->bannedEmailsProcessor->getAll()->toArray();

        $this->table($headers, $rules);
    }

}
