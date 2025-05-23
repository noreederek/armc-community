<?php



$config = require dirname(__DIR__) . "/module1/config.php";
$config['id'] = basename(__DIR__);
$config['class'] = \yii\base\Module::class;

return $config;
