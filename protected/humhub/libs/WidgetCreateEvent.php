<?php



namespace humhub\libs;

use yii\base\Event;

/**
 * WidgetCreateEvent is raised before creating a widget
 *
 * @see \humhub\components\Widget
 * @author luke
 */
class WidgetCreateEvent extends Event
{
    /**
     * @var array Reference to the config of widget create
     */
    public $config;

    /**
     * @inheritdoc
     */
    public function __construct(&$attributes)
    {
        $this->config = &$attributes;
        $this->init();
    }
}
