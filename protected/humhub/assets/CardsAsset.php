<?php



namespace humhub\assets;

use humhub\components\assets\WebStaticAssetBundle;
use yii\web\View;

class CardsAsset extends WebStaticAssetBundle
{
    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub/humhub.cards.js',
    ];

    /**
     * @inheritdoc
     */
    public $jsOptions = ['position' => View::POS_END];

}
