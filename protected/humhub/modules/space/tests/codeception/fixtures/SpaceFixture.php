<?php



namespace humhub\modules\space\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class SpaceFixture extends ActiveFixture
{
    public $modelClass = 'humhub\modules\space\models\Space';
    public $depends = [
        'humhub\modules\content\tests\codeception\fixtures\ContentContainerFixture',
    ];

}
