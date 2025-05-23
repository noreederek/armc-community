<?php



namespace humhub\modules\notification\widgets;

use humhub\modules\notification\models\Notification;
use Yii;
use yii\base\Widget;

/**
 * UpdateNotificationCount widget is an LayoutAddon widget for updating the notification count
 * and is only used if pjax is active.
 *
 * @author buddha
 * @since 1.2
 */
class UpdateNotificationCount extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        return $this->render('updateNotificationCount', [
            'count' => Notification::findUnseen()->count(),
        ]);
    }
}
