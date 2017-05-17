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

namespace Antares\Modules\BanManagement\Http\Controllers;

use Antares\Foundation\Http\Controllers\BaseController;
use Antares\Modules\BanManagement\Processor\BannedProcessor;
use Antares\Modules\BanManagement\Contracts\BannedListener;

class BannedController extends BaseController implements BannedListener
{

    /**
     * Banned processor instance.
     *
     * @var BannedProcessor
     */
    protected $bannedProcessor;

    /**
     * BannedController constructor.
     * @param BannedProcessor $bannedProcessor
     */
    public function __construct(BannedProcessor $bannedProcessor)
    {
        parent::__construct();
        $this->bannedProcessor = $bannedProcessor;
    }

    /**
     * Setup controller middleware.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        
    }

    /**
     * Returns the page about the banned action.
     *
     * @param string $reason
     * @return \Illuminate\Http\Response
     */
    public function index($reason)
    {
        return $this->bannedProcessor->handle($this, $reason);
    }

    /**
     * {@inheritdoc}
     */
    public function showInfoPage(array $data)
    {
        return response()->view('antares/ban_management::banned', $data, 403);
    }

}
