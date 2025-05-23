<?php



namespace humhub\modules\comment\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class CommentFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\comment\models\Comment';
    public $dataFile = '@modules/comment/tests/codeception/fixtures/data/comment.php';

}
