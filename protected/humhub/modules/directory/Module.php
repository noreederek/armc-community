<?php



namespace humhub\modules\directory;

/**
 * Deprecated Directory Base Module
 * @deprecated since 1.9
 */
class Module extends \humhub\components\Module
{
    /**
     * @deprecated since 1.11 will be removed with v1.12
     */
    public $isCoreModule = false;
    public $memberListSortField = 'profile.lastname';
    public $pageSize = 25;
    public $active = false;
    public $guestAccess = true;
    public $showUserProfilePosts = true;

}
