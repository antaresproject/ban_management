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

namespace Antares\Modules\BanManagement\Processor;

use Antares\Modules\BanManagement\Validation\ValidationHelper;
use Antares\Foundation\Processor\Processor;
use Antares\Modules\BanManagement\Contracts\RuleListener;
use Antares\Modules\BanManagement\Contracts\RuleStoreListener;
use Antares\Modules\BanManagement\Contracts\RuleUpdateListener;
use Antares\Modules\BanManagement\Contracts\RulesRepositoryContract;
use Antares\Modules\BanManagement\Http\Presenters\RulesPresenter;
use Antares\Modules\BanManagement\Validation\RuleValidation;
use Antares\Modules\BanManagement\Model\Rule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Events\Dispatcher;
use Antares\Support\Collection;
use Exception;
use Log;

class RulesProcessor extends Processor
{

    /**
     * Rules repository instance.
     *
     * @var RulesRepositoryContract
     */
    protected $repository;

    /**
     * Rules presenter instance.
     *
     * @var RulesPresenter
     */
    protected $presenter;

    /**
     * Rule validation instance.
     *
     * @var RuleValidation
     */
    protected $validation;

    /**
     * Events dispatcher.
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * RulesProcessor constructor.
     * @param RulesRepositoryContract $repository
     * @param RulesPresenter $presenter
     * @param RuleValidation $validation
     * @param Dispatcher $dispatcher
     */
    public function __construct(RulesRepositoryContract $repository, RulesPresenter $presenter, RuleValidation $validation, Dispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->presenter  = $presenter;
        $this->validation = $validation;
        $this->dispatcher = $dispatcher;
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
     * Returns the array of all rules.
     *
     * @return Collection|\Antares\Modules\BanManagement\Contracts\RuleContract[]
     */
    public function getAll()
    {
        return $this->repository->all();
    }

    /**
     * @return \Antares\Modules\BanManagement\Contracts\RuleContract[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getWhitelist()
    {
        return $this->repository->getEnabledWhitelist();
    }

    /**
     * Creates form for a new rule.
     *
     * @param RuleListener $listener
     * @return mixed
     */
    public function create(RuleListener $listener)
    {
        $rule = new Rule();
        $form = $this->presenter->form($rule);

        return $listener->showCreateForm(compact('rule', 'form'));
    }

    /**
     * Creates form for the editing rule.
     *
     * @param RuleListener $listener
     * @param int $id
     * @return mixed
     */
    public function edit(RuleListener $listener, $id)
    {
        $rule = $this->repository->findById($id);

        if (!$rule) {
            return $listener->notFound($id);
        }

        $form = $this->presenter->form($rule);

        $this->dispatcher->fire('antares.form: ban_management.rules', [$rule, $form]);
        $this->dispatcher->fire('antares.form: foundation.ban_management.rules', [$rule, $form, 'foundation.ban_management']);

        return $listener->showEditForm(compact('rule', 'form'));
    }

    /**
     * Stores the rule in the repository.
     *
     * @param RuleStoreListener $listener
     * @param array $input
     * @param bool $canBanSelf
     * @return JsonResponse
     */
    public function store(RuleStoreListener $listener, array $input, $canBanSelf = false)
    {
        if (empty(array_get($input, 'expired_at'))) {
            $input['expired_at'] = null;
        }

        $rule = new Rule();
        $rule->fill($input);
        $form = $this->presenter->form($rule, $canBanSelf);

        $validationHelper = new ValidationHelper($form, $this->validation->on('create'), $input);

        if (!$validationHelper->isValid()) {
            return $listener->createRuleFailedValidation($validationHelper->getMessageBag());
        }
        try {
            $this->repository->store($rule);
            return $listener->storeRuleSuccess($rule);
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            return $listener->storeRuleFailed(['error' => $e->getMessage()]);
        }
    }

    /**
     * Updates the rule in the repository.
     *
     * @param RuleUpdateListener $listener
     * @param int $id
     * @param array $input
     * @return JsonResponse
     */
    public function update(RuleUpdateListener $listener, $id, array $input)
    {
        if (empty(array_get($input, 'expired_at'))) {
            $input['expired_at'] = null;
        }

        $rule = $this->repository->findById($id);
        $rule->fill($input);

        if (!$rule) {
            return $listener->notFound($id);
        }

        $form             = $this->presenter->form($rule);
        $validationHelper = new ValidationHelper($form, $this->validation->on('update'), $input);

        if (!$validationHelper->isValid()) {
            return $listener->updateRuleFailedValidation($validationHelper->getMessageBag(), $id);
        }
        try {
            $this->repository->update($rule);
            return $listener->updateRuleSuccess($rule);
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            return $listener->updateRuleFailed(['error' => $e->getMessage()]);
        }
    }

    /**
     * Removes the rule from the repository.
     *
     * @param RuleListener $listener
     * @param int $id
     * @return mixed
     */
    public function destroy(RuleListener $listener, $id)
    {
        $rule = $this->repository->findById($id);

        if (!$rule) {
            return $listener->notFound($id);
        }

        try {
            $this->fireEvent('deleting', [$rule]);
            $this->repository->delete($rule);
            $this->fireEvent('deleted', [$rule]);
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            return $listener->deleteRuleFailed(['error' => $e->getMessage()]);
        }

        return $listener->deleteRuleSuccess();
    }

    /**
     * Handles the store action for whitelist rules.
     *
     * @param array $rules
     */
    public function storeWhiteList(array $rules)
    {
        $storedRules   = $this->repository->getEnabledWhitelist();
        $rulesToUpdate = [];
        $rulesToRemove = [];

        foreach ($storedRules as $storedRule) {
            $rule = $storedRule->getValue();
            $key  = array_search($rule, $rules, true);

            if ($key === false) {
                $rulesToRemove[] = $rule;
            } else if (in_array($rule, $rules, true)) {
                $rulesToUpdate[] = $rule;

                unset($rules[$key]);
            }
        }

        $rulesToStore = $rules;

        if (count($rulesToUpdate)) {
            Rule::query()->whereIn('value', $rulesToUpdate)->update(['expired_at' => null, 'status' => 1]);
        }

        if (count($rulesToStore)) {
            $date = Carbon::now();

            $rulesToStore = array_map(function($value) use($date) {
                return [
                    'value'      => $value,
                    'trusted'    => true,
                    'user_id'    => auth()->user()->getAuthIdentifier(),
                    'created_at' => $date->toDateTimeString(),
                    'status'     => 1,
                ];
            }, $rulesToStore);

            Rule::query()->insert($rulesToStore);
        }

        if (count($rulesToRemove)) {
            Rule::query()->whereIn('value', $rulesToRemove)->delete();
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
        $this->dispatcher->fire("antares.{$type}: ban_management.rules", $parameters);
    }

}
