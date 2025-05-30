<?php



namespace humhub\widgets\mails;

use humhub\components\rendering\Viewable;
use humhub\modules\content\interfaces\ContentOwner;

/**
 * MailCommentRow renders a comment row with originator info and image and comment content.
 *
 * @author buddha
 * @since 1.2
 */
class MailCommentEntry extends \yii\base\Widget
{
    /**
     * @var \humhub\modules\user\models\User content originator
     */
    public $originator;

    /**
     * @var \humhub\modules\user\models\User notification receiver
     */
    public $receiver;

    /**
     * @var string|Viewable|ContentOwner content to render
     */
    public $comment;

    /**
     * @var \humhub\modules\space\models\Space space of content (optional)
     */
    public $space;

    /**
     * @var string content date
     */
    public $date;

    /**
     * @inheritdoc
     */
    public function run()
    {

        return $this->render('mailCommentEntry', [
            'originator' => $this->originator,
            'receiver' => $this->receiver,
            'comment' => $this->comment,
            'space' => $this->space,
            'date' => $this->date,
        ]);
    }

}
