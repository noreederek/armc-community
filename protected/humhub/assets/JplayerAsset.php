<?php



namespace humhub\assets;

use yii\web\AssetBundle;

/**
 * jquery-At.js
 *
 * @author buddha
 */
class JplayerAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/jplayer/dist';

    /**
     * @inheritdoc
     */
    public $js = [
        'jplayer/jquery.jplayer.js',
        'add-on/jplayer.playlist.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = ['skin/blue.monday/css/jplayer.blue.monday.min.css'];

}
