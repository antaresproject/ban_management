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

namespace Antares\Modules\BanManagement\Listeners;

use Antares\Modules\BanManagement\Processor\RulesProcessor;
use Antares\Modules\BanManagement\Validation\ConfigValidation;
use Antares\Foundation\Events\SecurityFormSubmitted;
use Illuminate\Container\Container;
use Antares\Model\Component;
use Exception;
use Log;

class ConfigStoreListener
{

    /**
     * Container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * Rules Processor instance.
     *
     * @var RulesProcessor
     */
    protected $rulesProcessor;

    /**
     * Config Validation instance.
     *
     * @var ConfigValidation
     */
    protected $configValidation;

    /**
     * ConfigStoreListener constructor.
     * @param Container $container
     * @param RulesProcessor $rulesProcessor
     * @param ConfigValidation $configValidation
     */
    public function __construct(Container $container, RulesProcessor $rulesProcessor, ConfigValidation $configValidation)
    {
        $this->container        = $container;
        $this->rulesProcessor   = $rulesProcessor;
        $this->configValidation = $configValidation;
    }

    /**
     * Handles the security form event.
     *
     * @param SecurityFormSubmitted $securityFormSubmitted
     */
    public function handle(SecurityFormSubmitted $securityFormSubmitted)
    {
        $data       = $securityFormSubmitted->request->get('ban-management', []);
        $validation = $this->configValidation->with($data);

        if ($validation->fails()) {
            return $securityFormSubmitted->listener->onValidationFailed($validation->getMessageBag());
        }

        try {
            $options     = (array) array_get($data, 'options', []);
            $ipWhitelist = array_get($data, 'rules.ip-whitelist', '');
            $rules       = array_filter(explode(",", $ipWhitelist));

            $this->storeExtensionOptions($options);
            $this->rulesProcessor->storeWhiteList($rules);

            return $securityFormSubmitted->listener->onSuccess(trans('antares/ban_management::response.config.success'));
        } catch (Exception $e) {
            Log::emergency($e->getMessage());
            return $securityFormSubmitted->listener->onFail($e->getMessage());
        }
    }

    /**
     * Store the array with global options of the extension.
     *
     * @param array $options
     */
    protected function storeExtensionOptions(array $options)
    {
        $component          = Component::query()->where('name', 'ban_management')->first();
        $component->options = array_merge($component->options, $options);
        $component->save();
    }

}
