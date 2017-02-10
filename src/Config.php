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

namespace Antares\BanManagement;

use Illuminate\Container\Container;

class Config
{

    /**
     * Container instance
     *
     * @var Container
     */
    protected $container;

    /**
     * Extension's global options.
     *
     * @var array
     */
    protected $options;

    /**
     * Config constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->options   = (array) $this->container->make('antares.extension')->getExtensionOptions('ban_management');
    }

    /**
     * Returns the non-negative value of maximum failed attempts after which client's IP will be banned.
     *
     * @return int
     */
    public function getMaxFailedAttempts()
    {
        return (int) array_get($this->options, 'max_failed_attempts', 50);
    }

    /**
     * Returns the decay in minutes of attempts.
     *
     * @return int
     */
    public function getAttemptsDecay()
    {
        return (int) array_get($this->options, 'attempts_decay_minutes', 1440);
    }

    /**
     * Determines if the cookie tracking is enabled.
     *
     * @return bool
     */
    public function hasCookieTracking()
    {
        return (bool) array_get($this->options, 'cookie_tracking', false);
    }

}
