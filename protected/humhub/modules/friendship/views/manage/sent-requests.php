<?php

use humhub\modules\friendship\widgets\ManageMenu;
use yii\bootstrap\Html;
use humhub\widgets\GridView;

?>
<div class="panel-heading">
    <?php echo Yii::t('FriendshipModule.base', '<strong>Sent</strong> friend requests'); ?>
</div>


<?php echo ManageMenu::widget(['user' => $user]); ?>

<div class="panel-body">
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'username',
            'profile.firstname',
            'profile.lastname',
            [
                'header' => Yii::t('base', 'Actions'),
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function () {
                        return;
                    },
                    'view' => function () {
                        return;
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(Yii::t('base', 'Cancel'), ['/friendship/request/delete', 'userId' => $model->id], ['class' => 'btn btn-danger btn-sm', 'data-method' => 'POST']);
                    },
                ],
            ]],
    ]);
    ?>

</div>



