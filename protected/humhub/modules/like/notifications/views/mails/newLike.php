<?php

/* @var $this yii\web\View */
/* @var $viewable humhub\modules\like\notifications\NewLike */
/* @var $url string */
/* @var $date string */
/* @var $isNew bool */
/* @var $originator \humhub\modules\user\models\User */
/* @var $source yii\db\ActiveRecord */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $space humhub\modules\space\models\Space */

/* @var $record Notification */

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\notification\models\Notification;
use humhub\widgets\mails\MailButtonList;

$likedRecord = $viewable->getLikedRecord();
?>

<?php $this->beginContent('@notification/views/layouts/mail.php', $_params_); ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
        <tr>
            <td>
                <?=
                humhub\widgets\mails\MailContentEntry::widget([
                    'receiver' => $record->user,
                    'content' => $likedRecord,
                    'date' => $date,
                    'space' => $space
                ])
                ?>
            </td>
        </tr>
        <tr>
            <td height="10"></td>
        </tr>
        <tr>
            <td>
                <?=
                MailButtonList::widget([
                    'buttons' => [
                        humhub\widgets\mails\MailButton::widget(['url' => $url, 'text' => Yii::t('LikeModule.notifications', 'View Online')])
                    ]
                ])
                ?>
            </td>
        </tr>
    </table>
<?php
$this->endContent();
