<?php



namespace humhub\modules\content\tests\codeception\unit;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\permissions\ManageContent;
use humhub\modules\post\models\Post;

class TestContent extends Post
{
    protected $managePermission = ManageContent::class;

    public function setManagePermission($managePermission = [])
    {
        $this->managePermission = $managePermission;
    }

    public function getContentName()
    {
        return 'TestContent';
    }
}
