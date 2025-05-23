<?php



namespace humhub\modules\marketplace\assets;

use humhub\components\assets\AssetBundle;
use Yii;

class Assets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@marketplace/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.marketplace.js',
    ];

    public static function register($view)
    {
        $view->registerJsConfig('marketplace', [
            'text' => [
                'installing' => Yii::t('MarketplaceModule.base', 'Module is <strong>installing...</strong>'),
            ],
        ]);

        return parent::register($view);
    }
}
