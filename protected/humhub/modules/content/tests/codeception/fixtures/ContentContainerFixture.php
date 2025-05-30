<?php



namespace humhub\modules\content\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class ContentContainerFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\content\models\ContentContainer';
    public $dataFile = '@modules/content/tests/codeception/fixtures/data/contentcontainer.php';

    public $depends = [
        'humhub\modules\content\tests\codeception\fixtures\ContentContainerDefaultPermissionFixture',
        'humhub\modules\content\tests\codeception\fixtures\ContentContainerPermissionFixture',
        'humhub\modules\content\tests\codeception\fixtures\ContentContainerSettingFixture',
        'humhub\modules\content\tests\codeception\fixtures\ContentContainerModuleFixture',
    ];

}
