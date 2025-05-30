<?php


namespace humhub\modules\content\jobs;

use humhub\modules\content\models\Content;
use humhub\modules\queue\LongRunningActiveJob;
use Yii;

class PurgeDeletedContents extends LongRunningActiveJob
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        foreach (Content::findAll(['content.state' => Content::STATE_DELETED]) as $content) {
            if (!$content->hardDelete()) {
                Yii::error('Purge deleted contents job: Unable to delete content ID ' . $content->id . '. Error: ' . implode(' ', $content->getErrorSummary(true)), 'content');
            }
        }
    }

}
