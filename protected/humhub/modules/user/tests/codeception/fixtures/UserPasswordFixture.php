<?php



namespace humhub\modules\user\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class UserPasswordFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\user\models\Password';
    public $dataFile = '@modules/user/tests/codeception/fixtures/data/user_password.php';

}
