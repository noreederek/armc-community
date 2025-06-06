<?php



namespace humhub\modules\user\authclient\interfaces;

use humhub\modules\user\models\User;

/**
 * PrimaryClient authclient interface
 *
 * It's not possible to have two primary auth clients at the same time.
 * E.g. LDAP and Password
 *
 * @author luke
 */
interface PrimaryClient
{
    /**
     * Returns the user model of this auth client
     *
     * @return User
     * @since 1.2.2
     */
    public function getUser();
}
