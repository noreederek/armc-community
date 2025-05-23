<?php



return [
    'yii\bootstrap\BootstrapPluginAsset' => [
        'depends' => [
            'yii\web\JqueryAsset',
            'yii\bootstrap\BootstrapAsset',
            'humhub\assets\JuiBootstrapBridgeAsset',
        ],
    ],
    'yii\web\JqueryAsset' => [
        'sourcePath' => '@npm/jquery/dist',
    ],
    'yii\jui\JuiAsset' => [
        'sourcePath' => '@npm/jquery-ui/dist',
    ],
];
