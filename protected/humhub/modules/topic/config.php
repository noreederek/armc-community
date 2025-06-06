<?php



use humhub\modules\content\widgets\WallEntryControls;
use humhub\modules\topic\Module;
use humhub\modules\user\widgets\AccountSettingsMenu;
use humhub\modules\topic\Events;
use humhub\modules\space\modules\manage\widgets\DefaultMenu;

return [
    'id' => 'topic',
    'class' => Module::class,
    'isCoreModule' => true,
    'events' => [
        ['class' => WallEntryControls::class, 'event' => WallEntryControls::EVENT_INIT, 'callback' => [Events::class, 'onWallEntryControlsInit']],
        ['class' => DefaultMenu::class, 'event' => DefaultMenu::EVENT_INIT, 'callback' => [Events::class, 'onSpaceSettingMenuInit']],
        ['class' => AccountSettingsMenu::class, 'event' => AccountSettingsMenu::EVENT_INIT, 'callback' => [Events::class, 'onProfileSettingMenuInit']],
    ],
];
