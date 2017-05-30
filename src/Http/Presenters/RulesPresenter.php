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

namespace Antares\Modules\BanManagement\Http\Presenters;

use Antares\Foundation\Http\Presenters\Presenter;
use Antares\Modules\BanManagement\Http\Breadcrumb\RulesBreadcrumb;
use Antares\Modules\BanManagement\Http\Form\RuleForm;
use Antares\Modules\BanManagement\Http\DataTables\RulesDataTable;
use Antares\Modules\BanManagement\Model\Rule;

class RulesPresenter extends Presenter
{

    /**
     * Breadcrumb instance.
     *
     * @var RulesBreadcrumb
     */
    protected $breadcrumb;

    /**
     * Datatable instance.
     *
     * @var RulesDataTable
     */
    protected $datatable;

    /**
     * Form instance.
     *
     * @var RuleForm
     */
    protected $form;

    /**
     * RulesPresenter constructor.
     * @param RulesBreadcrumb $breadcrumb
     * @param RulesDataTable $datatable
     * @param RuleForm $form
     */
    public function __construct(RulesBreadcrumb $breadcrumb, RulesDataTable $datatable, RuleForm $form)
    {
        $this->breadcrumb = $breadcrumb;
        $this->datatable  = $datatable;
        $this->form       = $form;
    }

    /**
     * Returns the datatable of rules.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function table()
    {
        publish('ban_management', 'assets.scripts');


        return $this->datatable->render('antares/ban_management::admin.list');
    }

    /**
     * Returns the form for the given rule model.
     * 
     * @param Rule $rule
     * @param boolean $canBanSelf
     * @return \Antares\Contracts\Html\Builder
     */
    public function form(Rule $rule, $canBanSelf = false)
    {
        $this->breadcrumb->onForm($rule);
        return $this->form->canBanSelf($canBanSelf)->build($this, $rule);
    }

}
