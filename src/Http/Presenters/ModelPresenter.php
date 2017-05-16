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

namespace Antares\Modules\BanManagement\Http\Presenters;

use Antares\Modules\BanManagement\Contracts\ExpirableContract;
use Carbon\Carbon;

class ModelPresenter
{

    /**
     * @var ExpirableContract
     */
    protected $model;

    /**
     * ModelPresenter constructor.
     * @param ExpirableContract $model
     */
    public function __construct(ExpirableContract $model)
    {
        $this->model = $model;
    }

    /**
     * Returns the formatted expiration date.
     *
     * @return string
     */
    public function getFormattedExpirationDate()
    {
        $date = $this->model->getExpirationDate();

        if ($date instanceof Carbon) {
            $isFuture   = $date->isFuture();
            $translated = $isFuture ? $date->diffForHumans(null, true) : $date->diffForHumans();

            if ($isFuture) {
                $translated = 'in ' . $translated;
            }

            return $translated;
        }

        return '---';
    }

}
