<?php



namespace humhub\modules\user\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class GroupPermissionFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\user\models\GroupPermission';
    public $dataFile = '@modules/user/tests/codeception/fixtures/data/group_permission.php';

}
