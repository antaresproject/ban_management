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

namespace Antares\Modules\BanManagement\Http\DataTables;

use Antares\Modules\BanManagement\Contracts\RulesRepositoryContract;
use Antares\Modules\BanManagement\Http\Filter\SearchQuery;
use Antares\Datatables\Services\DataTable;
use Antares\Modules\BanManagement\Model\Rule;
use Carbon\Carbon;
use Closure;
use Form;

class RulesDataTable extends DataTable
{

    /**
     * container with filters
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Returns query builder for the resource.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = app()->make(RulesRepositoryContract::class)->datatable([
            'tbl_ban_management_rules.id',
            'tbl_ban_management_rules.user_id',
            'tbl_ban_management_rules.internal_reason',
            'tbl_ban_management_rules.value',
            'tbl_ban_management_rules.enabled',
            'tbl_ban_management_rules.trusted',
            'tbl_ban_management_rules.created_at',
            'tbl_ban_management_rules.expired_at'
        ]);

        if (request()->ajax()) {
            $columns = request()->get('columns', []);
            $all     = array_where($columns, function($item, $index) {
                return array_get($item, 'data') === 'status' AND array_get($item, 'search.value') === 'all';
            });

            if (!empty($all)) {
                $query->whereIn('tbl_ban_management_rules.status', [0, 1]);
            }
        }

        if (($where = $this->getDefaultWhere($query)) !== false) {
            $query->where($where);
        }

        return $query;
    }

    /**
     * Default ordering
     *
     * @return array
     */
    protected function getDefaultWhere($query)
    {
        if (!request()->ajax()) {
            $query->whereRaw('(tbl_ban_management_rules.expired_at > "' . date('Y-m-d H:i:s', time()) . '")');
            return false;
        } else {
            $columns = request()->get('columns', []);
            $found   = array_where($columns, function($item, $index) {
                return strlen(array_get($item, 'search.value')) > 0;
            });
            if (empty($found)) {
                return ['tbl_ban_management_rules.status' => 1];
            }
        }
        return false;
    }

    /**
     * Prepares the datatable data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        $acl       = app('antares.acl')->make('antares/ban_management');
        $canUpdate = $acl->can('update-rule');
        $canDelete = $acl->can('delete-rule');

        $today = Carbon::today();

        /* @var $searchQuery SearchQuery */
        $searchQuery = app()->make(SearchQuery::class);

        return $this->prepare()
                        ->filter(function($query) use($searchQuery) {
                            $searchQuery->apply(function($keyword) use($query) {
                                $query->leftJoin('tbl_users', 'tbl_ban_management_rules.user_id', '=', 'tbl_users.id');
                                $query->where(function($q) use($keyword) {
                                    $q->where('tbl_ban_management_rules.value', 'like', "%$keyword%");
                                    $q->orWhere('tbl_ban_management_rules.created_at', 'like', "%$keyword%");
                                    $q->orWhere('tbl_ban_management_rules.expired_at', 'like', "%$keyword%");
                                    $q->orWhere('tbl_ban_management_rules.internal_reason', 'like', "%$keyword%");
                                    $q->orWhere('tbl_users.firstname', 'like', "%$keyword%");
                                    $q->orWhere('tbl_users.lastname', 'like', "%$keyword%");
                                });
                            });
                        })
                        ->filterColumn('status', function($query, $keyword) {
                            $value = null;
                            switch ($keyword) {
                                case 'expired':
                                    $query->whereRaw('(tbl_ban_management_rules.expired_at < "' . date('Y-m-d H:i:s', time()) . '")');
                                    break;
                                case 'active':
                                    $query->whereRaw('(tbl_ban_management_rules.expired_at > "' . date('Y-m-d H:i:s', time()) . '")');
                                    break;
                            }
                        })
                        ->editColumn('enabled', function (Rule $rule) {
                            return $rule->isEnabled() ? '<span class="label-basic label-basic--success">YES</span>' : '<span class="label-basic label-basic--danger">NO</span>';
                        })
                        ->editColumn('trusted', function (Rule $rule) {
                            return $rule->isTrusted() ? '<span class="label-basic label-basic--success">YES</span>' : '<span class="label-basic label-basic--danger">NO</span>';
                        })
                        ->editColumn('user_id', function (Rule $rule) {
                            return $rule->user ? $rule->user->fullname : '---';
                        })
                        ->editColumn('internal_reason', function (Rule $rule) {
                            return strlen($rule->getInternalReason()) <= 0 ? '---' : $rule->getInternalReason();
                        })
                        ->editColumn('created_at', function (Rule $rule) {
                            return format_x_days($rule->getCreationDate());
                        })
                        ->editColumn('expired_at', function (Rule $rule) {
                            return $rule->getPresenter()->getFormattedExpirationDate();
                        })
                        ->editColumn('status', function (Rule $rule) use($today) {
                            if (!$rule->isEnabled()) {
                                return '<span class="label-basic label-basic--danger">' . trans('antares/ban_management::datagrid.header.disabled') . '</span>';
                            }

                            return $rule->isActive($today) ? '<span class="label-basic label-basic--success">' . trans('antares/ban_management::datagrid.header.active') . '</span>' : '<span class="label-basic label-basic--danger">' . trans('antares/ban_management::datagrid.header.expired') . '</span>';
                        })
                        ->addColumn('action', $this->getActionsColumn($canUpdate, $canDelete))
                        ->make(true);
    }

    /**
     * Returns the Datatable Builder.
     *
     * @return \Antares\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->setName('Rules List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('antares/ban_management::datagrid.header.id')])
                        ->addColumn(['data' => 'value', 'name' => 'value', 'title' => trans('antares/ban_management::datagrid.header.value'), 'className' => 'bolded'])
                        ->addColumn(['data' => 'user_id', 'name' => 'user_id', 'title' => trans('antares/ban_management::datagrid.header.author')])
                        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => trans('antares/ban_management::datagrid.header.status')])
                        ->addColumn(['data' => 'internal_reason', 'name' => 'internal_reason', 'title' => trans('antares/ban_management::datagrid.header.note')])
                        ->addColumn(['data' => 'trusted', 'name' => 'trusted', 'title' => trans('antares/ban_management::datagrid.header.trusted')])
                        ->addColumn(['data' => 'enabled', 'name' => 'enabled', 'title' => trans('antares/ban_management::datagrid.header.enabled')])
                        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => trans('antares/ban_management::datagrid.header.created_at')])
                        ->addColumn(['data' => 'expired_at', 'name' => 'expired_at', 'title' => trans('antares/ban_management::datagrid.header.expired_at')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions'])
                        ->setDeferedData()
                        ->addGroupSelect($this->statuses(), 3, 'active', [
                            'data-prefix'            => '',
                            'data-selectAR--mdl-big' => "true",
                            'data-column'            => 3,
                            'class'                  => 'ban_management-select-status mr24 select2--prefix',
        ]);
    }

    /**
     * Creates select for statuses
     *
     * @return array
     */
    protected function statuses()
    {
        return [
            'all'     => trans('antares/ban_management::statuses.all'),
            'active'  => trans('antares/ban_management::statuses.active'),
            'expired' => trans('antares/ban_management::statuses.expired'),
        ];
    }

    /**
     * Returns an action column for each table row.
     *
     * @param $canUpdate
     * @param $canDelete
     * @return Closure
     */
    protected function getActionsColumn($canUpdate, $canDelete)
    {
        return function ($row) use($canUpdate, $canDelete) {
            $btns = [];
            $html = app('html');

            if ($canUpdate) {
                $url    = handles('antares::ban_management/rules/' . $row->id . '/edit');
                $btns[] = $html->create('li', $html->link($url, trans('antares/ban_management::label.rule.edit'), ['data-icon' => 'edit']));
            }
            if ($canDelete) {
                $url    = handles('antares::ban_management/rules/' . $row->id . '/delete');
                $btns[] = $html->create('li', $html->link($url, trans('antares/ban_management::label.rule.delete'), [
                            'data-icon'        => 'delete',
                            'class'            => 'triggerable confirm',
                            'data-title'       => trans('Are you sure?'),
                            'data-http-method' => 'DELETE',
                            'data-description' => trans('Deleting rule') . ' ' . $row->value
                ]));
            }
            if (empty($btns)) {
                return '';
            }

            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}
