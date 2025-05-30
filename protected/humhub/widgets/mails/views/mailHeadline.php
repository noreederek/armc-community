<?php


use humhub\modules\ui\mail\DefaultMailStyle;
use humhub\modules\ui\view\components\View;

/* @var View $this */
/* @var int $level */
/* @var string $style */
/* @var string $text */

switch ($level) {
    case 3:
        $fontSize = '12px';
        $margin = '10';
        $weight = 'bold';
        break;
    case 2:
        $fontSize = '14px';
        $margin = '15';
        $weight = '300';
        break;
    default:
        $fontSize = '18px';
        $margin = '20';
        $weight = '300';
        break;
}
?>
<table border="0" cellspacing="0" cellpadding="0" align="left" >
    <tr>
        <td  style="font-size: <?= $fontSize ?>; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-highlight', '#555') ?> font-weight:<?= $weight ?>; text-align:left">
            <span>
                <a href="#" style="text-decoration: none; color:<?= $this->theme->variable('text-color-highlight', '#555') ?>; font-weight:<?= $weight ?>; <?= $style ?>"><?= $text ?></a>
            </span>
        </td>
    </tr>

    <!--start space height -->
    <tr>
        <td height="<?= $margin ?>"></td>
    </tr>
    <!--end space height -->
</table>
