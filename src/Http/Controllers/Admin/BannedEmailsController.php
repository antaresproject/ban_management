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

namespace Antares\Modules\BanManagement\Http\Controllers\Admin;

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Modules\BanManagement\Contracts\BannedEmailListener;
use Antares\Modules\BanManagement\Processor\BannedEmailsProcessor;
use Antares\Modules\BanManagement\Model\BannedEmail;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class BannedEmailsController extends AdminController implements BannedEmailListener
{

    /**
     * Banned emails processor instance.
     *
     * @var BannedEmailsProcessor
     */
    protected $processor;

    /*     * \
     * BannedEmailsController constructor.
     * @param BannedEmailsProcessor $processor
     */

    public function __construct(BannedEmailsProcessor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
        active_menu_route('ban_management/rules/datatable');
    }

    /**
     * Setup controller middleware.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');

        $this->middleware('antares.can:antares/ban_management::add-banned-email', ['only' => ['create', 'store'],]);
        $this->middleware('antares.can:antares/ban_management::list-banned-emails', ['only' => ['show', 'index'],]);
        $this->middleware('antares.can:antares/ban_management::update-banned-email', ['only' => ['edit', 'update'],]);
        $this->middleware('antares.can:antares/ban_management::delete-banned-email', ['only' => ['delete', 'destroy'],]);
    }

    /**
     * Returns the index page.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->index();
    }

    /**
     * {@inheritdoc}
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function datatable()
    {
        return $this->index();
    }

    /**
     * Returns the page with form for creating banned email.
     *
     * @return mixed
     */
    public function create()
    {
        return $this->processor->create($this);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function createBannedEmailFailedValidation(MessageBag $errors)
    {
        app('antares.messages')->add('error', $errors);
        $url = handles('ban_management.bannedemails.create');

        return $this->redirectWithErrors($url, $errors);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function storeBannedEmailSuccess(BannedEmail $bannedEmail)
    {

        $url     = handles('antares::ban_management/rules/datatable');
        $message = trans('antares/ban_management::response.bannedemails.create.success');

        return $this->redirectWithMessage($url, $message);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function storeBannedEmailFailed(array $errors)
    {
        $url     = handles('antares::ban_management/bannedemails/datatable');
        $message = trans('antares/ban_management::response.bannedemails.create.db-failed', $errors);

        return $this->redirectWithMessage($url, $message, 'error');
    }

    /**
     * {@inheritdoc}
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCreateForm(array $data)
    {
        set_meta('title', trans('antares/ban_management::title.bannedemails.create'));
        return view('antares/ban_management::admin.edit', $data);
    }

    /**
     * Handles the store action.
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $data = $request->all();

        return $this->processor->store($this, $data);
    }

    /**
     * Returns the page with form for editing banned email.
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id)
    {
        return $this->processor->edit($this, $id);
    }

    /**
     * {@inheritdoc}
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showEditForm(array $data)
    {
        set_meta('title', trans('antares/ban_management::title.bannedemails.edit'));
        return view('antares/ban_management::admin.edit', $data);
    }

    /**
     * Handles the update action.
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        return $this->processor->update($this, $id, $data);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function notFound($id)
    {
        $url     = handles('antares::ban_management/bannedemails/datatable');
        $message = trans('antares/ban_management::response.bannedemails.notexists');

        return $this->redirectWithMessage($url, $message);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function updateBannedEmailFailedValidation($errors, $id)
    {
        $url = handles('antares::ban_management/bannedemails/edit/' . $id);
        return $this->redirectWithErrors($url, $errors);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function updateBannedEmailSuccess(BannedEmail $bannedEmail)
    {
        $url     = handles('antares::ban_management/bannedemails/datatable');
        $message = trans('antares/ban_management::response.bannedemails.update.success');

        return $this->redirectWithMessage($url, $message);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function updateBannedEmailFailed(array $errors)
    {
        $url     = handles('antares::ban_management/bannedemails/datatable');
        $message = trans('antares/ban_management::response.bannedemails.update.db-failed', $errors);

        return $this->redirectWithMessage($url, $message, 'error');
    }

    /**
     * Handles the destroy action.
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        return $this->processor->destroy($this, $id);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function deleteBannedEmailSuccess()
    {
        $url     = handles('antares::ban_management/bannedemails/datatable');
        $message = trans('antares/ban_management::response.bannedemails.delete.success');

        return $this->redirectWithMessage($url, $message);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function deleteBannedEmailFailed(array $errors)
    {
        $url     = handles('antares::ban_management/bannedemails/datatable');
        $message = trans('antares/ban_management::response.bannedemails.delete.db-failed', $errors);

        return $this->redirectWithMessage($url, $message, 'error');
    }

}
