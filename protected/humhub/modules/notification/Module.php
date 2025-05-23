<?php



namespace humhub\modules\notification;

/**
 * Notification Module
 */
class Module extends \humhub\components\Module
{
    /**
     * @var int Delete read notifications after 2 months(by default)
     */
    public $deleteSeenNotificationsMonths = 2;

    /**
     * @var int Delete unread notifications after 3 months(by default)
     */
    public $deleteUnseenNotificationsMonths = 3;
}
