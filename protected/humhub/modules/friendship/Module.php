<?php



namespace humhub\modules\friendship;

use Yii;

/**
 * Friedship Module
 */
class Module extends \humhub\components\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'humhub\modules\friendship\controllers';

    /**
     * Returns if the friendship system is enabled
     *
     * @return bool is enabled
     */
    public function isFriendshipEnabled(): bool
    {
        if (Yii::$app->getModule('friendship')->settings->get('enable')) {
            return true;
        }

        return false;
    }

    public function getName()
    {
        return Yii::t('FriendshipModule.base', 'Friendship');
    }

    /**
     * @inheritdoc
     */
    public function getNotifications()
    {
        return [
            'humhub\modules\friendship\notifications\Request',
            'humhub\modules\friendship\notifications\RequestApproved',
            'humhub\modules\friendship\notifications\RequestDeclined',
        ];
    }
}
