<?php


namespace humhub\modules\marketplace\widgets;

use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\marketplace\Module;
use humhub\widgets\Button;
use Yii;

class MarketplaceLink extends Button
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->link(['/marketplace/browse'])->loader(false);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function beforeRun()
    {
        if (!Module::isMarketplaceEnabled() || !Yii::$app->user->can(ManageModules::class)) {
            return false;
        }

        return parent::beforeRun();
    }
}
