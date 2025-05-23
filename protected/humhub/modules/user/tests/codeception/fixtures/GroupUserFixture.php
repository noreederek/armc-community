<?php



namespace humhub\modules\user\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class GroupUserFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\user\models\GroupUser';
    public $dataFile = '@modules/user/tests/codeception/fixtures/data/group_user.php';

    public $depends = [
        UserFixture::class,
        GroupFixture::class,
    ];

}
