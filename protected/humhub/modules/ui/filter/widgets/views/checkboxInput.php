<?php


use humhub\modules\ui\view\components\View;
use yii\helpers\Html;

/* @var $this View */
/* @var $options array */
/* @var $title string */
/* @var $value bool */
/* @var $checked bool */
/* @var $iconInActive bool */
/* @var $iconActive bool */
?>

<?= Html::beginTag('a', $options) ?>
<i class="fa  <?= ($checked) ? $iconActive : $iconInActive ?>"></i> <?= $title ?>
<?= Html::endTag('a') ?>

