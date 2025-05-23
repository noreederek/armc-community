<?php



namespace humhub\modules\content\events;

use humhub\modules\content\models\Content;

class ContentStateEvent extends ContentEvent
{
    public Content $content;

    public int $newState;
    public ?int $previousState;
}
