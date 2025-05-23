<?php



namespace humhub\modules\admin\widgets;

use humhub\helpers\ControllerHelper;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\widgets\SubTabMenu;
use Yii;

/**
 * Group Administration Menu
 */
class GroupMenu extends SubTabMenu
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->addEntry(new MenuLink([
            'label' => Yii::t('AdminModule.user', 'Overview'),
            'url' => ['/admin/group/index'],
            'sortOrder' => 100,
            'isActive' => ControllerHelper::isActivePath('admin', 'group', 'index'),
        ]));
        parent::init();
    }

}
