<?php



namespace humhub\modules\user\widgets;

use humhub\helpers\ControllerHelper;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\widgets\LeftNavigation;
use humhub\modules\user\Module;
use humhub\modules\user\models\User;
use humhub\modules\user\permissions\ViewAboutPage;
use Yii;

/**
 * ProfileMenuWidget shows the (usually left) navigation on user profiles.
 *
 * Only a controller which uses the 'application.modules_core.user.ProfileControllerBehavior'
 * can use this widget.
 *
 * The current user can be gathered via:
 *  $user = Yii::$app->getController()->getUser();
 *
 * @since 0.5
 * @author Luke
 */
class ProfileMenu extends LeftNavigation
{
    /**
     * @var User
     */
    public $user;


    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->panelTitle = Yii::t('UserModule.profile', '<strong>Profile</strong> menu');

        /** @var Module $module */
        $module = Yii::$app->getModule('user');

        if (!$module->profileDisableStream) {
            $this->addEntry(new MenuLink([
                'label' => Yii::t('UserModule.profile', 'Stream'),
                'icon' => 'stream',
                'url' => $this->user->createUrl('//user/profile/home'),
                'sortOrder' => 200,
                'isActive' => ControllerHelper::isActivePath('user', 'profile', ['index', 'home']),
            ]));
        }

        $this->addEntry(new MenuLink([
            'label' => Yii::t('UserModule.profile', 'About'),
            'icon' => 'about',
            'url' => $this->user->createUrl('/user/profile/about'),
            'sortOrder' => 300,
            'isActive' => ControllerHelper::isActivePath('user', 'profile', 'about'),
            'isVisible' => $this->user->permissionManager->can(ViewAboutPage::class),
        ]));

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->user->isGuest && $this->user->visibility != User::VISIBILITY_ALL) {
            return '';
        }

        return parent::run();
    }

}
