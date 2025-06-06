<?php



namespace humhub\modules\user\notifications;

use humhub\modules\comment\models\Comment;
use humhub\modules\user\models\User;
use Yii;
use yii\bootstrap\Html;
use humhub\modules\notification\components\BaseNotification;

/**
 * Mentioned Notification is fired to all users which are mentionied
 * in a ContentActiveRecord or ContentAddonActiveRecord
 */
class Mentioned extends BaseNotification
{
    /**
     * @inheritdoc
     */
    public $viewName = 'mentioned';

    /**
     * @inheritdoc
     */
    public $moduleId = 'user';

    /**
     * @inheritdoc
     */
    public function category()
    {
        return new MentionedNotificationCategory();
    }

    /**
     * @inheritdoc
     */
    public function getViewName()
    {
        if ($this->source instanceof Comment) {
            return 'mentionedComment';
        }

        return 'mentioned';
    }

    /**
     * inheritdoc
     */
    public function send(User $user)
    {
        // Do additional access check here, because the mentioned user may have no access to the content
        if (!$this->source->content->canView($user)) {
            return;
        }

        return parent::send($user);
    }

    /**
     * inheritdoc
     */
    public function getMailSubject()
    {
        return Yii::t('UserModule.notification', "{displayName} just mentioned you in {contentTitle} \"{preview}\"", [
            'displayName' => $this->originator->displayName,
            'contentTitle' => $this->getContentName(),
            'preview' => $this->getContentPlainTextPreview(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function html()
    {
        return Yii::t('UserModule.notification', '{displayName} mentioned you in {contentTitle}.', [
            'displayName' => Html::tag('strong', Html::encode($this->originator->displayName)),
            'contentTitle' => $this->getContentInfo($this->source),
        ]);
    }
}
