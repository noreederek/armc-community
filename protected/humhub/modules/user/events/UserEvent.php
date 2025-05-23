<?php



namespace humhub\modules\user\events;

use humhub\components\Event;
use humhub\modules\user\models\User;

/**
 * UserEvent
 *
 * @since 1.2
 * @author Luke
 */
class UserEvent extends Event
{
    /**
     * @var User the user
     */
    public $user;
}
