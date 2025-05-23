<?php


namespace humhub\modules\marketplace\jobs;

use humhub\modules\marketplace\services\MarketplaceService;
use humhub\modules\queue\ActiveJob;

class RefreshPendingModuleUpdateCountJob extends ActiveJob
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        (new MarketplaceService())->refreshPendingModuleUpdateCount();
    }
}
