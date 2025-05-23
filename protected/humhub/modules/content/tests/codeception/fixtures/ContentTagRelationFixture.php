<?php



namespace humhub\modules\content\tests\codeception\fixtures;

use humhub\modules\content\models\ContentTag;
use humhub\modules\content\models\ContentTagRelation;
use yii\test\ActiveFixture;

class ContentTagRelationFixture extends ActiveFixture
{
    public $modelClass = ContentTagRelation::class;
    public $dataFile = '@modules/content/tests/codeception/fixtures/data/content_tag_relation.php';
}
