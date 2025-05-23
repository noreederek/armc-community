<?php



namespace humhub\assets;

use humhub\components\assets\WebStaticAssetBundle;

/**
 * Color Picker js utility
 *
 * @author buddha
 */
class HumHubColorPickerAsset extends WebStaticAssetBundle
{
    /**
     * @inheritdoc
     */
    public $js = ['js/humhub/humhub.ui.colorpicker.js'];

    public $depends = [BootstrapColorPickerAsset::class];

}
