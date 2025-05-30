<?php


namespace humhub\widgets;

use Yii;
use humhub\modules\ui\menu\widgets\Menu;
use humhub\modules\user\components\User;

/**
 * TopMenuWidget is the primary top navigation class extended from MenuWidget.
 *
 * @since 0.5
 * @author Luke
 */
class TopMenu extends Menu
{
    /**
     * @inheritdoc
     */
    public $id = 'top-menu-nav';

    /**
     * @inheritdoc
     */
    public $template = 'topNavigation';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Don't show top menu if guest access is disabled
        if (Yii::$app->user->isGuest && !User::isGuestAccessEnabled()) {
            $this->template = '';
        }
    }



}
