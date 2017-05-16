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

namespace Antares\Modules\BanManagement\Model;

use Antares\Modules\BanManagement\Contracts\BanReasonContract;
use Antares\Modules\BanManagement\Contracts\ExpirableContract;
use Antares\Modules\BanManagement\Http\Presenters\ModelPresenter;
use Antares\Modules\BanManagement\Traits\ExpirationTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Antares\Logger\Traits\LogRecorder;
use Antares\Model\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $email
 * @property int $user_id
 * @property int $status
 * @property string $internal_reason
 * @property string $reason
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $expired_at
 * @property User $user
 * @method Builder active()
 */
class BannedEmail extends Eloquent implements BanReasonContract, ExpirableContract
{

    use LogRecorder,
        ExpirationTrait;

    // Disables the log record in this model.
    protected $auditEnabled   = true;
    // Disables the log record after 500 records.
    protected $historyLimit   = 500;
    // Fields you do NOT want to register.
    protected $dontKeepLogOf  = ['created_at', 'updated_at'];
    // Tell what actions you want to audit.
    protected $auditableTypes = ['created', 'saved', 'deleted'];

    /**
     * {@inheritdoc}
     */
    protected $fillable = ['email', 'internal_reason', 'reason', 'note', 'expired_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at', 'expired_at'];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'id'         => 'integer',
        'expired_at' => 'date',
        'status'     => 'integer',
    ];

    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'email'  => '',
        'status' => 0,
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_ban_management_banned_emails';

    /**
     * Return the model primary key.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the banned email address.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the reason of the ban.
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Returns the internal reason of the ban.
     *
     * @return string
     */
    public function getInternalReason()
    {
        return $this->internal_reason;
    }

    /**
     * Relation to user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|User
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Returns the creation datetime.
     *
     * @return Carbon
     */
    public function getCreationDate()
    {
        return $this->created_at;
    }

    /**
     * Returns the date when the rule will expire, otherwise returns null.
     *
     * @return Carbon|null
     */
    public function getExpirationDate()
    {
        return $this->expired_at;
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return string
     */
    public static function getPatternUrl()
    {
        return handles('antares::ban_management/bannedemails/{id}/edit');
    }

    /**
     * Checks if the banned email is active based on its expiration date.
     *
     * @param Carbon $date
     * @return bool
     */
    public function isActive(Carbon $date)
    {
        return !$this->isExpired($date);
    }

    /**
     * Returns the model presenter.
     *
     * @return ModelPresenter
     */
    public function getPresenter()
    {
        return new ModelPresenter($this);
    }

    /**
     * Filters by (virtual) active status.
     *
     * @param Builder $query
     */
    public function scopeActive(Builder $query)
    {
        $query->where('status', 1);
    }

}
