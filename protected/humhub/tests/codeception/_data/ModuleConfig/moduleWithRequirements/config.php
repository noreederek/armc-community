<?php



/** @noinspection MissedFieldInspection */

require_once __DIR__ . "/Module.php";

return [
    'id' => 'moduleWithRequirements',
    'class' => \Some\Name\Space\moduleWithRequirements\Module::class,
    'namespace' => "Some\\Name\\Space\\moduleWithRequirements",
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
