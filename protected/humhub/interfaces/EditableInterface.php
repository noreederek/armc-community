<?php



namespace humhub\interfaces;

use humhub\modules\user\models\User;

/**
 * Editable Interface
 * @since 1.16
 */
interface EditableInterface
{
    /**
     * Checks if the given user can edit/create this element.
     *
     * @param User|int|string|null $user user instance or user id
     * @return bool
     */
    public function canEdit($user = null): bool;

}
