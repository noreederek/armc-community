<?php

use humhub\libs\Html;



?>

<script <?= Html::nonce() ?>>
    $(document).one('humhub:ready', function () {
        if (humhub && humhub.modules.notification && humhub.modules.notification.menu) {
            humhub.modules.notification.menu.updateCount(<?= $count ?>);
        }
    });
</script>
