<?php



namespace humhub\modules\user\components;

use humhub\modules\user\models\User as UserModel;
use Yii;
use yii\base\Behavior;

/**
 * Impersonator behavior provides actions to impersonate users by admin
 *
 * @since 1.10
 * @property-read UserModel|null $impersonator Admin user who impersonate the current User
 * @property bool $isImpersonated Whether this user is impersonated by admin currently.
 * @author luke
 */
class Impersonator extends Behavior
{
    /**
     * @var User
     */
    public $owner;

    protected bool $impersonated = false;

    /**
     * Determines if the current user can impersonate the given user.
     *
     * @param UserModel $user
     * @return bool
     */
    public function canImpersonate(UserModel $user): bool
    {
        if ($this->owner->isGuest) {
            return false;
        }

        return $this->owner->getIdentity()->canImpersonate($user);
    }

    /**
     * @return bool True if this user is impersonated by admin currently
     */
    public function getIsImpersonated(): bool
    {
        return $this->impersonated || $this->getImpersonator() !== null;
    }

    public function setIsImpersonated($impersonated)
    {
        $this->impersonated = $impersonated;
    }

    /**
     * Get admin user who impersonate current user
     *
     * @return UserModel|null
     */
    public function getImpersonator(): ?UserModel
    {
        if ($this->owner->isGuest) {
            return null;
        }

        $impersonator = Yii::$app->session->get('impersonator');

        if (!($impersonator instanceof UserModel)) {
            return null;
        }

        if (!$impersonator->canImpersonate($this->owner->getIdentity())) {
            return null;
        }

        return $impersonator;
    }

    /**
     * Impersonate the given user with storing current user in session in order to sing in back
     *
     * @param UserModel $user
     * @return bool
     */
    public function impersonate(UserModel $user): bool
    {
        if (!$this->canImpersonate($user)) {
            return false;
        }

        Yii::$app->session->set('impersonator', $this->owner->getIdentity());
        $this->impersonated = true;
        $this->owner->switchIdentity($user);

        return true;
    }

    /**
     * Restore impersonator user from session
     *
     * @return bool
     */
    public function restoreImpersonator(): bool
    {
        if (!($impersonator = $this->getImpersonator())) {
            return false;
        }

        Yii::$app->session->remove('impersonator');
        $this->impersonated = false;
        $this->owner->switchIdentity($impersonator);

        return true;
    }
}
