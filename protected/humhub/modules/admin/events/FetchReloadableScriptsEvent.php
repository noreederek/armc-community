<?php



namespace humhub\modules\admin\events;

use yii\base\Event;

/**
 * This event is used when an object is added to search index
 *
 * @author luke
 * @since 0.21
 */
class FetchReloadableScriptsEvent extends Event
{
    /**
     * @var array Reference to the currently added search attributes
     */
    public $urls = [];

    public function addScriptUrl($urls)
    {
        $this->urls[] = $urls;
    }

}
