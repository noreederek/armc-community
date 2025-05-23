<?php


namespace humhub\modules\ui\menu\widgets;

/**
 * SubTabMenu
 *
 * @since 1.4
 * @package humhub\modules\ui\menu\widgets
 */
abstract class SubTabMenu extends TabMenu
{
    /**
     * @var string the title of the panel
     */
    public $panelTitle;

    /**
     * @inheritdoc
     */
    public $template = '@ui/menu/widgets/views/sub-tab-menu.php';

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return [
            'class' => 'nav nav-tabs tab-sub-menu',
        ];
    }

}
