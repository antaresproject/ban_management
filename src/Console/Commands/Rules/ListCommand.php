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

namespace Antares\Modules\BanManagement\Console\Commands\Rules;

use Illuminate\Console\Command;
use Antares\Modules\BanManagement\Processor\RulesProcessor;

class ListCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected $signature = 'ban-management:rules';

    /**
     * {@inheritdoc}
     */
    protected $description = 'List all rules.';

    /**
     * Rules processor instance.
     *
     * @var RulesProcessor
     */
    protected $rulesProcessor;

    /**
     * ListCommand constructor.
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
        $headers = ['id', 'value', 'enabled', 'trusted', 'internal_reason', 'reason', 'created_at', 'updated_at', 'expired_at'];
        $rules   = $this->rulesProcessor->getAll()->toArray();

        $this->table($headers, $rules);
    }

}
