<?php



namespace humhub\modules\content\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class ContentContainerDefaultPermissionFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\content\models\ContentContainerDefaultPermission';
    public $dataFile = '@modules/content/tests/codeception/fixtures/data/contentcontainer_default_permission.php';
}
