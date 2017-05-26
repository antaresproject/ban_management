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

namespace Antares\Modules\BanManagement\Processor;

use Antares\Foundation\Processor\Processor;
use Antares\Modules\BanManagement\Contracts\BannedEmailListener;
use Antares\Modules\BanManagement\Contracts\BannedEmailStoreListener;
use Antares\Modules\BanManagement\Contracts\BannedEmailUpdateListener;
use Antares\Modules\BanManagement\Contracts\BannedEmailsRepositoryContract;
use Antares\Modules\BanManagement\Contracts\BannedEmailSyncListener;
use Antares\Modules\BanManagement\Http\Presenters\BannedEmailsPresenter;
use Antares\Modules\BanManagement\Validation\BannedEmailValidation;
use Antares\Modules\BanManagement\Model\BannedEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Events\Dispatcher;
use Antares\Support\Collection;
use Exception;
use Log;

class BannedEmailsProcessor extends Processor
{

    /**
     * Banned emails repository instance.
     *
     * @var BannedEmailsRepositoryContract
     */
    protected $repository;

    /**
     * Banned emails presenter instance.
     *
     * @var BannedEmailsPresenter
     */
    protected $presenter;

    /**
     * Banned emails validation instance.
     *
     * @var BannedEmailValidation
     */
    protected $validation;

    /**
     * Events dispatcher.
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Banned email service instance.
     *
     * @var BannedEmailService
     */
    protected $bannedEmailService;

    /**
     * BannedEmailsProcessor constructor.
     * @param BannedEmailsRepositoryContract $repository
     * @param BannedEmailsPresenter $presenter
     * @param BannedEmailValidation $validation
     * @param Dispatcher $dispatcher
     */
    public function __construct(BannedEmailsRepositoryContract $repository, BannedEmailsPresenter $presenter, BannedEmailValidation $validation, Dispatcher $dispatcher)
    {
        $this->repository         = $repository;
        $this->presenter          = $presenter;
        $this->validation         = $validation;
        $this->dispatcher         = $dispatcher;
        $this->bannedEmailService = app(\Antares\Modules\BanManagement\Services\BannedEmailService::class);
    }

    /**
     * Returns the view for datatable.
     *
     * @return JsonResponse|\Illuminate\View\View
     */
    public function index()
    {
        return $this->presenter->table();
    }

    /**
     * Returns the array of all banned emails.
     *
     * @return Collection|\Antares\Modules\BanManagement\Model\BannedEmail[]
     */
    public function getAll()
    {
        return $this->repository->all();
    }

    /**
     * Creates form for a new banned email.
     *
     * @param BannedEmailListener $listener
     * @return mixed
     */
    public function create(BannedEmailListener $listener)
    {
        $bannedEmail = new BannedEmail();
        $form        = $this->presenter->form($bannedEmail);

        return $listener->showCreateForm(compact('bannedEmail', 'form'));
    }

    /**
     * Creates form for the editing banned email.
     *
     * @param BannedEmailListener $listener
     * @param int $id
     * @return mixed
     */
    public function edit(BannedEmailListener $listener, $id)
    {
        $bannedEmail = $this->repository->findById($id);

        if (!$bannedEmail) {
            return $listener->notFound($id);
        }

        $form = $this->presenter->form($bannedEmail);

        $this->dispatcher->fire('antares.form: ban_management.bannedemails', [$bannedEmail, $form]);
        $this->dispatcher->fire('antares.form: foundation.ban_management.bannedemails', [$bannedEmail, $form, 'foundation.ban_management']);

        return $listener->showEditForm(compact('bannedEmail', 'form'));
    }

    /**
     * Stores the banned email in the repository.
     *
     * @param BannedEmailStoreListener $listener
     * @param array $input
     * @return JsonResponse
     */
    public function store(BannedEmailStoreListener $listener, array $input)
    {
        if (empty(array_get($input, 'expired_at'))) {
            $input['expired_at'] = null;
        }

        $bannedEmail = new BannedEmail();
        $bannedEmail->fill($input);

        $form = $this->presenter->form($bannedEmail);

        if (!$form->isValid()) {
            return $listener->createBannedEmailFailedValidation($form->getMessageBag());
        }
        try {
            $this->repository->store($bannedEmail);
            $this->bannedEmailService->saveToFile();
            return $listener->storeBannedEmailSuccess($bannedEmail);
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            return $listener->storeBannedEmailFailed(['error' => $e->getMessage()]);
        }
    }

    /**
     * Updates the banned email in the repository.
     *
     * @param BannedEmailUpdateListener $listener
     * @param int $id
     * @param array $input
     * @return JsonResponse
     */
    public function update(BannedEmailUpdateListener $listener, $id, array $input)
    {
        if (empty(array_get($input, 'expired_at'))) {
            $input['expired_at'] = null;
        }

        $bannedEmail = $this->repository->findById($id);
        if (!$bannedEmail) {
            return $listener->notFound($id);
        }
        $bannedEmail->fill($input);
        $form = $this->presenter->form($bannedEmail);
        if (!$form->isValid()) {
            return $listener->updateBannedEmailFailedValidation($form->getMessageBag(), $id);
        }
        try {
            $this->repository->update($bannedEmail);
            $this->bannedEmailService->saveToFile();
            return $listener->updateBannedEmailSuccess($bannedEmail);
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            return $listener->updateBannedEmailFailed(['error' => $e->getMessage()]);
        }
    }

    /**
     * Removes the banned email from the repository.
     *
     * @param BannedEmailListener $listener
     * @param int $id
     * @return mixed
     */
    public function destroy(BannedEmailListener $listener, $id)
    {
        $bannedEmail = $this->repository->findById($id);

        if (!$bannedEmail) {
            return $listener->notFound($id);
        }

        try {
            $this->fireEvent('deleting', [$bannedEmail]);
            $this->repository->delete($bannedEmail);
            $this->bannedEmailService->saveToFile();
            $this->fireEvent('deleted', [$bannedEmail]);
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            return $listener->deleteBannedEmailFailed(['error' => $e->getMessage()]);
        }

        return $listener->deleteBannedEmailSuccess();
    }

    /**
     * Starts synchronization for banned emails.
     *
     * @param BannedEmailSyncListener $listener
     * @return mixed
     */
    public function sync(BannedEmailSyncListener $listener)
    {
        try {
            $this->bannedEmailService->saveToFile();

            return $listener->syncBannedEmailSuccess(trans('antares/ban_management::response.bannedemails.sync.success'));
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            return $listener->syncBannedEmailFailed($e->getMessage());
        }
    }

    /**
     * Fire Event related to eloquent process.
     *
     * @param  string  $type
     * @param  array   $parameters
     * @return void
     */
    protected function fireEvent($type, array $parameters = [])
    {
        $this->dispatcher->fire("antares.{$type}: ban_management:bannedemails", $parameters);
    }

}
