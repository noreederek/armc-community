<?php



use humhub\modules\admin\Module;
use humhub\modules\dashboard\widgets\Sidebar;
use humhub\modules\admin\Events;
use humhub\commands\CronController;
use humhub\modules\user\components\User;

return [
    'id' => 'admin',
    'class' => Module::class,
    'isCoreModule' => true,
    'events' => [
        [
            'class' => User::class,
            'event' => User::EVENT_BEFORE_SWITCH_IDENTITY,
            'callback' => [
                Events::class,
                'onSwitchUser',
            ],
        ],
        [
            'class' => Sidebar::class,
            'event' => Sidebar::EVENT_INIT,
            'callback' => [
                Events::class,
                'onDashboardSidebarInit',
            ],
        ],
        [
            'class' => CronController::class,
            'event' => CronController::EVENT_ON_DAILY_RUN,
            'callback' => [
                Events::class,
                'onCronDailyRun',
            ],
        ],
    ],
];
