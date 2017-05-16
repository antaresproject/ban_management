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

namespace Antares\Modules\BanManagement\Rules;

use M6Web\Component\Firewall\Entry\AbstractEntry;

class Hostname extends AbstractEntry
{

    /**
     * Regular expression for determining hostname.
     *
     * @var string
     */
    protected static $hostnameRegex = "/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/";

    /**
     * {@inheritdoc}
     */
    public static function match($entry)
    {
        return (bool) preg_match(self::$hostnameRegex, trim($entry));
    }

    /**
     * {@inheritdoc}
     */
    public function check($entry)
    {
        $checkRegex = $this->getTemplateRegexPattern();

        return (bool) preg_match($checkRegex, $entry);
    }

    /**
     * Returns the regular expression for a hostname template.
     *
     * @return string
     */
    protected function getTemplateRegexPattern()
    {
        $template = str_replace('.', '\.', $this->template);
        $template = str_replace('*', '[a-zA-Z0-9]*', $template);

        return sprintf('/^%s$/', $template);
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchingEntries()
    {
        return [$this->template];
    }

}
