<?php


/* @var $this View */
/* @var $pickerClass string */
/* @var $pickerOptions string */

/* @var $options array */

use humhub\modules\ui\view\components\View;

?>
<?= call_user_func($pickerClass . '::widget', $pickerOptions) ?>
