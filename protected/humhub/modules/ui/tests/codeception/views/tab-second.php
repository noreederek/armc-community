<?php


use humhub\modules\ui\form\widgets\ActiveForm;
use tests\codeception\models\TestTabbedFormModel;

/* @var $form ActiveForm */
/* @var $tabbedForm TestTabbedFormModel */
?>

<?= $form->field($tabbedForm, 'countryId')->textInput() ?>
<?= $form->field($tabbedForm, 'stateId')->textInput() ?>
<?= $form->field($tabbedForm, 'cityId')->textInput() ?>
