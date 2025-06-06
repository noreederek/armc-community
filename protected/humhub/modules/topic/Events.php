<?php



namespace humhub\modules\topic;

use humhub\helpers\ControllerHelper;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\topic\widgets\ContentTopicButton;
use humhub\modules\topic\widgets\TopicPicker;
use humhub\modules\user\events\UserEvent;
use humhub\modules\user\widgets\AccountMenu;
use Yii;
use yii\base\BaseObject;

class Events extends BaseObject
{
    public static function onWallEntryControlsInit($event)
    {
        /** @var ContentActiveRecord $record */
        $record = $event->sender->object;

        if ($record->content->canEdit() && TopicPicker::showTopicPicker($record->content->container)) {
            $event->sender->addWidget(ContentTopicButton::class, ['record' => $record], ['sortOrder' => 370]);
        }
    }

    /**
     * @param $event
     */
    public static function onSpaceSettingMenuInit($event)
    {
        $space = $event->sender->space;

        if ($space->isAdmin() && Yii::$app->getModule('space')->settings->get('allowSpaceTopics', true)) {
            $event->sender->addItem([
                'label' => Yii::t('TopicModule.base', 'Topics'),
                'url' => $space->createUrl('/topic/manage'),
                'isActive' => ControllerHelper::isActivePath('topic', 'manage'),
                'sortOrder' => 250,
            ]);
        }
    }

    /**
     * @param $event UserEvent
     */
    public static function onProfileSettingMenuInit($event)
    {
        if (Yii::$app->user->isGuest || !Yii::$app->getModule('user')->settings->get('auth.allowUserTopics', true)) {
            return;
        }

        $event->sender->addItem([
            'label' => Yii::t('TopicModule.base', 'Topics'),
            'url' => Yii::$app->user->identity->createUrl('/topic/manage'),
            'isActive' => ControllerHelper::isActivePath('topic', 'manage'),
            'sortOrder' => 250,
        ]);

        if (ControllerHelper::isActivePath('topic', 'manage')) {
            AccountMenu::markAsActive('account-settings-settings');
        }
    }
}
