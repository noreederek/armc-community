<?php



namespace humhub\modules\file\tests\codeception\fixtures;

use humhub\modules\file\models\FileHistory;
use yii\test\ActiveFixture;

class FileHistoryFixture extends ActiveFixture
{
    public $modelClass = FileHistory::class;
    public $dataFile = '@file/tests/codeception/fixtures/data/file-history.php';

}
