<?php



namespace humhub\modules\content\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class ContentFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\content\models\Content';
    public $dataFile = '@modules/content/tests/codeception/fixtures/data/content.php';

    public $depends = [
        'humhub\modules\content\tests\codeception\fixtures\ContentContainerFixture',
        'humhub\modules\content\tests\codeception\fixtures\ContentTagFixture',
        'humhub\modules\content\tests\codeception\fixtures\ContentTagRelationFixture',
        'humhub\modules\content\tests\codeception\fixtures\ContentContainerTagFixture',
        'humhub\modules\content\tests\codeception\fixtures\ContentContainerTagRelationFixture',
        'humhub\modules\post\tests\codeception\fixtures\PostFixture',
        'humhub\modules\comment\tests\codeception\fixtures\CommentFixture',
        'humhub\modules\like\tests\codeception\fixtures\LikeFixture',
    ];

}
