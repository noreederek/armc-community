<?php



namespace humhub\modules\notification\targets;

use humhub\components\InstallationState;
use humhub\modules\notification\components\BaseNotification;
use humhub\modules\user\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 *
 * @author buddha
 */
class MailTarget extends BaseTarget
{
    /**
     * @inheritdoc
     */
    public $id = 'email';

    /**
     * Enable this target by default.
     * @var bool
     */
    public $defaultSetting = true;

    /**
     * @var array Notification mail layout.
     */
    public $view = [
        'html' => '@notification/views/mails/wrapper',
        'text' => '@notification/views/mails/plaintext/wrapper',
    ];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('NotificationModule.targets', 'E-Mail');
    }

    /**
     * @inheritdoc
     */
    public function handle(BaseNotification $notification, User $recipient)
    {
        Yii::$app->i18n->setUserLocale($recipient);

        Yii::$app->view->params['showUnsubscribe'] = true;
        Yii::$app->view->params['unsubscribeUrl'] = Url::to(['/notification/user'], true);

        // Note: the renderer is configured in common.php by default its an instance of MailTarget
        $renderer = $this->getRenderer();

        $viewParams = ArrayHelper::merge([
            'headline' => '',
            'notification' => $notification,
            'space' => $notification->getSpace(),
            'content' => $renderer->render($notification),
            'content_plaintext' => $renderer->renderText($notification),
        ], $notification->getViewParams());

        $mail = Yii::$app->mailer->compose($this->view, $viewParams)
            ->setTo($recipient->email)
            ->setSubject(str_replace("\n", " ", trim($notification->getMailSubject())));
        if ($replyTo = Yii::$app->settings->get('mailerSystemEmailReplyTo')) {
            $mail->setReplyTo($replyTo);
        }

        if ($notification->beforeMailSend($mail)) {
            $mail->send();
        }


        Yii::$app->i18n->autosetLocale();
    }

    /**
     * @inheritdoc
     */
    public function isActive(User $user = null)
    {
        // Do not send mail notifications for example content during installlation.
        return parent::isActive() && Yii::$app->installationState->hasState(InstallationState::STATE_INSTALLED);
    }
}
