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

class Email extends AbstractEntry
{

    /**
     * Regular expression for email part before @ sign.
     *
     * @var string
     */
    protected static $nameRegex = '[a-zA-Z0-9._%+-]+';

    /**
     * Regular expression for email part after @ sign and before TLD.
     *
     * @var string
     */
    protected static $domainSecondTLDRegex = '[a-zA-Z0-9.-]+';

    /**
     * Regular expression for email TLD.
     *
     * @var string
     */
    protected static $domainTLDRegex = '[a-zA-Z]{2,6}';

    /**
     * Returns the regular expression for an email.
     *
     * @return string
     */
    protected static function getRegexPattern()
    {
        return sprintf("/^(%s@%s\.%s)$/", self::$nameRegex, self::$domainSecondTLDRegex, self::$domainTLDRegex);
    }

    /**
     * {@inheritdoc}
     */
    public static function match($entry)
    {
        return (bool) preg_match(self::getRegexPattern(), trim($entry));
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
     * Returns the regular expression for an email template.
     *
     * @return string
     */
    protected function getTemplateRegexPattern()
    {
        $parts = explode('@', $this->template);
        if (count($parts) === 2) {
            $parts[0]    = str_replace('*', self::$nameRegex, $parts[0]);
            $topTLDIndex = strrpos($parts[1], '.');


            if ($topTLDIndex !== FALSE) {
                $secondTLD = substr($parts[1], 0, $topTLDIndex);
                $secondTLD = str_replace('*', self::$domainSecondTLDRegex, $secondTLD);

                $topTLD   = substr($parts[1], $topTLDIndex);
                $topTLD   = str_replace('*', self::$domainTLDRegex, $topTLD);
                $template = $parts[0] . '@' . $secondTLD . $topTLD;
            } else {
                $template = $parts[0] . '@' . $parts[1];
            }
        } else {
            $template = '';
        }

        //$template = str_replace(['.', '+', '-'], ['\.', '\+', '\-'], $template);

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
