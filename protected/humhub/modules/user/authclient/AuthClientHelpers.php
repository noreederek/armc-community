<?php



namespace humhub\modules\user\authclient;

use humhub\modules\user\models\User;
use humhub\modules\user\services\AuthClientUserService;
use yii\authclient\ClientInterface;

/**
 * @deprecated since 1.14
 */
class AuthClientHelpers
{
    /**
     * @deprecated since 1.14
     */
    public static function storeAuthClientForUser(ClientInterface $authClient, User $user)
    {
        (new AuthClientUserService($user))->add($authClient);
    }
}
