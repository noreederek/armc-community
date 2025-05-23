<?php



namespace humhub\modules\user\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class InviteFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\user\models\Invite';
    public $dataFile = '@modules/user/tests/codeception/fixtures/data/user_invite.php';

}
