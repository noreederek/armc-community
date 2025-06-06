<?php



namespace humhub\modules\user\tests\codeception\unit;

use humhub\libs\BasePermission;
use humhub\modules\admin\permissions\ManageGroups;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\admin\permissions\ManageSettings;
use humhub\modules\admin\permissions\ManageSpaces;
use humhub\modules\admin\permissions\ManageUsers;
use humhub\modules\admin\permissions\SeeAdminInformation;
use humhub\modules\user\components\PermissionManager;

class PermissionManagerMock extends PermissionManager
{
    public $permissions = [
        2 => [
            ManageUsers::class,
            ManageGroups::class,
        ],
        3 => [
            ManageUsers::class,
            ManageGroups::class,
            ManageModules::class,
            ManageSettings::class,
            ManageSpaces::class,
            SeeAdminInformation::class,
        ],
    ];

    protected function verify(BasePermission $permission)
    {
        $subject = $this->getSubject();
        if ($subject) {
            $permissions = $this->permissions[$subject->id];
            return in_array(get_class($permission), $permissions);
        }
        return false;
    }
}
