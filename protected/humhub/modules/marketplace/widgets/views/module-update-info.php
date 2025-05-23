<?php


use humhub\modules\ui\icon\widgets\Icon;

/* @var string $class */
/* @var string $icon */
/* @var string $info */
/* @var string $link */
?>
<div class="row directory-filters-footer <?= $class ?>">
    <div class="col-md-8 col-xs-12">
        <?= Icon::get($icon, ['htmlOptions' => ['class' => 'filter-footer-icon']]) ?>
        <strong><?= $info ?></strong>
    </div>
    <div class="col-md-4 col-xs-12 text-right">
        <?= $link ?>
    </div>
</div>