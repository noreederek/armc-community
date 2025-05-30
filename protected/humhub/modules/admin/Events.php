<?php



namespace humhub\modules\admin;

use humhub\components\Application;
use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\user\events\UserEvent;
use Yii;
use yii\base\BaseObject;
use yii\base\Event;

/**
 * Admin Module provides the administrative backend for HumHub installations.
 *
 * @since 0.5
 */
class Events extends BaseObject
{
    /**
     * On Init of Dashboard Sidebar, add the approve notification widget
     *
     * @param Event $event the event
     */
    public static function onDashboardSidebarInit($event)
    {
        $event->sender->addWidget(widgets\MaintenanceModeWarning::class, [], ['sortOrder' => 0]);

        if (Yii::$app->user->isGuest) {
            return;
        }

        if (Yii::$app->getModule('user')->settings->get('auth.needApproval')) {
            if (Yii::$app->user->getIdentity()->canApproveUsers()) {
                $event->sender->addWidget(widgets\DashboardApproval::class, [], [
                    'sortOrder' => 99,
                ]);
            }
        }

        $event->sender->addWidget(widgets\IncompleteSetupWarning::class, [], ['sortOrder' => 1]);
    }

    /**
     * Callback on daily cron job run
     *
     * @param Event $event
     */
    public static function onCronDailyRun($event)
    {
        Yii::$app->queue->push(new jobs\CleanupLog());
        Yii::$app->queue->push(new jobs\CleanupPendingRegistrations());
        Yii::$app->queue->push(new jobs\CheckForNewVersion());
    }

    /**
     * @param $event UserEvent
     */
    public static function onSwitchUser($event)
    {
        if (Yii::$app instanceof Application) {
            AdminMenu::reset();
        }
    }
}
