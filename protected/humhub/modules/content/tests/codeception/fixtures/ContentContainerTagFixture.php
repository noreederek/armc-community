<?php



namespace humhub\modules\content\tests\codeception\fixtures;

use humhub\modules\content\models\ContentContainerTag;
use yii\test\ActiveFixture;

class ContentContainerTagFixture extends ActiveFixture
{
    public $modelClass = ContentContainerTag::class;
    public $dataFile = '@modules/content/tests/codeception/fixtures/data/contentcontainer_tag.php';
}
