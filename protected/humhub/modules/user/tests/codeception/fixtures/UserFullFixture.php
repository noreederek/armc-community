<?php



namespace humhub\modules\user\tests\codeception\fixtures;

use humhub\modules\content\tests\codeception\fixtures\ContentContainerFixture;
use yii\test\ActiveFixture;

class UserFullFixture extends ActiveFixture
{
    public $tableName = 'user_mentioning';
    public $depends = [
        UserFixture::class,
        UserProfileFixture::class,
        ContentContainerFixture::class,
        UserPasswordFixture::class,
        UserFollowFixture::class,
        InviteFixture::class,
        GroupSpaceFixture::class,
        GroupFixture::class,
    ];

}
