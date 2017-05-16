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

namespace Antares\Modules\BanManagement\Http\Controllers\Admin;

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Modules\BanManagement\Contracts\RuleListener;
use Antares\Modules\BanManagement\Processor\RulesProcessor;
use Antares\Modules\BanManagement\Contracts\RuleContract;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class ConfigController extends AdminController implements RuleListener
{

    /**
     * Rules processor instance.
     *
     * @var RulesProcessor
     */
    protected $processor;

    /**
     * RulesController constructor.
     * @param RulesProcessor $processor
     */
    public function __construct(RulesProcessor $processor)
    {
        parent::__construct();

        $this->processor = $processor;
    }

    /**
     * Setup controller middleware.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');

        $this->middleware('antares.can:antares/ban_management::add-rule', ['only' => ['create', 'store'],]);
        $this->middleware('antares.can:antares/ban_management::list-rules', ['only' => ['show', 'index'],]);
        $this->middleware('antares.can:antares/ban_management::update-rule', ['only' => ['edit', 'update'],]);
        $this->middleware('antares.can:antares/ban_management::delete-rule', ['only' => ['delete', 'destroy'],]);
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
     * Returns the page with form for creating rule.
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
    public function createRuleFailedValidation(MessageBag $errors)
    {
        app('antares.messages')->add('error', $errors);
        $url = handles('ban_management.rules.create');

        return $this->redirectWithErrors($url, $errors);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function storeRuleSuccess(RuleContract $rule)
    {
        $url     = handles('antares::ban_management/rules/datatable');
        $message = trans('antares/ban_management::response.rules.create.success');

        return $this->redirectWithMessage($url, $message);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function storeRuleFailed(array $errors)
    {
        $url     = handles('antares::ban_management/rules/datatable');
        $message = trans('antares/ban_management::response.rules.create.db-failed', $errors);

        return $this->redirectWithMessage($url, $message, 'error');
    }

    /**
     * {@inheritdoc}
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCreateForm(array $data)
    {
        set_meta('title', trans('antares/ban_management::title.rules.create'));
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
     * Returns the page with form for editing rule.
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
        set_meta('title', trans('antares/ban_management::title.rules.edit'));
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
        $url     = handles('antares::ban_management/rules/datatable');
        $message = trans('antares/ban_management::response.rules.notexists');

        return $this->redirectWithMessage($url, $message);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function updateRuleFailedValidation($errors, $id)
    {
        $url = handles('antares::ban_management/rules/edit/' . $id);
        return $this->redirectWithErrors($url, $errors);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function updateRuleSuccess(RuleContract $rule)
    {
        $url     = handles('antares::ban_management/rules/datatable');
        $message = trans('antares/ban_management::response.rules.update.success');

        return $this->redirectWithMessage($url, $message);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function updateRuleFailed(array $errors)
    {
        $url     = handles('antares::ban_management/rules/datatable');
        $message = trans('antares/ban_management::response.rules.update.db-failed', $errors);

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
    public function deleteRuleSuccess()
    {
        $url     = handles('antares::ban_management/rules/datatable');
        $message = trans('antares/ban_management::response.rules.delete.success');

        return $this->redirectWithMessage($url, $message);
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function deleteRuleFailed(array $errors)
    {
        $url     = handles('antares::ban_management/rules/datatable');
        $message = trans('antares/ban_management::response.rules.delete.db-failed', $errors);

        return $this->redirectWithMessage($url, $message, 'error');
    }

}
