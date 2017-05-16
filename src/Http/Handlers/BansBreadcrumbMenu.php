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

namespace Antares\Modules\BanManagement\Http\Handlers;

use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Authorization\Authorization;

class BansBreadcrumbMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'       => 'bans',
        'position' => '*',
        'title'    => 'Ban Management',
        'link'     => '#',
        'icon'     => 'zmdi-shield-security',
    ];

    /**
     * Get the title.
     * @param  string  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        return $this->container->make('translator')->trans($value);
    }

    /**
     * Get position.
     *
     * @return string
     */
    public function getPositionAttribute()
    {
        return $this->handler->has('settings.customfields') ? '^:logger' : '>:home';
    }

    /**
     * Check authorization to display the menu.
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return app('antares.acl')->make('antares/ban_management')->can('list-rules');
    }

    /**
     * Create a handler.
     * @return void
     */
    public function handle()
    {
        if (!$this->passesAuthorization()) {
            return;
        }

        $acl = $this->container->make('antares.acl')->make('antares/ban_management');
        $this->createMenu();
        if ($acl->can('add-rule')) {
            $this->createAddRuleHandler();
        }
        if ($acl->can('add-banned-email')) {
            $this->createAddBannedEmailHandler();
        }
    }

    /**
     * Create a handler routing for creating a new rule.
     */
    protected function createAddRuleHandler()
    {
        $this->handler
                ->add('rule-add', '^:bans')
                ->title('Add new IP rule')
                ->icon('zmdi-plus-circle')
                ->link(handles('antares::ban_management/rules/create'));
    }

    /**
     * Create a handler routing for creating a new banned email.
     */
    protected function createAddBannedEmailHandler()
    {
        $this->handler
                ->add('banned-email-add', '^:bans')
                ->title('Add new banned email')
                ->icon('zmdi-plus-circle')
                ->link(handles('antares::ban_management/bannedemails/create'));
    }

}
