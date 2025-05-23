<?php


use humhub\modules\ui\form\widgets\ActiveForm;
use tests\codeception\models\TestTabbedFormModel;

/* @var $form ActiveForm */
/* @var $tabbedForm TestTabbedFormModel */
?>

<?= $form->field($tabbedForm, 'firstname')->textInput() ?>
<?= $form->field($tabbedForm, 'lastname')->textInput() ?>
<?= $form->field($tabbedForm, 'email')->textInput() ?>
