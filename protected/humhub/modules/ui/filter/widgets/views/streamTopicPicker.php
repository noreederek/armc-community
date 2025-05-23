<?php


/* @var $this View */

use humhub\modules\topic\widgets\TopicPicker;
use humhub\modules\ui\view\components\View;

?>

<?= TopicPicker::widget([
    'id' => 'stream-topic-picker',
    'name' => 'stream-topic-picker',
    'addOptions' => false
])
?>
