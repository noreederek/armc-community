<?php



namespace humhub\modules\live\driver;

use yii\base\BaseObject;
use humhub\modules\live\components\LiveEvent;
use humhub\modules\user\models\User;

/**
 * Base driver for live event storage and distribution
 *
 * @since 1.2
 * @author Luke
 */
abstract class BaseDriver extends BaseObject
{
    /**
     * Sends a live event
     *
     * @param LiveEvent $liveEvent The live event to send
     * @return bool indicates the sent was successful
     */
    abstract public function send(LiveEvent $liveEvent);

    /**
     * Returns the JavaScript Configuration for this driver
     *
     * @return array the JS Configuratoin
     * @see \humhub\widgets\CoreJsConfig
     * @since 1.3
     */
    abstract public function getJsConfig();

    /**
     * This callback will be executed whenever the access rules for a
     * contentcontainer is changed. e.g. user joined a new space as member.
     *
     * @since 1.3
     * @see \humhub\modules\live\Module::getLegitimateContentContainerIds()
     */
    public function onContentContainerLegitimationChanged(User $user, $legitimation = [])
    {

    }

}
