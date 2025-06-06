<?php


use humhub\modules\user\widgets\AccountMenu;
use humhub\widgets\FooterMenu;

/* @var string $content */
?>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <?= AccountMenu::widget() ?>
        </div>
        <div class="col-md-9">
            <?= $content ?>
            <?= FooterMenu::widget(['location' => FooterMenu::LOCATION_FULL_PAGE]); ?>
        </div>
    </div>
</div>
