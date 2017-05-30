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

namespace Antares\Modules\BanManagement\Model;

use Antares\Modules\BanManagement\Contracts\RuleContract;
use Antares\Modules\BanManagement\Traits\ExpirationTrait;
use Carbon\Carbon;

class PlainRule implements RuleContract
{

    use ExpirationTrait;

    /**
     * Determine if the rule is enabled.
     *
     * @var bool
     */
    protected $enabled = true;

    /**
     * The value of the rule.
     *
     * @var string
     */
    protected $value;

    /**
     * Determine if the rule should be whitelisted.
     *
     * @var bool
     */
    protected $trusted;

    /**
     * Date when rule will be expired. For null means infinite time.
     *
     * @var Carbon|null
     */
    protected $expirationDate;

    /**
     * PlainRule constructor.
     * @param $value
     * @param bool $trusted
     * @param Carbon|null $expirationDate
     */
    public function __construct($value, $trusted = false, Carbon $expirationDate = null)
    {
        $this->value          = (string) $value;
        $this->trusted        = (bool) $trusted;
        $this->expirationDate = $expirationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isTrusted()
    {
        return $this->trusted;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the reason of the ban.
     *
     * @return string
     */
    public function getReason()
    {
        return '';
    }

    /**
     * Returns the internal reason of the ban.
     *
     * @return string
     */
    public function getInternalReason()
    {
        return '';
    }

    /**
     * Returns the date when the rule will expire, otherwise returns null.
     *
     * @return \Carbon\Carbon|null
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::ban_management/rules/{id}/edit');
    }

}
