<?php



namespace humhub\modules\live\tests\codeception\fixtures;

use humhub\modules\live\models\Live;
use yii\test\ActiveFixture;

class LiveFixture extends ActiveFixture
{
    public $modelClass = Live::class;
    public $dataFile = '@live/tests/codeception/fixtures/data/live.php';

}
