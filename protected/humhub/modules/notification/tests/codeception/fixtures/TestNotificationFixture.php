<?php



namespace humhub\modules\notification\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class TestNotificationFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\notification\models\Notification';
    public $dataFile = '@modules/notification/tests/codeception/fixtures/data/testnotification.php';

    public $depends = [
        'humhub\modules\user\tests\codeception\fixtures\GroupUserFixture',
    ];
}
