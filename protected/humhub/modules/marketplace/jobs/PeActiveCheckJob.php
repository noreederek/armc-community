<?php



namespace humhub\modules\marketplace\jobs;

use humhub\modules\marketplace\components\LicenceManager;
use humhub\modules\queue\ActiveJob;

class PeActiveCheckJob extends ActiveJob
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        LicenceManager::get();
    }
}
