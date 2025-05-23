<?php



namespace humhub\modules\post\controllers;

use humhub\modules\post\permissions\CreatePost;

final class ShareIntendController extends \humhub\modules\content\controllers\ShareIntendController
{
    public function actionCreate()
    {
        return $this->renderAjax('create', [
            'shareTarget' => $this->shareTarget,
            'fileList' => \humhub\modules\file\controllers\ShareIntendController::getShareFileGuids(),
        ]);
    }

    protected function getCreatePermissionClass(): string
    {
        return CreatePost::class;
    }

}
