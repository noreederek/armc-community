<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;

class ProsemirrorEditorAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/humhub-prosemirror-richtext/dist/';

    /**
     * @inheritdoc
     */
    public $js = ['humhub-editor.js'];
}
