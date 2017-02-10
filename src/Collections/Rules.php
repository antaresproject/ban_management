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

namespace Antares\BanManagement\Collections;

use Antares\BanManagement\Contracts\RulesRepositoryContract;
use Antares\BanManagement\Contracts\RuleContract;
use Carbon\Carbon;

class Rules
{

    /**
     * Rules Repository
     *
     * @var RulesRepositoryContract.
     */
    protected $rulesRepository;

    /**
     * List of whitelisted entries.
     *
     * @var array
     */
    protected $whiteList = [];

    /**
     * List of blacklisted entries.
     *
     * @var array
     */
    protected $blackList = [];

    /**
     * For performance purpose. If true use already fetched rules.
     *
     * @var bool
     */
    protected $fetched = false;

    /**
     * Rules constructor.
     *
     * @param RulesRepositoryContract $rulesRepository
     */
    public function __construct(RulesRepositoryContract $rulesRepository)
    {
        $this->rulesRepository = $rulesRepository;
    }

    /**
     * Gets enabled rules from the repository.
     *
     * @return $this
     */
    protected function fetchIfNecessary()
    {
        if ($this->fetched === false) {
            $now   = Carbon::now();
            $rules = $this->rulesRepository->enabledUntilDate($now);
            foreach ($rules as $rule) {
                $this->add($rule);
            }

            $this->fetched = true;
        }

        return $this;
    }

    /**
     * Adds a new rule to the rules list.
     *
     * @param RuleContract $rule
     */
    public function add(RuleContract $rule)
    {
        if ($rule->isTrusted()) {
            $this->whiteList[] = $rule;
        } else {
            $this->blackList[] = $rule;
        }
    }

    /**
     * Returns a white list of rules.
     *
     * @return RuleContract[]
     */
    public function getWhiteList()
    {
        return $this->fetchIfNecessary()->whiteList;
    }

    /**
     * Returns a black list of rules.
     *
     * @return RuleContract[]
     */
    public function getBlackList()
    {
        return $this->fetchIfNecessary()->blackList;
    }

}
