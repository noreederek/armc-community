<?php



use humhub\modules\admin\widgets\AuthenticationMenu;
use humhub\modules\ldap\Events;
use humhub\modules\ldap\Module;
use humhub\modules\user\authclient\Collection;
use humhub\components\console\Application;

/** @noinspection MissedFieldInspection */
return [
    'id' => 'ldap',
    'class' => Module::class,
    'isCoreModule' => true,
    'consoleControllerMap' => [
        'ldap' => 'humhub\modules\ldap\commands\LdapController',
    ],
    'events' => [
        [AuthenticationMenu::class, AuthenticationMenu::EVENT_INIT, [Events::class, 'onAuthenticationMenu']],
        [Collection::class, Collection::EVENT_BEFORE_CLIENTS_SET, [Events::class, 'onAuthClientCollectionSet']],
    ],
];
