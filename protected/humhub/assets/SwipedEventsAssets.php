<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;

/**
 * animate.css
 *
 * @author buddha
 */
class SwipedEventsAssets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/swiped-events/dist';

    /**
     * @inheritdoc
     */
    public $js = ['swiped-events.min.js'];

}
