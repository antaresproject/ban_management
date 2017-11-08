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

namespace Antares\Modules\BanManagement\Http\Breadcrumb;

use Antares\Breadcrumb\Navigation;
use Antares\Modules\BanManagement\Model\Rule;
use DaveJamesMiller\Breadcrumbs\Generator;

class RulesBreadcrumb extends Navigation
{

    /**
     * Base name for the component extension.
     *
     * @var string
     */
    protected static $name = 'ban_management';

    /**
     * Register the breadcrumb for the index page.
     *
     * @throws \DaveJamesMiller\Breadcrumbs\Exception
     */
    public function onIndex()
    {
        if (!$this->breadcrumbs->exists(self::$name)) {
            $this->breadcrumbs->register(self::$name, function(Generator $breadcrumbs) {
                $breadcrumbs->push(trans('antares/ban_management::messages.breadcrumbs.ban_management'), handles('antares::ban_management/rules/datatable'));
            });
        }
        if (!$this->breadcrumbs->exists(self::$name . '-rules')) {
            $this->breadcrumbs->register(self::$name . '-rules', function(Generator $breadcrumbs) {
                $breadcrumbs->parent(self::$name);
            });
        }

        $this->shareOnView(self::$name);
    }

    /**
     * Register the breadcrumb for the create/edit page.
     * 
     * @param Rule $rule
     * @throws \DaveJamesMiller\Breadcrumbs\Exception
     */
    public function onForm(Rule $rule)
    {
        $this->onIndex();

        $name = $rule->exists ? 'rule-' . $rule->getValue() : 'rule-add';
        if (!$this->breadcrumbs->exists($name)) {
            $this->breadcrumbs->register($name, function(Generator $breadcrumbs) use($rule) {
                $trans = 'antares/ban_management::messages.breadcrumbs';
                $name  = $rule->exists ? trans($trans . '.ip_edit', ['id' => $rule->id, 'ip' => $rule->getValue()]) : trans($trans . '.ip_create');
                $breadcrumbs->parent(self::$name . '-rules');
                $breadcrumbs->push($name);
            });
        }

        $this->shareOnView($name);
    }

}
