<?php



namespace humhub\modules\content\events;

use humhub\modules\content\models\Content;
use yii\base\ModelEvent;

class ContentEvent extends ModelEvent
{
    public Content $content;
}
