<?php



/* @var $this View */
/* @var $space Space */
/* @var $url string */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $html string */
/* @var $text string */

/* @var $originator User */

use humhub\modules\content\components\ContentContainerActiveRecord;
use yii\web\View;

?>

---

<?= $content ?>
<?php if (!empty($space)) : ?>
    (<?= Yii::t('ActivityModule.base', 'via') ?> <?= $space->displayName ?>)
<?php endif; ?>

<?php if ($url != '') : ?>
    <?= Yii::t('ActivityModule.base', 'See online:') ?> <?= urldecode($url) ?>
<?php endif; ?>
