<?php



/** @noinspection MissedFieldInspection */

require_once __DIR__ . "/Module.php";

return [
    'id' => 'module2',
    'class' => \Some\Name\Space\module2\Module::class,
    'namespace' => "Some\\Name\\Space\\module2",
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
