<?php



namespace humhub\modules\user\events;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use yii\base\Event;

/**
 * FollowEvent
 *
 * @since 1.2
 * @author Luke
 */
class FollowEvent extends Event
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var ActiveRecord the followed item
     */
    public $target;

}
