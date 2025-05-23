<?php



/** @noinspection MissedFieldInspection */

require_once __DIR__ . "/Module.php";

return [
    'id' => 'moduleWithMigration',
    'class' => \Some\Name\Space\moduleWithMigration\Module::class,
    'namespace' => "Some\\Name\\Space\\moduleWithMigration",
    'events' => [
        [
            'class' => \humhub\tests\codeception\unit\components\ModuleManagerTest::class,
            'event' => 'valid',
            'callback' => [
                \humhub\tests\codeception\unit\components\ModuleManagerTest::class,
                'handleEvent',
            ],
        ],
    ],
];
