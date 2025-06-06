<?php



namespace humhub\modules\space\notifications;

use humhub\modules\notification\components\BaseNotification;
use Yii;
use yii\bootstrap\Html;

/**
 * SpaceInviteDeclinedNotification is sent to the originator of the invite to
 * inform him about the decline.
 *
 * @since 0.5
 * @author Luke
 */
class InviteDeclined extends BaseNotification
{
    /**
     * @inheritdoc
     */
    public $moduleId = 'space';

    /**
     * @inheritdoc
     */
    public $viewName = 'inviteDeclined';

    /**
     * @inheritdoc
     */
    public function category()
    {
        return new SpaceMemberNotificationCategory();
    }

    /**
     * @inheritdoc
     */
    public function getSpace()
    {
        return $this->source;
    }

    public function getMailSubject()
    {
        return $this->getInfoText($this->originator->displayName, $this->getSpace()->name);
    }

    /**
     * @inheritdoc
     */
    public function html()
    {
        return $this->getInfoText(
            Html::tag('strong', Html::encode($this->originator->displayName)),
            Html::tag('strong', Html::encode($this->getSpace()->name)),
        );
    }

    private function getInfoText($displayName, $spaceName)
    {
        return Yii::t('SpaceModule.notification', '{displayName} declined your invite for the space {spaceName}', [
            '{displayName}' => $displayName,
            '{spaceName}' => $spaceName,
        ]);
    }

}
