<?php



namespace humhub\modules\user\tests\codeception\fixtures;

use humhub\modules\space\tests\codeception\fixtures\SpaceFixture;
use yii\test\ActiveFixture;

class GroupFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\user\models\Group';
    public $dataFile = '@modules/user/tests/codeception/fixtures/data/group.php';

    public $depends = [
        UserFixture::class,
        SpaceFixture::class,
    ];

}
