<?php



namespace humhub\modules\user\widgets;

use humhub\modules\user\models\User;
use humhub\widgets\BaseSidebar;

/**
 * ProfileSidebar implements the sidebar for the user profiles.
 *
 * @since 0.5
 * @author Luke
 */
class ProfileSidebar extends BaseSidebar
{
    /**
     * @var User the user this sidebar belongs to
     */
    public $user;

}
