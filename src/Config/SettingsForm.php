<?php

declare(strict_types = 1);

namespace Antares\Modules\BanManagement\Config;

use Antares\Contracts\Html\Form\Fieldset;
use Antares\Extension\Contracts\Config\SettingsContract;
use Antares\Extension\Contracts\Config\SettingsFormContract;

class SettingsForm implements SettingsFormContract
{

    /**
     * Builds the content of the settings form.
     *
     * @param Fieldset $fieldset
     * @param SettingsContract $settings
     * @return mixed
     */
    public function build(Fieldset $fieldset, SettingsContract $settings)
    {
        $cookieTracking = $settings->getValueByName('cookie_tracking');

        $fieldset->control('input:hidden', 'cookie_tracking')->value(0);

        $fieldset->control('input:checkbox', 'cookie_tracking')
                ->value(1)
                ->attributes($cookieTracking ? ['checked' => 'checked'] : [])
                ->label('Cookie tracking');

        $fieldset->control('input:text', 'max_failed_attempts')
                ->value($settings->getValueByName('max_failed_attempts'))
                ->label('Max failed attempts');

        $fieldset->control('input:text', 'attempts_decay_minutes')
                ->value($settings->getValueByName('attempts_decay_minutes'))
                ->label('Attempts decay minutes');
    }

}
