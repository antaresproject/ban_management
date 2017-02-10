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

namespace Antares\BanManagement\Services;

use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;

class CookieBanService
{

    /**
     * Cookie jar instance.
     *
     * @var CookieJar
     */
    protected $cookieJar;

    /**
     * Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * The name of the cookie.
     *
     * @var string
     */
    protected static $cookieName = 'ban';

    /**
     * CookieBanService constructor.
     * @param CookieJar $cookieJar
     * @param Request $request
     */
    public function __construct(CookieJar $cookieJar, Request $request)
    {
        $this->cookieJar = $cookieJar;
        $this->request   = $request;
    }

    /**
     * Adds the ban cookie.
     */
    public function add()
    {
        $this->cookieJar->forever(self::$cookieName, $this->request->getClientIp());
    }

    /**
     * Removes the ban cookie.
     */
    public function remove()
    {
        $this->cookieJar->forget(self::$cookieName);
    }

    /**
     * Check if the request has the cookie.
     * 
     * @return bool
     */
    public function has()
    {
        return $this->request->cookie(self::$cookieName) !== null;
    }

    /**
     * Check if the request has the cookie and its value has different IP.
     *
     * @return bool
     */
    public function hasDifferentIp()
    {
        if ($this->has()) {
            return $this->request->cookie(self::$cookieName) !== $this->request->getClientIp();
        }

        return false;
    }

}
