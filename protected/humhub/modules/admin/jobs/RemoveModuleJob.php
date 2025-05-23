<?php


namespace humhub\modules\admin\jobs;

use humhub\modules\queue\ActiveJob;
use humhub\modules\queue\interfaces\ExclusiveJobInterface;
use Yii;

class RemoveModuleJob extends ActiveJob implements ExclusiveJobInterface
{
    public $moduleId;

    public function getExclusiveJobId()
    {
        return "module.$this->moduleId.remove";
    }


    public function run()
    {
        Yii::$app->moduleManager->removeModule($this->moduleId);
    }
}
