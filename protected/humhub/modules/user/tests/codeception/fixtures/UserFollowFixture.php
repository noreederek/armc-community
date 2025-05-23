<?php



namespace humhub\modules\user\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class UserFollowFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\user\models\Follow';
    public $dataFile = '@modules/user/tests/codeception/fixtures/data/user_follow.php';

}
