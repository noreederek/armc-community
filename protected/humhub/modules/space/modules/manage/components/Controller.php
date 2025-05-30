<?php



namespace humhub\modules\space\modules\manage\components;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\space\models\Space;

/**
 * Default Space Manage Controller
 *
 * @author luke
 */
class Controller extends ContentContainerController
{
    protected function getAccessRules()
    {
        return [
            ['login'],
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Space::USERGROUP_ADMIN]],
        ];
    }
}
