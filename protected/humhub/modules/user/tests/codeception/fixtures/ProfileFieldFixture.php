<?php



namespace humhub\modules\user\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class ProfileFieldFixture extends ActiveFixture
{
    public $tableName = 'profile_field';
    public $modelClass = 'humhub\modules\user\models\ProfileField';
    public $dataFile = '@modules/user/tests/codeception/fixtures/data/profile_field.php';

    public $depends = [
        'humhub\modules\user\tests\codeception\fixtures\ProfileFieldCategoryFixture',
    ];
}
