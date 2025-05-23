<?php



namespace humhub\modules\space;

use humhub\modules\user\models\User;
use yii\base\Event;

/**
 * MemberEvent
 *
 * @since 1.2
 * @author Luke
 */
class MemberEvent extends Event
{
    /**
     * @var models\Space
     */
    public $space;

    /**
     * @var User
     */
    public $user;

}
