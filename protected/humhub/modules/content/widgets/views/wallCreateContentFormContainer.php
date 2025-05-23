<?php


use humhub\modules\content\assets\ContentFormAsset;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\widgets\WallCreateContentMenu;

/* @var $contentContainer ContentContainerActiveRecord */
/* @var $formClass string */

ContentFormAsset::register($this);
?>

<?= WallCreateContentMenu::widget(['contentContainer' => $contentContainer]) ?>

<?php if ($formClass) : ?>
    <?= $formClass::widget(['contentContainer' => $contentContainer]) ?>
<?php endif; ?>
