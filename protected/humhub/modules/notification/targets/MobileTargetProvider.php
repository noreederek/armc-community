<?php



namespace humhub\modules\notification\targets;

use humhub\modules\notification\components\BaseNotification;
use humhub\modules\user\models\User;

interface MobileTargetProvider
{
    /**
     * @param BaseNotification $notification
     * @param User $user
     * @return bool
     */
    public function handle(BaseNotification $notification, User $user);

    /**
     * @param User|null $user
     * @return bool
     */
    public function isActive(User $user = null);
}
