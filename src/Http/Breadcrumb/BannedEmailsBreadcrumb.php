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
use Antares\Modules\BanManagement\Model\BannedEmail;
use DaveJamesMiller\Breadcrumbs\Generator;

class BannedEmailsBreadcrumb extends Navigation
{

    /**
     * Base name for the component breadcrumb.
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
        $this->breadcrumbs->register(self::$name, function(Generator $breadcrumbs) {
            $breadcrumbs->push(trans('antares/ban_management::messages.breadcrumbs.ban_management'), handles('antares::ban_management/bannedemails/datatable'));
        });

        $this->breadcrumbs->register(self::$name . '-bannedemails', function(Generator $breadcrumbs) {
            $breadcrumbs->parent(self::$name);
        });

        $this->shareOnView(self::$name);
    }

    /**
     * Register the breadcrumb for the create/edit page.
     * 
     * @param BannedEmail $bannedEmail
     * @throws \DaveJamesMiller\Breadcrumbs\Exception
     */
    public function onForm(BannedEmail $bannedEmail)
    {
        $this->onIndex();

        $name = $bannedEmail->exists ? 'rule-' . $bannedEmail->getEmail() : 'rule-add';

        $this->breadcrumbs->register($name, function(Generator $breadcrumbs) use($bannedEmail) {
            $trans = 'antares/ban_management::messages.breadcrumbs';
            $name  = $bannedEmail->exists ? trans($trans . '.banned_email_edit', ['id' => $bannedEmail->id, 'email' => $bannedEmail->getEmail()]) : trans($trans . '.banned_email_new');

            $breadcrumbs->parent(self::$name . '-bannedemails');
            $breadcrumbs->push($name);
        });

        $this->shareOnView($name);
    }

}
