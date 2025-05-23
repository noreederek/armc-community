<?php



namespace humhub\modules\post\tests\codeception\fixtures;

use tests\codeception\_support\ContentActiveFixture;

class PostFixture extends ContentActiveFixture
{
    public $modelClass = 'humhub\modules\post\models\Post';
    public $dataFile = '@modules/post/tests/codeception/fixtures/data/post.php';
}
