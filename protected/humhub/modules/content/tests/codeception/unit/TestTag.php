<?php



namespace humhub\modules\content\tests\codeception\unit;

use humhub\modules\content\models\ContentTag;

class TestTag extends ContentTag
{
    public $moduleId = 'test';
    public $includeTypeQuery = true;

    public static function getLabel()
    {
        return 'testCategory';
    }
}
