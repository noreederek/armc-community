<?php



namespace humhub\modules\user\components;

use humhub\components\Controller;
use Yii;
use humhub\components\access\ControllerAccess;

/**
 * BaseAccountController is the base controller for user account (settings) pages
 *
 * @since 1.1
 * @author luke
 */
class BaseAccountController extends Controller
{
    /**
     * @inheritdoc
     */
    public $subLayout = "@humhub/modules/user/views/account/_layout";

    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [
            [ControllerAccess::RULE_LOGGED_IN_ONLY],
        ];
    }

    /**
     * @var \humhub\modules\user\models\User the user
     */
    public $user;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->appendPageTitle(Yii::t('UserModule.base', 'My Account'));
        parent::init();
    }

    /**
     * Returns the current user of this account
     *
     * @return \humhub\modules\user\models\User
     */
    public function getUser()
    {
        if ($this->user === null) {
            $this->user = Yii::$app->user->getIdentity();
        }

        return $this->user;
    }

}
