<?php



namespace humhub\modules\user\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\user\models\User';
    public $dataFile = '@modules/user/tests/codeception/fixtures/data/user.php';
}
