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

namespace Antares\Modules\BanManagement;

use Illuminate\Container\Container;
use Antares\Extension\Contracts\Config\SettingsContract;

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
     * @var SettingsContract
     */
    protected $options;

    /**
     * Config constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->options   = $this->container->make('antares.extension')->getSettings('antaresproject/module-ban_management');
    }

    /**
     * Returns the non-negative value of maximum failed attempts after which client's IP will be banned.
     *
     * @return int
     */
    public function getMaxFailedAttempts()
    {
        return (int) !is_null($this->options) ? $this->options->getValueByName('max_failed_attempts', 5) : 5;
    }

    /**
     * Returns the decay in minutes of attempts.
     *
     * @return int
     */
    public function getAttemptsDecay()
    {
        return (int) !is_null($this->options) ? $this->options->getValueByName('attempts_decay_minutes', 1440) : 1440;
    }

    /**
     * Determines if the cookie tracking is enabled.
     *
     * @return bool
     */
    public function hasCookieTracking()
    {

        return (bool) !is_null($this->options) ? $this->options->getValueByName('cookie_tracking', false) : false;
    }

}
