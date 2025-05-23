<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;

/**
 * Clipboard JS
 *
 * @author luke
 */
class ClipboardJsAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/clipboard-polyfill/dist/main';

    /**
     * @inheritdoc
     */
    public $js = ['clipboard-polyfill.js'];

    public $publishOptions = [
        'only' => ['clipboard-polyfill.js', 'clipboard-polyfill.js.map'],
    ];

}
