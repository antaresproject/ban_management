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

use Antares\Modules\BanManagement\Repositories\RulesRepository;
use Antares\Modules\BanManagement\Model\Rule;

class DDoSService
{

    /**
     * Configuration container
     *
     * @var array
     */
    protected $config;

    /**
     * RulesRepository
     *
     * @var RulesRepository 
     */
    protected $repository;

    /**
     * Construct
     * 
     * @param RulesRepository $repository
     */
    public function __construct(RulesRepository $repository)
    {
        $this->config     = config('antares/ban_management::ddos');
        $this->repository = $repository;
    }

    /**
     * Runs protection
     * 
     * @return mixed
     */
    public function run()
    {
        if (request()->ajax() or array_get($this->config, 'enabled', false) === false) {
            return $this;
        }
        if (!isset($_SESSION) && php_sapi_name() !== 'cli') {
            @session_start();
        }
        $interval = array_get($this->config, 'interval', 5);
        if (isset($_SESSION['last_request_count']) && $_SESSION['last_session_request'] > (time() - $interval)) {
            if (empty($_SESSION['last_request_count'])) {
                $_SESSION['last_request_count'] = 1;
            } elseif ($_SESSION['last_request_count'] < $interval) {
                $_SESSION['last_request_count'] = $_SESSION['last_request_count'] + 1;
            } elseif ($_SESSION['last_request_count'] >= $interval) {
                $this->ban();
                echo response('', 302)->header('Location', array_get($this->config, 'redirect', '/index.html'))->send();
                return app()->abort(302);
            }
        } else {
            $_SESSION['last_request_count'] = 1;
        }
        $_SESSION['last_session_request'] = time();
        return $this;
    }

    /**
     * Bans when too many requests per second
     * 
     * @return mixed
     */
    protected function ban()
    {
        $rule             = Rule::firstOrNew([
                    'value'           => request()->getClientIp(),
                    'trusted'         => 0,
                    'internal_reason' => trans('antares/ban_management::messages.too_many_requests'),
                    'reason'          => trans('antares/ban_management::messages.too_many_requests'),
        ]);
        $rule->expired_at = time() + array_get($this->config, 'enable_after', 60);
        $rule->status     = 1;
        $rule->enabled    = 1;
        return $this->repository->store($rule);
    }

}
