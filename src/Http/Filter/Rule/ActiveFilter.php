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

namespace Antares\BanManagement\Http\Filter\Rule;

use Yajra\Datatables\Contracts\DataTableScopeContract;
use Antares\Datatables\Filter\SelectFilter;
use Illuminate\Database\Query\Builder;

class ActiveFilter extends SelectFilter implements DataTableScopeContract
{

    /**
     * name of filter
     *
     * @var String 
     */
    protected $name = 'Active';

    /**
     * column to search
     *
     * @var String
     */
    protected $column = 'activity';

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
            0 => 'Disabled',
            1 => 'Enabled'
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

        return $builder->whereIn('enabled', $values);
    }

}
