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

namespace Antares\Modules\BanManagement\Validation;

use Antares\Modules\BanManagement\Model\PlainRule;
use Illuminate\Http\Request;
use Antares\Contracts\Auth\Guard;
use Antares\Modules\BanManagement\Services\FirewallService;
use Antares\Modules\BanManagement\Services\BannedEmailService;
use Antares\Modules\BanManagement\Rules\Email;

class CustomRules
{

    /**
     * Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Guard instance.
     *
     * @var Guard
     */
    protected $guard;

    /**
     * Firewall service instance.
     *
     * @var FirewallService
     */
    protected $firewallService;

    /**
     * Banned emails service instance.
     *
     * @var BannedEmailService
     */
    protected $bannedEmailService;

    /**
     * CustomRules constructor.
     * @param Request $request
     * @param Guard $guard
     * @param FirewallService $firewallService
     * @param BannedEmailService $bannedEmailService
     */
    public function __construct(Request $request, Guard $guard, FirewallService $firewallService, BannedEmailService $bannedEmailService)
    {
        $this->request            = $request;
        $this->guard              = $guard;
        $this->firewallService    = $firewallService;
        $this->bannedEmailService = $bannedEmailService;
    }

    /**
     * Rule to check if the submitter IP is not the same as the given one.
     *
     * @param $field
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function notSubmitterIpHostname($field, $value, $parameters)
    {
        $this->firewallService->getRules()->add(new PlainRule($value));
        $this->firewallService->updateRules();

        return $this->firewallService->isIpAllowed($this->request->getClientIp());
    }

    /**
     * Rule to check if the submitter e-mail address is not the same as the given one.
     *
     * @param $field
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function notSubmitterEmail($field, $value, $parameters)
    {
        if ($this->guard->guest()) {
            return true;
        }

        $userEmail = $this->guard->user()->email;
        $emails    = $this->bannedEmailService->getEmailTemplatesFromRepository()->push($value);

        foreach ($emails->all() as $emailTemplate) {
            $emailRule = new Email($emailTemplate);

            if ($emailRule->check($userEmail)) {
                return false;
            }
        }

        return true;
    }

}
