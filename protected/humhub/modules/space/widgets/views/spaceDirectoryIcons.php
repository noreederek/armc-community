<?php


use humhub\modules\space\models\Space;
use humhub\widgets\Link;
use yii\helpers\Html;

/* @var $space Space */
/* @var $membersCount int */
/* @var $canViewMembers bool */

$text = ' <span>' . $membersCount . '</span>';
$class = 'fa fa-users';
?>
<?php if ($canViewMembers) : ?>
    <?= Link::withAction($text, 'ui.modal.load', $space->createUrl('/space/membership/members-list'))->cssClass($class) ?>
<?php else: ?>
    <?= Html::tag('span', $text, ['class' => $class]) ?>
<?php endif; ?>