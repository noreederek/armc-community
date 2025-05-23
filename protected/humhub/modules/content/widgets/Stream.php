<?php



namespace humhub\modules\content\widgets;

use humhub\components\Widget;
use humhub\modules\stream\widgets\StreamViewer;

/**
 * Stream Wrapper for older theme versions
 *
 * @deprecated since version 1.2
 * @author Luke
 */
class Stream extends Widget
{
    public static function widget($config = [])
    {
        $config['class'] = StreamViewer::class;
        return parent::widget($config);
    }

}
