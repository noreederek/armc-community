<?php



namespace humhub\modules\activity\controllers;

use humhub\modules\activity\actions\ActivityStreamAction;
use humhub\modules\content\components\ContentContainerController;

class StreamController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'stream' => [
                'class' => ActivityStreamAction::class,
                'contentContainer' => $this->contentContainer,
            ],
        ];
    }

}
