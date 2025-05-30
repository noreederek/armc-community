<?php



namespace humhub\modules\comment\widgets;

use humhub\modules\comment\models\Comment;
use humhub\modules\like\widgets\LikeLink;
use humhub\widgets\BaseStack;

/**
 * CommentEntryControls
 * @since 1.8
 */
class CommentEntryLinks extends BaseStack
{
    /**
     * @var Comment
     */
    public $object = null;

    /**
     * @inheritdoc
     */
    public $seperator = '&nbsp;&middot;&nbsp;';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initDefaultWidgets();
        parent::init();
    }

    /**
     * Initialize default widgets for Comment links
     */
    public function initDefaultWidgets()
    {
        if (!($this->object instanceof Comment)) {
            return;
        }
        $this->addWidget(CommentLink::class, ['object' => $this->object], ['sortOrder' => 100]);
        $this->addWidget(LikeLink::class, ['object' => $this->object], ['sortOrder' => 200]);
    }

}
