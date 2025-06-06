<?php


use humhub\modules\marketplace\models\forms\GeneralModuleSettingsForm;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;

/* @var GeneralModuleSettingsForm $settings */
?>
<?php ModalDialog::begin(['header' => Yii::t('MarketplaceModule.base', '<strong>General</strong> Settings')]) ?>

<?php $form = ActiveForm::begin() ?>

<div class="modal-body">
    <?= $form->field($settings, 'includeBetaUpdates')->checkbox() ?>
</div>

<div class="modal-footer">
    <?= ModalButton::cancel() ?>
    <?= ModalButton::submitModal() ?>
</div>

<?php ActiveForm::end() ?>

<?php ModalDialog::end() ?>
