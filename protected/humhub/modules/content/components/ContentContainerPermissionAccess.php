<?php



namespace humhub\modules\content\components;

use humhub\components\access\PermissionAccessValidator;

class ContentContainerPermissionAccess extends PermissionAccessValidator
{
    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    protected function verifyPermission($permission, $rule)
    {
        return parent::verifyPermission($permission, $rule) ||
            (($this->contentContainer instanceof ContentContainerActiveRecord) && $this->contentContainer->can($permission, $rule));
    }
}
