<?php

use yii\helpers\Html;
use humhub\assets\AppAsset;
use humhub\widgets\PoweredBy;
use yii\web\View;

/* @var $this View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo Html::encode($this->title); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="language" content="en"/>

    <?= Html::csrfMetaTags() ?>

    <?php $this->head() ?>

    <!-- start: render additional head (css and js files) -->
    <?php echo $this->render('@humhub/views/layouts/head'); ?>
    <!-- end: render additional head -->
</head>
<body>
<?php $this->beginBody() ?>

<div class="container installer" style="margin: 0 auto; max-width: 770px;">
    <div class="logo">
        <a class="animated fadeIn" href="http://www.aramco.com" target="_blank" class="">
            <img src="<?php echo Yii::getAlias("@web-static/resources/installer"); ?>/humhub-logo.png" alt="Logo">
        </a>
    </div>

    <?php echo $content; ?>

    <div class="text text-center powered">
        <?= PoweredBy::widget(); ?>
        <br/>
        <br/>
    </div>
</div>

<div class="clear"></div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
