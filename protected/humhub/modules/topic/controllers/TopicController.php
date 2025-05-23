<?php



namespace humhub\modules\topic\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\topic\widgets\TopicPicker;

class TopicController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public $requireContainer = false;

    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [
            ['json'],
        ];
    }

    public function actionSearch($keyword)
    {
        return $this->contentContainer
            ? TopicPicker::searchByContainer($keyword, $this->contentContainer)
            : TopicPicker::search($keyword);
    }
}
