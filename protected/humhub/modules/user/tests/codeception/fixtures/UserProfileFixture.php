<?php



namespace humhub\modules\user\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class UserProfileFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\user\models\Profile';
    public $dataFile = '@modules/user/tests/codeception/fixtures/data/profile.php';

    public $depends = [
        'humhub\modules\user\tests\codeception\fixtures\ProfileFieldFixture',
    ];

}
