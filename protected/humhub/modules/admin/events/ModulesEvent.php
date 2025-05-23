<?php



namespace humhub\modules\admin\events;

use humhub\components\Module;
use yii\base\Event;

/**
 * This event is used when modules is listed and filtered
 *
 * @author luke
 * @since 1.11
 */
class ModulesEvent extends Event
{
    /**
     * @var Module[]|array
     */
    public $modules;
}
