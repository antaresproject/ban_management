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

namespace Antares\Modules\BanManagement\Http\Filter;

use Illuminate\Http\Request;
use Closure;

class SearchQuery
{

    /**
     * Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * SearchQuery constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Returns the search query.
     *
     * @param null $default
     * @return string|mixed
     */
    public function getQuery($default = null)
    {
        $search = $this->request->get('search', $default);

        if (is_array($search) AND isset($search['value'])) {
            return $search['value'];
        }

        return $search;
    }

    /**
     * Checks if the query can be applied.
     *
     * @return bool
     */
    public function canApply()
    {
        $query = $this->getQuery();

        return !(is_null($query) OR strlen($query) === 0);
    }

    /**
     * Apply the search query to the given closure.
     *
     * @param Closure $closure
     * @return void
     */
    public function apply(Closure $closure)
    {
        if ($this->canApply()) {
            $keyword = $this->getQuery();
            $closure($keyword);
        }
    }

}
