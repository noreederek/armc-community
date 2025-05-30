<?php


use humhub\modules\ui\view\helpers\ThemeHelper;

/* @var $content string */
?>
<div
    class="<?php if (ThemeHelper::isFluid()): ?>container-fluid<?php else: ?>container<?php endif; ?> container-cards container-modules">
    <div class="row">
        <div class="col-lg-12">
            <?= $content; ?>
        </div>
    </div>
</div>
