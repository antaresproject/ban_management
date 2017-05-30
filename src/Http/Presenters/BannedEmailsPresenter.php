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
use Antares\Modules\BanManagement\Http\Breadcrumb\BannedEmailsBreadcrumb;
use Antares\Modules\BanManagement\Http\Form\BannedEmailForm;
use Antares\Modules\BanManagement\Http\DataTables\BannedEmailsDataTable;
use Antares\Modules\BanManagement\Model\BannedEmail;

class BannedEmailsPresenter extends Presenter
{

    /**
     * Breadcrumb instance.
     *
     * @var BannedEmailsBreadcrumb
     */
    protected $breadcrumb;

    /**
     * Datatable instance.
     *
     * @var BannedEmailsDataTable
     */
    protected $datatable;

    /**
     * Form instance.
     *
     * @var BannedEmailForm
     */
    protected $form;

    /**
     * BannedEmailsPresenter constructor.
     * @param BannedEmailsBreadcrumb $breadcrumb
     * @param BannedEmailsDataTable $datatable
     * @param BannedEmailForm $form
     */
    public function __construct(BannedEmailsBreadcrumb $breadcrumb, BannedEmailsDataTable $datatable, BannedEmailForm $form)
    {
        $this->breadcrumb = $breadcrumb;
        $this->datatable  = $datatable;
        $this->form       = $form;
    }

    /**
     * Returns the datatable of banned emails.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function table()
    {
        publish('ban_management', 'assets.scripts');

        return $this->datatable->render('antares/ban_management::admin.list');
    }

    /**
     * Returns the form for the given banned email model.
     *
     * @@param BannedEmail $bannedEmail
     * @return \Antares\Contracts\Html\Builder
     */
    public function form(BannedEmail $bannedEmail)
    {
        $this->breadcrumb->onForm($bannedEmail);
        return $this->form->build($this, $bannedEmail);
    }

}
