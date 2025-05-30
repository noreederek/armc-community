<?php



namespace humhub\modules\space;

use humhub\modules\admin\models\forms\SpaceSettingsForm;
use humhub\modules\user\models\User;
use Yii;

/**
 * SpaceModule provides all space related classes & functions.
 *
 * @author Luke
 * @since 0.5
 */
class Module extends \humhub\components\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'humhub\modules\space\controllers';

    /**
     * @var bool Allow global admins (super admin) access to private content also when no member
     */
    public $globalAdminCanAccessPrivateContent = false;

    /**
     *
     * @var bool Do not allow multiple spaces with the same name
     */
    public $useUniqueSpaceNames = true;

    /**
     * @var bool defines if the space following is disabled or not.
     * @since 1.2
     */
    public $disableFollow = false;

    /**
     * @var bool defines if a space members can add anyone the the space without invitation
     * @since 1.8
     */
    public $membersCanAddWithoutInvite = false;

    /**
     * @var int maximum space url length
     * @since 1.3
     */
    public $maximumSpaceUrlLength = 45;

    /**
     * @var int minimum space url length
     * @since 1.3
     */
    public $minimumSpaceUrlLength = 2;

    /**
     * @var bool hide about page in space menu (default value for advanced settings page)
     * @since 1.7
     */
    public $hideAboutPage = false;

    /**
     * @var bool Hide "Spaces" in top menu
     * @since 1.10
     */
    public $hideSpacesPage = false;

    /**
     * @var bool Hide Activity Sidebar Widget (default value for advanced settings page)
     * @since 1.13
     */
    public $hideActivities = false;

    /**
     * @var bool Hide Members (default value for advanced settings page)
     * @since 1.13
     */
    public $hideMembers = false;

    /**
     * @var bool Hide Followers (default value for advanced settings page)
     * @since 1.13
     */
    public $hideFollowers = false;

    /**
     * @var SpaceSettingsForm|null
     */
    private ?SpaceSettingsForm $defaultSettings = null;

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer instanceof models\Space) {
            return [
                new permissions\InviteUsers(),
            ];
        } elseif ($contentContainer instanceof User) {
            return [];
        }

        return [
            new permissions\SpaceDirectoryAccess(),
            new permissions\CreatePrivateSpace(),
            new permissions\CreatePublicSpace(),
        ];
    }

    public function getName()
    {
        return Yii::t('SpaceModule.base', 'Space');
    }

    /**
     * @inheritdoc
     */
    public function getNotifications()
    {
        return [
            'humhub\modules\space\notifications\ApprovalRequest',
            'humhub\modules\space\notifications\ApprovalRequestAccepted',
            'humhub\modules\space\notifications\ApprovalRequestDeclined',
            'humhub\modules\space\notifications\Invite',
            'humhub\modules\space\notifications\InviteAccepted',
            'humhub\modules\space\notifications\InviteDeclined',
            'humhub\modules\space\notifications\SpaceCreated',
        ];
    }

    /**
     * @return SpaceSettingsForm
     */
    public function getDefaultSettings(): SpaceSettingsForm
    {
        if ($this->defaultSettings === null) {
            $this->defaultSettings = new SpaceSettingsForm(['settingsManager' => $this->settings]);
            $this->defaultSettings->loadBySettings();
        }
        return $this->defaultSettings;
    }
}
