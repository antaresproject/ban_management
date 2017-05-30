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

namespace Antares\Modules\BanManagement\Http\Placeholder;

use Antares\Foundation\Http\Composers\LeftPane;

class BansPlaceholder extends LeftPane
{

    /**
     * Handle pane for dashboard page.
     *
     * @param string|null $name
     * @param array $options
     */
    public function compose($name = NULL, $options = array())
    {
        $menu = $this->widget->make('menu.bans.pane');

        $menu->add('bans-banned-rules')
                ->link(handles('antares::ban_management/rules/datatable'))
                ->title(trans('IPs'))
                ->icon('zmdi-shield-check');

        $menu->add('bans-banned-emails')
                ->link(handles('antares::ban_management/bannedemails/datatable'))
                ->title(trans('Banned Emails'))
                ->icon('zmdi-lock');

        $this->widget->make('pane.left')->add('bans')->content(view('antares/foundation::components.placeholder_left')->with('menu', $menu));
    }

}
