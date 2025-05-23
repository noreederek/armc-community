<?php


/* @var $data array */
/* @var $filterInput string */
?>

<div class="<?= $data['wrapperClass'] ?>">
    <?php if (isset($data['title'])) : ?>
        <div class="<?= $data['titleClass'] ?>"><?= $data['title'] ?></div>
    <?php endif; ?>
    <?= $filterInput ?>
</div>
