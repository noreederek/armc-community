<?php



Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

return [
    'id' => 'humhub-console',
    'controllerNamespace' => 'humhub\commands',
    'components' => [
        'user' => [
            'class' => \humhub\modules\user\components\User::class,
            'identityClass' => \humhub\modules\user\models\User::class,
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => ['/user/auth/login'],
        ],
        'urlManager' => [
            'class' => \humhub\components\console\UrlManager::class,
            'scriptUrl' => '/index.php',
        ],
        'runtimeCache' => [
            'class' => \yii\caching\DummyCache::class,
        ],
    ],
];
