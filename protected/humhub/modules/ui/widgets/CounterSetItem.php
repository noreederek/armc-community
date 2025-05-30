<?php


namespace humhub\modules\ui\widgets;

use Yii;
use yii\base\BaseObject;

/**
 * Class CounterSetItem
 *
 * @since 1.3
 * @see CounterSet
 * @package humhub\modules\ui\widgets
 */
class CounterSetItem extends BaseObject
{
    /**
     * @var int the numberic value of this counter item
     */
    public $value;

    /**
     * @var string the label of this counter item. The output will not encoded!
     */
    public $label;

    /**
     * @var array the URL
     */
    public $url;

    /**
     * @var array the Link options
     */
    public $linkOptions = [];


    /**
     * @return bool
     */
    public function hasLink()
    {
        return (!empty($this->url));
    }

    /**
     * @return string
     */
    public function getShortValue()
    {
        return Yii::$app->formatter->asShortInteger($this->value);
    }

}
