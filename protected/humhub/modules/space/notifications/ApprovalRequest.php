<?php



namespace humhub\modules\space\notifications;

use humhub\modules\notification\components\BaseNotification;
use humhub\modules\space\models\Membership;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/**
 * SpaceApprovalRequestNotification
 *
 * @since 0.5
 */
class ApprovalRequest extends BaseNotification
{
    /**
     * @inheritdoc
     */
    public $moduleId = 'space';

    /**
     * @inheritdoc
     */
    public $viewName = 'approval';
    public $message;

    /**
     * @inheritdoc
     */
    public $markAsSeenOnClick = false;

    /**
     * Sets the approval request message for this notification.
     *
     * @param string $message
     */
    public function withMessage($message)
    {
        if ($message) {
            $this->message = $message;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getViewParams($params = [])
    {
        return ArrayHelper::merge(parent::getViewParams(['message' => $this->message]), $params);
    }

    /**
     * @inheritdoc
     */
    public function getMailSubject()
    {
        return Yii::t('SpaceModule.notification', '{displayName} requests membership for the space {spaceName}', [
            '{displayName}' => $this->originator->displayName,
            '{spaceName}' => $this->source->name,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function category()
    {
        return new SpaceMemberNotificationCategory();
    }

    /**
     * @inerhitdoc
     */
    public function isValid()
    {
        return Membership::find()->where([
            'user_id' => $this->originator->id,
            'space_id' => $this->source->id,
            'status' => Membership::STATUS_APPLICANT,
        ])->exists();
    }

    /**
     * @inheritdoc
     */
    public function html()
    {
        return Yii::t('SpaceModule.notification', '{displayName} requests membership for the space {spaceName}', [
            '{displayName}' => Html::tag('strong', Html::encode($this->originator->displayName)),
            '{spaceName}' => Html::tag('strong', Html::encode($this->source->name)),
        ]);
    }

    public function getUrl()
    {
        return $this->source->createUrl('/space/manage/member/pending-approvals');
    }

    /**
     * @inheritdoc
     */
    public function serialize(): array
    {
        return ['source' => $this->source, 'originator' => $this->originator, 'message' => $this->message];
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        $this->init();
        $unserializedArr = unserialize($serialized);
        $this->from($unserializedArr['originator']);
        $this->about($unserializedArr['source']);
        $this->withMessage($unserializedArr['message']);
    }

}
