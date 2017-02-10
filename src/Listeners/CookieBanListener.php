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

namespace Antares\BanManagement\Listeners;

use Antares\BanManagement\Services\CookieBanService;
use Antares\BanManagement\Events\Banned;

class CookieBanListener
{

    /**
     * Cookie Ban service instance.
     *
     * @var CookieBanService
     */
    protected $cookieBanService;

    /**
     * CookieBanListener constructor.
     * @param CookieBanService $cookieBanService
     */
    public function __construct(CookieBanService $cookieBanService)
    {
        $this->cookieBanService = $cookieBanService;
    }

    /**
     * Adds the cookie ban.
     * 
     * @param Banned $banned
     */
    public function handle(Banned $banned)
    {
        $this->cookieBanService->add();
    }

}
