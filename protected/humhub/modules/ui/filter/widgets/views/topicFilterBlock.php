<?php


use humhub\libs\Html;
use humhub\modules\topic\widgets\TopicPicker;
use humhub\modules\ui\view\components\View;

/* @var $this View */
/* @var $title string */

?>

<?= Html::beginTag('div', $options) ?>
<strong><?= $title ?></strong>
<?= TopicPicker::widget([
    'id' => 'stream_filter_topic',
    'name' => 'filter_topic'
]); ?>
<?= Html::endTag('div') ?>
