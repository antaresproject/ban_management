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

namespace Antares\Modules\BanManagement\Http\Filter\Rule;

use Yajra\Datatables\Contracts\DataTableScopeContract;
use Antares\Datatables\Filter\SelectFilter;
use Illuminate\Database\Query\Builder;

class TrustedFilter extends SelectFilter implements DataTableScopeContract
{

    /**
     * name of filter
     *
     * @var String 
     */
    protected $name = 'Trusted';

    /**
     * column to search
     *
     * @var String
     */
    protected $column = 'trusted';

    /**
     * filter pattern
     *
     * @var String
     */
    protected $pattern = '%value';

    /**
     * filter instance dataprovider
     *
     * @return array
     */
    protected function options()
    {
        return [
            0 => 'Trusted',
            1 => 'Restricted'
        ];
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply($builder)
    {
        $values = $this->getValues();

        if (empty($values)) {
            return $builder;
        }

        return $builder->whereIn('trusted', $values);
    }

}
