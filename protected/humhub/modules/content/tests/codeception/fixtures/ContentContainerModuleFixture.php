<?php



namespace humhub\modules\content\tests\codeception\fixtures;

use humhub\modules\content\models\ContentContainerModuleState;
use yii\test\ActiveFixture;

class ContentContainerModuleFixture extends ActiveFixture
{
    public $modelClass = ContentContainerModuleState::class;
    public $dataFile = '@modules/content/tests/codeception/fixtures/data/contentcontainer_module.php';
}
