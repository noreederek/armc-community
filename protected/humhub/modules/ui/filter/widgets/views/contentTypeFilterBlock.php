<?php


use humhub\libs\Html;
use humhub\modules\content\widgets\ContentTypePicker;
use humhub\modules\ui\view\components\View;

/* @var $this View */
/* @var $title string */
?>

<?= Html::beginTag('div', $options) ?>
<strong><?= $title ?></strong>
<?= ContentTypePicker::widget([
    'id' => 'stream_filter_content_type',
    'name' => 'filter_content_type'
]); ?>
<?= Html::endTag('div') ?>
