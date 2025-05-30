<?php


use humhub\libs\Html;
use humhub\modules\ui\view\components\View;

/* @var $this View */
/* @var $title string */
/* @var $filters array */
/* @var $options array */

?>

<?= Html::beginTag('div', $options) ?>
<strong><?= $title ?></strong>
<ul class="filter-list">

    <?php foreach ($filters as $filter): ?>
        <li>
            <?= call_user_func($filter['class'] . '::widget', $filter) ?>
        </li>
    <?php endforeach; ?>

</ul>
<?= Html::endTag('div') ?>
