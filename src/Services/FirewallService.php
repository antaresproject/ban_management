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

namespace Antares\Modules\BanManagement\Services;

use Antares\Modules\BanManagement\Contracts\RuleContract;
use Antares\Modules\BanManagement\Collections\Rules;
use M6Web\Component\Firewall\Entry\EntryInterface;
use Symfony\Component\HttpFoundation\Request;
use M6Web\Component\Firewall\Firewall;
use M6Web\Component\Firewall\Entry\EntryFactory;
use M6Web\Component\Firewall\Lists\ListMerger;

class FirewallService
{

    /**
     * Rules collection instance.
     *
     * @var Rules
     */
    protected $rules;

    /**
     * Entry factory instance.
     *
     * @var EntryFactory
     */
    protected $entryFactory;

    /**
     * List merger instance.
     *
     * @var ListMerger
     */
    protected $listMerger;

    /**
     * Firewall instance.
     *
     * @var Firewall
     */
    protected $firewall;

    /**
     * Array of the matching entries.
     *
     * @var array
     */
    protected $matchingEntries;

    /**
     * FirewallService constructor.
     *
     * @param Rules $rules
     * @param EntryFactory $entryFactory
     * @param ListMerger $listMerger
     */
    public function __construct(Rules $rules, EntryFactory $entryFactory, ListMerger $listMerger)
    {
        $this->rules        = $rules;
        $this->entryFactory = $entryFactory;
        $this->listMerger   = $listMerger;

        $this->setupFirewall();
    }

    /**
     * Creates instance of Firewall with white and black lists.
     */
    protected function setupFirewall()
    {
        $this->firewall = new Firewall($this->entryFactory, $this->listMerger);
        $this->firewall->setDefaultState(true);
        $this->updateRules();
    }

    /**
     * Updates the rules.
     */
    public function updateRules()
    {
        $this->firewall
                ->addList(self::getValuesOfRules($this->rules->getWhiteList()), 'allowed', true)
                ->addList(self::getValuesOfRules($this->rules->getBlackList()), 'rejected', false);
    }

    /**
     * Checks if the provided IP address can be allowed.
     *
     * @param $ip
     * @return bool
     */
    public function isIpAllowed($ip)
    {
        $response = $this->firewall->setIpAddress($ip)->handle();
        $entry    = $this->entryFactory->getEntry($ip);

        if (!$response AND $entry instanceof EntryInterface) {
            $this->matchingEntries = $entry->getMatchingEntries();
        }

        return $response;
    }

    /**
     * Checks if the provided Request object can be allowed.
     *
     * @param Request $request
     * @return bool
     */
    public function isRequestAllowed(Request $request)
    {
        $ip = $request->getClientIp();
        return $this->isIpAllowed($ip);
    }

    /**
     * Returns the rules collection.
     *
     * @return Rules
     */
    public function & getRules()
    {
        return $this->rules;
    }

    /**
     * Returns matching entries after the validation.
     *
     * @return RuleContract | null
     */
    public function getMatchingRule()
    {
        $rules = $this->rules->getBlackList();

        foreach ($rules as $rule) {
            if (in_array($rule->getValue(), $this->matchingEntries, true)) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Returns an array with values of the given rules.
     *
     * @param array $rules
     * @return array
     */
    protected static function getValuesOfRules(array $rules)
    {
        return array_map(function(RuleContract $rule) {
            return $rule->getValue();
        }, $rules);
    }

}
