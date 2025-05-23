<?php



namespace humhub\modules\content\tests\codeception\unit;

use humhub\modules\content\models\ContentTag;

class TestTagOtherModule extends ContentTag
{
    public $moduleId = 'otherTest';

    public static function getLabel()
    {
        return 'testCategory';
    }
}
