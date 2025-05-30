<?php


namespace humhub\modules\ui\menu\widgets;

/**
 * Class LeftNavigation
 *
 * @since 1.4
 * @package humhub\modules\ui\menu\widgets
 */
abstract class LeftNavigation extends Menu
{
    /**
     * @var string the title of the panel
     */
    public $panelTitle;

    /**
     * @inheritdoc
     */
    public $template = '@ui/menu/widgets/views/left-navigation.php';

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return [
            'class' => 'panel panel-default left-navigation',
        ];
    }


}
