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

namespace Antares\Modules\BanManagement\Services;

use Antares\Modules\BanManagement\Contracts\BannedEmailsRepositoryContract;
use Antares\Modules\BanManagement\Model\BannedEmail;
use Antares\Modules\BanManagement\Rules\Email;
use Illuminate\Filesystem\Filesystem;
use Carbon\Carbon;
use Exception;

class BannedEmailsService
{

    /**
     * Banned emails repository instance.
     *
     * @var BannedEmailsRepositoryContract
     */
    protected $bannedEmailsRepository;

    /**
     * Filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Path to the storage where the emails will be stored.
     *
     * @var string
     */
    protected $storagePath;

    /**
     * BannedEmailsService constructor.
     * @param BannedEmailsRepositoryContract $bannedEmailsRepository
     * @param Filesystem $filesystem
     */
    public function __construct(BannedEmailsRepositoryContract $bannedEmailsRepository, Filesystem $filesystem)
    {
        $this->bannedEmailsRepository = $bannedEmailsRepository;
        $this->filesystem             = $filesystem;

        $storageDirectory = storage_path('ban_management');

        if (!$this->filesystem->exists($storageDirectory)) {
            $this->filesystem->makeDirectory($storageDirectory);
        }

        $this->storagePath = $storageDirectory . DIRECTORY_SEPARATOR . 'banned_emails.txt';
    }

    /**
     * Gets email addresses from the repository and stores them in the file.
     */
    public function saveToFile()
    {
        $emails = $this->getEmailTemplatesFromRepository()->toJson();

        $this->filesystem->put($this->storagePath, $emails);
    }

    /**
     * Returns the fresh email templates from the repository.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getEmailTemplatesFromRepository()
    {
        $today = Carbon::today()->toDateString();

        return $this->bannedEmailsRepository
                        ->datatable(['email'])
                        ->where('expired_at', '>', $today)
                        ->pluck('email');
    }

    /**
     * Returns the array of saved email templates in the file.
     *
     * @return array
     */
    public function getEmailTemplates()
    {
        try {
            return json_decode($this->filesystem->get($this->storagePath));
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Check if the given email is banned.
     *
     * @param string $email
     * @return bool
     */
    public function isEmailBanned($email)
    {
        $emailTemplates = $this->getEmailTemplates();

        foreach ($emailTemplates as $emailTemplate) {
            $emailRule = new Email($emailTemplate);
            if ($emailRule->check($email)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the model for the given email.
     *
     * @param string $email
     * @return BannedEmail | null
     */
    public function getModelForEmail($email)
    {
        $emailTemplates = $this->getEmailTemplates();

        foreach ($emailTemplates as $emailTemplate) {
            $emailRule = new Email($emailTemplate);

            if ($emailRule->check($email)) {
                return $this->bannedEmailsRepository->findByEmail($emailTemplate);
            }
        }

        return null;
    }

}
