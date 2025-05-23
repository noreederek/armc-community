<?php


namespace humhub\modules\ui\widgets;

use humhub\components\Widget;

/**
 * Class CounterSet
 *
 * @since 1.3
 * @package humhub\modules\ui\widgets
 */
class CounterSet extends Widget
{
    /**
     * @var CounterSetItem[]
     */
    public $counters = [];


    /**
     * @var string the template to use
     */
    public $template = '@ui/widgets/views/counterSetHeader';


    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render($this->template, ['counters' => $this->counters]);
    }
}
