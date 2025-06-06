<?php



namespace humhub\modules\live\components;

use humhub\modules\live\driver\BaseDriver;
use Yii;
use yii\base\Component;

/**
 * Live Data Sender
 *
 * @since 1.2
 * @author Luke
 */
class Sender extends Component
{
    /**
     * @var BaseDriver|array|string
     */
    public $driver = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->driver = Yii::createObject($this->driver);
    }

    /**
     * Sends a live event
     *
     * @param LiveEvent $event the live event
     */
    public function send($event)
    {
        return $this->driver->send($event);
    }

}
