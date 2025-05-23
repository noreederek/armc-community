<?php


use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\ui\menu\MenuEntry;
use humhub\widgets\Button;

/* @var MenuEntry[] $entries */
?>
<?= Button::defaultType(Icon::get('cog') . Icon::get('dropdownToggle'))
    ->options(['data-toggle' => 'dropdown'])
    ->sm()
    ->loader(false) ?>
<ul class="dropdown-menu pull-right">
    <?php foreach ($entries as $entry) : ?>
        <li>
            <?= $entry->render() ?>
        </li>
    <?php endforeach; ?>
</ul>
