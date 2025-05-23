<?php



namespace humhub\modules\content\tests\codeception\fixtures;

use humhub\modules\content\models\ContentContainerTagRelation;
use yii\test\ActiveFixture;

class ContentContainerTagRelationFixture extends ActiveFixture
{
    public $modelClass = ContentContainerTagRelation::class;
    public $dataFile = '@modules/content/tests/codeception/fixtures/data/contentcontainer_tag_relation.php';
}
