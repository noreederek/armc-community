<?php



namespace humhub\assets;

use humhub\components\assets\WebStaticAssetBundle;

/**
 * jquery-color
 *
 * @author buddha
 */
class BootstrapColorPickerAsset extends WebStaticAssetBundle
{
    /**
     * @inheritdoc
     */
    public $js = ['js/colorpicker/js/bootstrap-colorpicker-modified.js'];

    /**
    * @inheritdoc
    */
    public $css = ['js/colorpicker/css/bootstrap-colorpicker.min.css'];

}
