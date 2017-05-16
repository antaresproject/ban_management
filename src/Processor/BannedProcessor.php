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

namespace Antares\Modules\BanManagement\Processor;

use Antares\Modules\BanManagement\Contracts\BannedListener;

class BannedProcessor
{

    /**
     * Returns the listener page about the ban reason.
     *
     * @param BannedListener $listener
     * @param string $reason
     * @return mixed
     */
    public function handle(BannedListener $listener, $reason)
    {
        $data = [
            'info'        => trans('antares/ban_management::response.banned.info'),
            'reasonLabel' => trans('antares/ban_management::response.banned.reasonLabel'),
            'reason'      => $reason,
        ];

        return $listener->showInfoPage($data);
    }

}
