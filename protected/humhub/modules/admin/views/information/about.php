<?php

use humhub\modules\marketplace\widgets\AboutVersion;
use yii\helpers\Html;

?>


<?= AboutVersion::widget(); ?>
<br/>

<?php if ($isNewVersionAvailable) : ?>
    <div class="alert alert-danger">
        <p>
            <strong><?= Yii::t('AdminModule.information', 'There is a new update available! (Latest version: %version%)', ['%version%' => $latestVersion]); ?></strong><br>
            <?= Html::a("https://www.aramco.com", "https://www.aramco.com"); ?>
        </p>
    </div>
<?php elseif ($isUpToDate): ?>
    <div class="alert alert-info">
        <p>
            <strong><?= Yii::t('AdminModule.information', 'This CommunityHub installation is up to date!'); ?></strong><br/>
            <?= Html::a("https://www.aramco.com", "https://www.aramco.com"); ?>
        </p>
    </div>
<?php endif; ?>

<br>

<?php if (YII_DEBUG) : ?>
    <p class="alert alert-danger">
        <strong><?= Yii::t('AdminModule.information', 'HumHub is currently in debug mode. Disable it when running on production!'); ?></strong><br>
        <?= Yii::t('AdminModule.information', 'See installation manual for more details.'); ?>
    </p>
<?php endif; ?>

<hr>
© <?= date("Y") ?> HumHub GmbH & Co. KG
&middot;
<?= Html::a(Yii::t('AdminModule.information', 'Licences'), "https://www.aramco.com/licences", ['target' => '_blank']); ?>
